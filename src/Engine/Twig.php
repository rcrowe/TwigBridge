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

use Twig_Error;
use ErrorException;
use Illuminate\View\Engines\CompilerEngine;
use TwigBridge\Twig\Loader\Viewfinder;


/**
 * View engine for Twig files.
 */
class Twig extends CompilerEngine
{
    /**
     * @var array Global data that is always passed to the template.
     */
    protected $globalData = [];

    /**
     * @var Viewfinder The ViewFinder Loader, to find the original file
     */
    protected $finder = [];

    /**
     * Create a new Twig view engine instance.
     *
     * @param \TwigBridge\Engine\Compiler $compiler
     * @param \TwigBridge\Twig\Loader\Viewfinder $finder
     * @param array $globalData
     *
     */
    public function __construct(Compiler $compiler, Viewfinder $finder, array $globalData = [])
    {
        parent::__construct($compiler);

        $this->finder     = $finder;
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
        $template = $this->compiler->load($path);

        try {
            return $template->render($data);
        } catch (Twig_Error $e) {
            $this->handleTwigError($e);
        }

    }

    /**
     * Handle a TwigError Exception.
     *
     * @param  Twig_Error  $e
     *
     * @throws Twig_Error|ErrorException
     */
    protected function handleTwigError($e)
    {
        $templateFile = $e->getTemplateFile();
        $templateLine = $e->getTemplateLine();

        if ($templateFile && file_exists($templateFile)) {
            $file = $templateFile;
        } elseif($templateFile) {
            $file = $this->finder->findTemplate($templateFile);
        }

        if ($file) {
            $e = new ErrorException($e->getMessage(), 0, 1, $file, $templateLine, $e);
        }

        throw $e;
    }
}
