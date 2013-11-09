<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\Twig\Loader;

use Twig_LoaderInterface;
use Twig_ExistsLoaderInterface;

/**
 * Deals with loading a template with an absolute path.
 */
class Path implements Twig_LoaderInterface, Twig_ExistsLoaderInterface
{
    /**
     * Return path to template.
     *
     * @param string $name Template file path.
     *
     * @return string|bool Path to template or FALSE if not found.
     */
    protected function findTemplate($name)
    {
        return realpath($name);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return is_file($this->findTemplate($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        return (filemtime($this->findTemplate($name)) <= $time);
    }
}
