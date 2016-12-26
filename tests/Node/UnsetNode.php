<?php
namespace Node;

class UnsetNode extends \Twig_Node
{
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('$context[\''.$this->getAttribute('name').'\'] = \'__removed__\';')
            ->raw(PHP_EOL)
            ->write('$context = array_filter($context, function($var) { return $var !== \'__removed__\'; });')
            ->raw(PHP_EOL)
        ;
    }
}