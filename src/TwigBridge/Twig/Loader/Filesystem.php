<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Twig\Loader;

use ReflectionProperty;
use Twig_Loader_Filesystem;
use Illuminate\View\ViewFinderInterface;

/**
 * Extends the Twig filesystem to remove the need to add
 * an extension inside a template.
 *
 * Removes the need to add an extension when for example
 * extending another template.
 */
class Filesystem extends Twig_Loader_Filesystem
{
    /**
     * @var string Twig file extension
     */
    protected $extension;

    /**
     * Create a new instance.
     *
     * @param Illuminate\View\ViewFinderInterface $finder
     * @param string                              $extension Twig file extension.
     */
    public function __construct(ViewFinderInterface $finder, $extension = 'twig')
    {
        $this->finder    = $finder;
        $this->extension = $extension;
        $view_paths      = $this->finder->getPaths();

        // Extract paths from namespaces
        $namespace_paths = array();

        foreach ($this->finder->getHints() as $namespace => $paths) {
            foreach ($paths as $path) {
                $namespace_paths[] = $path;
            }
        }

        // Combine package and view paths
        // View paths take precedence
        // $paths = array_merge($view_paths, $namespace_paths);
        $paths = array_merge($view_paths, $namespace_paths);
        $paths = array_unique($paths);

        // Setup twig with all the search paths
        parent::__construct($paths);
    }

    /**
     * Get the Twig template extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the extension Twig templates use.
     *
     * @param string $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * No longer have to use an extension for a twig file in your templates.
     *
     * @param string $name Template file name.
     * @return string Path to template.
     */
    protected function findTemplate($name, $abort = false)
    {
        if (strpos($name, '::') !== false) {
            // File is namespaced, use finder to lookup the file
            return $this->finder->find($name);
        }

        $extension = '.'.$this->extension;

        // Remove the extension
        if (strtolower(substr($name, -strlen($extension))) == $extension) {
            $name = substr($name, 0, -strlen($extension));
        }

        // Replace dot-notation
        $name = str_replace('.', '/', $name).$extension;

        return parent::findTemplate($name);
    }
}
