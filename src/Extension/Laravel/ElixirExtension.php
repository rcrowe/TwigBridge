<?php

/*
 * (c) Brieuc Thomas <tbrieuc@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrieucThomas\Twig\Extension;

/**
 * Twig extension for the Laravel Elixir component.
 *
 * @author Brieuc Thomas <tbrieuc@gmail.com>
 */
class ElixirExtension extends \Twig_Extension
{
    protected $publicDir;
    protected $buildDir;
    protected $manifestName;
    protected $manifest;

    public function __construct($publicDir, $buildDir = 'build', $manifestName = 'rev-manifest.json')
    {
        $this->publicDir = rtrim($publicDir, '/');
        $this->buildDir = trim($buildDir, '/');
        $this->manifestName = $manifestName;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('elixir', [$this, 'getVersionedFilePath']),
        ];
    }

    /**
     * Gets the public url/path to a versioned Elixir file.
     *
     * @param string $file
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getVersionedFilePath($file)
    {
        $manifest = $this->getManifest();

        if (!isset($manifest[$file])) {
            throw new \InvalidArgumentException("File {$file} not defined in asset manifest.");
        }

        return $this->buildDir.'/'.$manifest[$file];
    }

    /**
     * Returns the manifest file content as array.
     *
     * @return array
     */
    protected function getManifest()
    {
        if (null === $this->manifest) {
            $manifestPath = $this->publicDir.'/'.$this->buildDir.'/'.$this->manifestName;
            $this->manifest = json_decode(file_get_contents($manifestPath), true);
        }

        return $this->manifest;
    }

    public function getName()
    {
        return 'elixir';
    }
}
