<?php
namespace TokenParser;

use Node\UnsetNode;
use Twig_Token;

class UnsetTokenParser extends \Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new UnsetNode(array(), array('name' => $name));
    }

    public function getTag()
    {
        return 'unset';
    }

}