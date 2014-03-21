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

use Twig_Lexer;

/**
 * Wraps default Twig lexer so we can get at some of the internals.
 */
class Lexer extends Twig_Lexer
{
    /**
     * Get the options passed to the lexer construct.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
