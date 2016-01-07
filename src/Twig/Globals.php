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

if (version_compare(\Twig_Environment::VERSION, '1.23.0') === -1) {
    interface Globals
    {
    }
} else {
    interface Globals extends \Twig_Extension_GlobalsInterface
    {
    }
}
