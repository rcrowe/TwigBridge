<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Twig\Loader;

use Twig_Loader_Filesystem;

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
     * @param string|array $paths     A path or an array of paths where to look for templates.
     * @param string       $extension Twig file extension.
     */
    public function __construct($paths, $extension = 'twig')
    {
        parent::__construct($paths);

        $this->extension = $extension;
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
    protected function findTemplate($name)
    {
        // Append twig file extension
        // Make things nicer in the templates
        $extension = '.'.$this->extension;

        if (strtolower(substr($name, -strlen($extension))) !== $extension) {
            $name .= $extension;
        }

        return parent::findTemplate($name);
    }
}