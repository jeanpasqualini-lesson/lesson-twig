<?php
namespace tests;

use Escaper\LeetEscaper;
use Twig_Environment;

class LeetEscaperTest extends \PHPUnit_Framework_TestCase
{
    /** @var Twig_Environment */
    protected $twig;

    public function setUp()
    {
        $this->twig = new \Twig_Environment(
            new \Twig_Loader_Array(array(
                'test.twig' => '{{ "the geek world" | lower }}'
            )),
            array(
                'autoescape' => 'leet'
            )
        );
        $this->twig->getExtension('core')->setEscaper('leet', array(LeetEscaper::class, 'escape'));
    }

    public function testEscape()
    {
        $this->assertEquals('+h3 g33k w0r1d', $this->twig->render('test.twig'));
    }
}