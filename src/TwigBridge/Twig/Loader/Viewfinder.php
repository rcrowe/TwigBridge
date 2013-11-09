<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Twig\Loader;

use Illuminate\View\ViewFinderInterface;

/**
 * Same as Path loader without the need to use an extension.
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
            $view = $name;
            $ext  = ".".$this->extension;
            $len  = strlen($ext);

            if (substr($view, -$len) == $ext) {
                $view = substr($view, 0, -$len);
            }

            // Cache for the next lookup
            $this->cache[$name] = realpath($this->finder->find($view));

            return $this->cache[$name];
        }
    }
}
