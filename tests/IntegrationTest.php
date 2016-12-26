<?php
namespace tests;

use CsrfTokenStorage\ArrayCsrfTokenStorage;
use CsrfTokenStorage\ArraySecurityTokenStorage;
use Doctrine\Bundle\DoctrineBundle\Twig\DoctrineExtension;
use Extension\AppExtension;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\CodeExtension;
use Symfony\Bridge\Twig\Extension\ExpressionExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\HttpFoundationExtension;
use Symfony\Bridge\Twig\Extension\HttpKernelExtension;
use Symfony\Bridge\Twig\Extension\LogoutUrlExtension;
use Symfony\Bridge\Twig\Extension\ProfilerExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Extension\YamlExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Fragment\EsiFragmentRenderer;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\HttpKernel\Fragment\HIncludeFragmentRenderer;
use Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use PHPUnit_Framework_MockObject_MockObject;
use Webmozart\Assert\Assert;

class IntegrationTest extends \Twig_Test_IntegrationTestCase
{
    /** @var RequestContext */
    protected $requestContext;

    /** @var RequestStack */
    protected $requestStack;

    /** @var $url */
    protected $url;

    protected function setRequestUrl($url)
    {
        $this->url = $url;
    }

    /**
     *
     */
    protected function factoryFormBuilder()
    {
        $formFactoryBuilder = new FormFactoryBuilder();
        return $formFactoryBuilder->getFormFactory()->createBuilder();
    }

    public function factoryRequestContext()
    {
        if (null === $this->requestContext)
        {
            $this->requestContext = new RequestContext();
            $this->setRequestUrl('https://mami.com/papi');
        }

        return $this->requestContext->fromRequest(Request::create($this->url));
    }

    protected function factoryUrlGenerator()
    {
        $requestContent = $this->factoryRequestContext();

        $routeCollection = new RouteCollection();
        $routeCollection->add('home_https', new Route(
            $path           = '/home',
            $defaults       = array(),
            $requirements   = array(),
            $options        = array(),
            $host           = '',
            $schemes        = 'https',
            $methods        = array(),
            $condition      = ''
        ));
        $routeCollection->add('home', new Route(
            $path           = '/home',
            $defaults       = array(),
            $requirements   = array(),
            $options        = array(),
            $host           = '',
            $schemes        = array(),
            $methods        = array(),
            $condition      = ''
        ));

        return new UrlGenerator($routeCollection, $requestContent);
    }

    public function factoryRequestStack()
    {
        if (null === $this->requestStack)
        {
            $this->requestStack = new RequestStack();
            $this->setRequestUrl('https://mami.com/papi');
        }

        $request = Request::create($this->url);
        $request->headers->set('Surrogate-Capability', 'ESI/1.0');

        $this->requestStack->pop();
        $this->requestStack->push($request);

        return $this->requestStack;
    }

    protected function factoryTranslator()
    {
        $translator = new Translator('fr_FR');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', array(
            'Hello world' => 'Bonjour',
            'apple' => '%count% pomme|%count% pommes',
            'fr_FR' => 'ok'
        ), 'fr_FR');
        $translator->addResource('array', array(
            'fr' => 'ok'
        ), 'fr');
        $translator->addResource('array', array(
            'en_US' => 'ok'
        ), 'en_US');
        $translator->addResource('array', array(
            'en' => 'ok'
        ), 'en');

        $translator->setLocale('fr_FR');
        $translator->setFallbackLocales(array('en', 'en_US'));

        return $translator;
    }

    protected function factoryRenderers()
    {
        $renderers = array();

        /** @var HttpKernelInterface|PHPUnit_Framework_MockObject_MockObject $httpKernel */
        $httpKernel = $this->getMockBuilder(HttpKernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpKernel
            ->expects($this->any())
            ->method('handle')
            ->will($this->returnValue(new Response('inline rendered')));

        $renderers['inline'] = new InlineFragmentRenderer($httpKernel);
        $renderers['hinclude'] = new HIncludeFragmentRenderer();
        $renderers['esi'] = new EsiFragmentRenderer(new Esi(), $renderers['inline'], new UriSigner('secret'));

        return $renderers;
    }

    protected function factoryLogoutUrlGenerator()
    {
        $tokenStorage = new ArraySecurityTokenStorage();
        $token = $this->getMockBuilder(RememberMeToken::class)
            ->disableOriginalConstructor()
            ->getMock();
        $token->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue('super_secure_route'));
        $tokenStorage->setToken($token);

        $logoutUrlGenerator = new LogoutUrlGenerator($this->factoryRequestStack(), $this->factoryUrlGenerator(), $tokenStorage);
        $logoutUrlGenerator->registerListener('super_secure_url', '/superlogout', '', '');
        $logoutUrlGenerator->registerListener('super_secure_route', 'home', '', '');
        $logoutUrlGenerator->registerListener('super_secure_route_https', 'home_https', '', '');

        return $logoutUrlGenerator;
    }

    protected function factoryAuthorizationChecker()
    {
        $authorizationChecker = $this->getMockBuilder(AuthorizationCheckerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $authorizationChecker
            ->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(true));

        return $authorizationChecker;
    }

    protected function factoryPackages()
    {
        $versionStrategy = new StaticVersionStrategy('v1');

        $defaultPackage = new Package($versionStrategy);

        $namedPackages = array(
            'img' => new UrlPackage('http://img.example.com/', $versionStrategy),
            'doc' => new PathPackage('/somewhere/deep/for/documents', $versionStrategy),
        );

        return new Packages($defaultPackage, $namedPackages);
    }

    public function getExtensions()
    {
        $policy = new \Twig_Sandbox_SecurityPolicy(array(), array(), array(), array(), array());

        return array(
            // Twig Base
            new \Twig_Extension_Debug(),
            new \Twig_Extension_Sandbox($policy, false),
            new \Twig_Extension_StringLoader(),

            // Twig App
            new AppExtension(),

            // Twig Extra
            new \Twig_Extensions_Extension_Array(),

            // Twig Symfonys
            new FormExtension(new TwigRenderer(new TwigRendererEngine(array('form_div_layout.html.twig')), new CsrfTokenManager(
                $generator = null,
                $storage = new ArrayCsrfTokenStorage()
            ))),

            // Twig Expression
            new ExpressionExtension(),

            // Twig Routing
            new RoutingExtension($this->factoryUrlGenerator()),

            // Twig Http Foundation
            new HttpFoundationExtension($this->factoryRequestStack(), $this->factoryRequestContext()),

            // Twig Profiler
            new ProfilerExtension(new \Twig_Profiler_Profile(), new Stopwatch()),

            // Twig Web Profiler
            new WebProfilerExtension(),

            // Twig Translation
            new TranslationExtension($this->factoryTranslator()),

            // Twig Yaml
            new YamlExtension(),

            // Twig Code
            new CodeExtension('txmt://open?url=file://%f&line=%l', '/root', 'UTF-8'),

            // Twig Doctrine
            new DoctrineExtension(),

            // Twig Http Kernel
            new HttpKernelExtension(new FragmentHandler($this->factoryRequestStack(), $this->factoryRenderers())),

            // Twig Security
            new SecurityExtension($this->factoryAuthorizationChecker()),

            // Twig Logout Url
            new LogoutUrlExtension($this->factoryLogoutUrlGenerator()),

            // Twig Asset
            new AssetExtension($this->factoryPackages()),
        );
    }

    public function getFixturesDir()
    {
        return dirname(__FILE__).'/Fixtures/';
    }
}
