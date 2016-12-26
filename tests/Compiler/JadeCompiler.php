<?php
/**
 * Created by PhpStorm.
 * User: aurore
 * Date: 04/01/2017
 * Time: 13:36
 */

namespace Compiler;

/**
 * Class JadeCompiler
 * @package Compiler
 */
class JadeCompiler
{
    /** @var \Twig_Environment */
    protected $environment;

    /**
     * JadeCompiler constructor.
     * @param \Twig_Environment $environment
     */
    public function __construct(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * When two tag openned with same number space
     * Auto close previous tag with same number space
     *
     * @param $sourceLines
     * @param $opened
     * @param null $current
     */
    protected function autoCloseTag(&$sourceLines, &$opened, $current = null)
    {
        if (null === $current) {
            return;
        }

        foreach ($opened as $openedKey => $openedItem) {

            if ($current['line'] != $openedItem['line']) {
                if ($current['space_before_tag'] == $openedItem['space_before_tag']) {
                    $sourceLines[$current['line']] =
                        str_repeat(' ', $current['space_before_tag']).'</'.$openedItem['tag'].'>'.PHP_EOL.$sourceLines[$current['line']];

                    $opened[$openedKey]['closed'] = true;
                }
            }
        }
    }

    /**
     * Close all tags not closed at end of content
     *
     * @param $sourceLines
     * @param $opened
     */
    protected function closeAllTags(&$sourceLines, &$opened)
    {
        foreach ($opened as $openedKey => $openedItem) {
            if (!$openedItem['closed']) {
                $sourceLines[] = str_repeat(' ', $openedItem['space_before_tag']).'</'.$openedItem['tag'].'>';

                $opened[$openedKey]['closed'] = true;
            }
        }
    }

    /**
     * @param $jadeSource
     * @param array $context
     *
     * @return string
     * @exception Twig_Error_Runtime
     */
    public function compile($jadeSource, array $context)
    {
        $compilationPass = array();

        // Compilation pass : add OPEN_TAG
        $compilationPass[] = function($jadeSource) {
            $htmlTags = array(
                'html'      => '<html>',
                'head'      => '<head>',
                'title'     => '<title>',
                'body'      => '<body>',
                'h1'        => '<h1>',
            );

            return str_replace(array_keys($htmlTags), array_values($htmlTags), $jadeSource);
        };

        // Compilation pass : add CLOSE_TAG INLINE
        $compilationPass[] = function($jadeSource) {
            return preg_replace('/<([a-z0-9]+)> ([ a-z0-9#{}]+)/i', '<$1>$2</$1>', $jadeSource);
        };

        // Compilation pass : add CLOSE_TAG NEWLINE
        $compilationPass[] = function($jadeSource) {
            $jadeSourceLines = explode(PHP_EOL, $jadeSource);

            $opened = array();

            foreach ($jadeSourceLines as $lineNumber => $lineContent) {

                $current = null;

                // When tag is open and is not closed inline
                // Declare tag openned for process auto close tag
                if (preg_match('/(?P<spaces>[ ]*)<(?P<tag>[a-z0-9]+)>/i', $lineContent, $extract))
                {
                    if (false === strpos($lineContent, '</'.$extract['tag'].'>')) {
                        array_unshift($opened, $current = array(
                            'line'              => $lineNumber,
                            'space_before_tag'  => strlen($extract['spaces']),
                            'tag'               => $extract['tag'],
                            'closed' => false,
                        ));
                    }
                }

                $this->autoCloseTag($jadeSourceLines, $opened, $current);

                // Auto close all tag not closed by auto close at end of content
                if (!isset($jadeSourceLines[$lineNumber + 1])) {
                    $this->closeAllTags($jadeSourceLines, $opened);
                }
            }

            return implode(PHP_EOL, $jadeSourceLines);
        };

        // Compilation pass : parse expression variable
        $compilationPass[] = function($jadeSource) use ($context) {
            return preg_replace_callback(
                '/#{(?P<variableName>[a-z]+)}/i',
                function(array $extract) use ($context) {
                    $variableExist = isset($context[$extract['variableName']]);

                    if (!$variableExist) {
                        if (!$this->environment->isStrictVariables())
                        {
                            return null;
                        } else {
                            throw new \Twig_Error_Runtime(
                                'the variable '.$extract['variableName'].' not exist'
                            );
                        }
                    }

                    return $context[$extract['variableName']];
                },
                $jadeSource
            );
        };

        // Compilation processor
        $jadeCompiled = $jadeSource;
        foreach($compilationPass as $compilationPassItem)
        {
            $jadeCompiled = $compilationPassItem($jadeCompiled);
        }

        return $jadeCompiled;
    }
}