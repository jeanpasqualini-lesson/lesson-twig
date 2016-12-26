<?php

abstract class AppTemplate extends \Twig_Template
{
    public function render(array $context)
    {
        return 'content overriden';
    }
}