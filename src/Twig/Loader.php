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

use Twig_LoaderInterface;
use Twig_Error_Loader;
use Twig_ExistsLoaderInterface;
use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ViewFinderInterface;

/**
 * Basic loader using absolute paths.
 */
class Loader implements Twig_LoaderInterface, Twig_ExistsLoaderInterface
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

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
    protected $cache = [];

    /**
     * @param \Illuminate\Filesystem\Filesystem     $files The filesystem
     * @param \Illuminate\View\ViewFinderInterface  $finder
     * @param string                                $extension Twig file extension.
     */
    public function __construct(Filesystem $files, ViewFinderInterface $finder, $extension = 'twig')
    {
        $this->files     = $files;
        $this->finder    = $finder;
        $this->extension = $extension;
    }

    /**
     * Return path to template without the need for the extension.
     *
     * @param string $name Template file path.
     *
     * @throws \Twig_Error_Loader
     * @return string|bool Path to template or FALSE if not found.
     */
    public function findTemplate($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if ($this->files->exists($name)) {
            return $this->cache[$name] = $name;
        }

        if ($this->files->extension($name) === $this->extension) {
            $name = substr($name, 0, - (strlen($this->extension) + 1));
        }

        try {
            return $this->cache[$name] = $this->finder->find($name);
        } catch (InvalidArgumentException $ex) {
            throw new Twig_Error_Loader($ex->getMessage());
        }

    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        try {
            $this->findTemplate($name);

            return true;
        } catch (Twig_Error_Loader $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return $this->files->get($this->findTemplate($name));
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
        return $this->files->lastModified($this->findTemplate($name)) <= $time;
    }
}
