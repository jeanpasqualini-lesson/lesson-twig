<?php
/**
 * Created by PhpStorm.
 * User: aurore
 * Date: 05/01/2017
 * Time: 14:51
 */

namespace Escaper;


class LeetEscaper
{
    private $english = array("a", "e", "s", "S", "A", "o", "O", "t", "l", "ph", "y", "H", "W", "M", "D", "V", "x");
    private $leet = array("4", "3", "z", "Z", "4", "0", "0", "+", "1", "f", "j", "|-|", "\\/\\/", "|\\/|", "|)", "\\/", "><");

    /**
     * @param $string
     * @return string
     */
    protected function encode($string)
    {
        $result = '';
        for ($i = 0; $i < strlen($string); $i++)
        {
            $char = $string[$i];

            if (false !== ($pos = array_search($char, $this->english)))
            {
                $char = $this->leet[$pos]; //Change the char to l33t.
            }
            $result .= $char;
        }
        return $result;
    }

    /**
     * @return LeetEscaper
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param \Twig_Environment $environment
     * @param $content
     * @param $charset
     */
    public static function escape(\Twig_Environment $environment, $content, $charset)
    {
        return self::create()->encode($content);
    }
}