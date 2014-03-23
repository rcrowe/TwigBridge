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

use Twig_LoaderInterface;
use Twig_ExistsLoaderInterface;
use InvalidArgumentException;

/**
 * Basic loader using absolute paths.
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
        $file = $this->findTemplate($name);

        if (!$file) {
            throw new InvalidArgumentException('Unable to get source for file: '.$name);
        }

        return file_get_contents($file);
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
