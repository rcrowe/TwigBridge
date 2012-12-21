<?php

namespace TwigBridge\Twig;

use InvalidArgumentException;
use Twig_Environment;
use Twig_Lexer;

class Lexer
{
    /**
     * @var array Lexer tags.
     */
    protected $tags = array();

    public function __construct(array $comment, array $block, array $variable)
    {
        // Make sure arrays contain the tags we need
        foreach (array('comment', 'block', 'variable') as $type) {
            if (count($$type) !== 2) {
                throw new InvalidArgumentException(ucfirst($type).' must contain both an opening and closing tag');
            }
        }

        $this->tags = array(
            'tag_comment'  => $comment,
            'tag_block'    => $block,
            'tag_variable' => $variable,
        );
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getLexer(Twig_Environment $twig)
    {
        return new Twig_Lexer($twig, $this->tags);
    }
}