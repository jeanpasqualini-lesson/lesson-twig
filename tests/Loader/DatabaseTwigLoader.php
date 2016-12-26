<?php
/**
 * Created by PhpStorm.
 * User: aurore
 * Date: 04/01/2017
 * Time: 17:45
 */

namespace Loader;

class DatabaseTwigLoader implements \Twig_LoaderInterface
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSource($name)
    {
        if(false === $source = $this->getValue('source', $name))
        {
            throw new \Twig_Error_Loader('the template not laoded from database');
        }

        return $source;
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function isFresh($name, $time)
    {
        return false;
    }

    /**
     * @param $column
     * @param $name
     */
    protected function getValue($column, $name)
    {
        $st = $this->pdo->prepare('SELECT '.$column. ' FROM templates WHERE name = :name');
        $st->execute(array(':name' => (string) $name));

        return $st->fetchColumn();
    }
}