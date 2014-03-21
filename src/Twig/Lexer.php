<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Twig;

use InvalidArgumentException;
use Twig_Environment;
use Twig_Lexer;

/**
 * Deals with getting a new Twig_Lexer instance.
 */
class Lexer
{
    /**
     * @var array Lexer tags.
     */
    protected $tags = array();

    /**
     * Get a new instance.
     *
     * @param array $comment  Opening & closing tag for comments.
     * @param array $block    Opening & closing tag for blocks.
     * @param array $variable Opening & closing tag for variables.
     *
     * @throws \InvalidArgumentException If opening & closing tag aren't both defined.
     */
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

    /**
     * Get tags used for the lexer.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Get a new instance of Twig_Lexer.
     *
     * @param \Twig_Environment $twig
     *
     * @return \Twig_Lexer
     */
    public function getLexer(Twig_Environment $twig)
    {
        return new Twig_Lexer($twig, $this->tags);
    }
}
