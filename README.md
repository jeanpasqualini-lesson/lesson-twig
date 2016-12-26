##### Documentation

http://symfony.com/doc/current/reference/twig_reference.html

##### Quality Code
[![CircleCI](https://circleci.com/gh/jeanpasqualini-lesson/lesson-twig.svg?style=svg)](https://circleci.com/gh/jeanpasqualini-lesson/lesson-twig)
https://circleci.com/gh/jeanpasqualini-lesson/lesson-twig

##### Test

docker run -v $(pwd):/app --rm phpunit/phpunit -c phpunit.xml.dist

Symfony standard edition

List extensions class

```
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_67    Symfony\Bridge\Twig\Extension\LogoutUrlExtension                                            
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_68    Symfony\Bridge\Twig\Extension\SecurityExtension                                             
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_69    Symfony\Bridge\Twig\Extension\ProfilerExtension                                             
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_70    Symfony\Bridge\Twig\Extension\TranslationExtension                                          
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_71    Symfony\Bridge\Twig\Extension\AssetExtension                                                
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_72    Symfony\Bridge\Twig\Extension\CodeExtension                                                 
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_73    Symfony\Bridge\Twig\Extension\RoutingExtension                                              
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_74    Symfony\Bridge\Twig\Extension\YamlExtension                                                 
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_75    Symfony\Bridge\Twig\Extension\StopwatchExtension                                            
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_76    Symfony\Bridge\Twig\Extension\ExpressionExtension                                           
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_77    Symfony\Bridge\Twig\Extension\HttpKernelExtension                                           
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_78    Symfony\Bridge\Twig\Extension\HttpFoundationExtension                                       
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_79    Symfony\Bridge\Twig\Extension\FormExtension                                                 
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_82    Twig_Extension_Debug                                                                        
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_83    Doctrine\Bundle\DoctrineBundle\Twig\DoctrineExtension                                       
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_84    Symfony\Bridge\Twig\Extension\DumpExtension                                                 
  b5245a1f344d554a67f1c74265a98e053359cc65910d31edcdd31d93e4732f8c_85    Symfony\Bundle\WebProfilerBundle\Twig\WebProfilerExtension
```

List functions, filters, tests

```
Functions
---------

 TWIG-BASE
    * source(name, ignoreMissing = false)
    * max(args)
    * min(args)
    * random(values = null)
    * range(low, high, step)
    * cycle(values, position)
    * date(date = null, timezone = null)
    * dump()
    * include(template, variables = [], withContext = true, ignoreMissing = false, sandboxed = false)
    * constant(constant, object = null)
 TWIG-ROUTING
    * path(name, parameters = [], relative = false)
    * url(name, parameters = [], schemeRelative = false)
 TWIG-HTTP-FOUNDATION
    * absolute_url(path)
    * relative_path(path)
 TWIG-FORM
    * form(unknown?)
    * form_end(unknown?)
    * form_errors(unknown?)
    * form_label(unknown?)
    * form_rest(unknown?)
    * form_row(unknown?)
    * form_start(unknown?)
    * form_widget(unknown?)
 TWIG-SECURITY
    * is_granted(role, object = null, field = null)
    * logout_path(key = null)
    * logout_url(key = null)
 TWIG-ASSET
    * asset(path, packageName = null)
    * asset_version(path, packageName = null)
 TWIG-HTTP-KERNEL
    * controller(controller, attributes = [], query = [])
    * render(uri, options = [])
    * render_*(strategy, uri, options = [])
 TWIG-EXPESSION
    * expression(expression)
 TWIG-OTHER
    * csrf_token(tokenId)
    * profiler_dump(value)

Filters
-------

 TWIG-BASE
    * abs
    * batch(size, fill = null)
    * capitalize
    * convert_encoding(to, from)
    * date(format = null, timezone = null)
    * date_modify(modifier)
    * default(default = "")
    * humanize
    * join(glue = "")
    * json_encode(options = 0)
    * keys
    * last
    * length
    * lower
    * merge(arr2)
    * nl2br(is_xhtml)
    * number_format(decimal = null, decimalPoint = null, thousandSep = null)
    * raw
    * replace(from, to = null)
    * reverse(preserveKeys = false)
    * round(precision = 0, method = "common")
    * slice(start, length = null, preserveKeys = false)
    * sort
    * split(delimiter, limit = null)
    * striptags(allowable_tags)
    * title
    * trim(character_mask)
    * upper
    * url_encode
    * e(strategy = "html", charset = null, autoescape = false)
    * escape(strategy = "html", charset = null, autoescape = false)
    * first
 TWIG-DOCTRINE
    * doctrine_minify_query
    * doctrine_pretty_query(highlightOnly = false)
    * doctrine_replace_query_parameters(parameters)
 TWIG-OTHER
    * format(args)
    * format_args
    * format_args_as_text
    * format_file(line, text = null)
    * format_file_from_text
    * abbr_class
    * abbr_method
    * file_excerpt(line)
    * file_link(line)
 TWIG-YAML   
    * yaml_dump(inline = 0, dumpObjects = false)
    * yaml_encode(inline = 0, dumpObjects = false)
 TWIG-TRANSLATION
    * trans(arguments = [], domain = null, locale = null)
    * transchoice(count, arguments = [], domain = null, locale = null)

Tests
-----

 TWIG-BASE
    * constant
    * defined
    * divisible by
    * divisibleby
    * empty
    * even
    * iterable
    * none
    * null
    * odd
    * same as
    * sameas
 TWIG-FORM
    * selectedchoice

Globals
-------

 TWIG-FRAMEWORK
    * app = object(Symfony\Bridge\Twig\AppVariable)
```