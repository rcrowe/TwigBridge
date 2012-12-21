<?php

namespace TwigBridge\Twig\Loader;

use Twig_Loader_Filesystem;

class Filesystem extends Twig_Loader_Filesystem
{
    /**
     * @var string Twig file extension
     */
    protected $extension;

    /**
     * Constructor.
     *
     * @param string|array $paths     A path or an array of paths where to look for templates
     * @param string       $extension Twig file extension
     */
    public function __construct($paths, $extension = 'twig')
    {
        parent::__construct($paths);

        $this->extension = $extension;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * No longer have to use an extension for a twig file in your templates.
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