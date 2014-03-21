<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Twig\Loader;

use Illuminate\View\ViewFinderInterface;
use InvalidArgumentException;

/**
 * A loader using a ViewFinderInterface instance.
 *
 * Means that dot-syntax, views across packages, etc can be found.
 */
class Viewfinder extends Path
{
    /**
     * @var \Illuminate\View\ViewFinderInterface
     */
    protected $finder;

    /**
     * @var string Twig file extension.
     */
    protected $extension;

    /**
     * @var array Template lookup cache.
     */
    protected $cache = array();

    /**
     * Create a new instance.
     *
     * @param \Illuminate\View\ViewFinderInterface $finder
     * @param string                               $extension Twig file extension.
     */
    public function __construct(ViewFinderInterface $finder, $extension = 'twig')
    {
        $this->finder    = $finder;
        $this->extension = $extension;
    }

    /**
     * Return path to template without the need for the extension.
     *
     * @param string $name Template file path.
     *
     * @return string|bool Path to template or FALSE if not found.
     */
    protected function findTemplate($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        } else {
            $view      = $name;
            $extension = ".".$this->extension;
            $length    = strlen($extension);

            if (substr($view, -$length) == $extension) {
                $view = substr($view, 0, -$length);
            }

            // Cache for the next lookup
            try {
                $this->cache[$name] = realpath($this->finder->find($view));
            } catch (InvalidArgumentException $ex) {
                return false;
            }

            return $this->cache[$name];
        }
    }
}
