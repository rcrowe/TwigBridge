<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Engine;

use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Compilers\CompilerInterface;

class Twig extends CompilerEngine
{
    /**
     * @var array Global data that is always passed to the template.
     */
    protected $globalData = [];

    /**
     * Create a new Twig view engine instance.
     *
     * @param \Illuminate\View\Compilers\CompilerInterface $compiler
     * @param array                                        $globalData
     *
     * @return void
     */
    public function __construct(CompilerInterface $compiler, array $globalData = [])
    {
        parent::__construct($compiler);

        $this->globalData = $globalData;
    }

    /**
     * Get the global data.
     *
     * @return array
     */
    public function getGlobalData()
    {
        return $this->globalData;
    }

    /**
     * Set global data sent to the view.
     *
     * @param array $globalData Global data.
     *
     * @return void
     */
    public function setGlobalData(array $globalData)
    {
        $this->globalData = $globalData;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param string $path Full file path to Twig template.
     * @param array  $data
     *
     * @return string
     */
    public function get($path, array $data = [])
    {
        $data = array_merge($this->globalData, $data);

        return $this->compiler->compile($path)->render($data);
    }
}
