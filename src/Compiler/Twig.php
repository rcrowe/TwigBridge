<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Compiler;

use Twig_Environment;
use Twig_Error;
use Illuminate\View\Compilers\CompilerInterface;

/**
 * Twig compiler for Laravel.
 */
class Twig implements CompilerInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Create a new instance of the Twig engine.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Returns the instance of Twig used to render the template.
     *
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * Get the path to the compiled version of a view.
     *
     * @param  string  $path
     * @return string
     */
    public function getCompiledPath($path)
    {
        return $this->twig->getCacheFilename($path);
    }

    /**
     * Determine if the given view is expired.
     *
     * @param  string  $path
     * @return bool
     */
    public function isExpired($path)
    {
        $time = filemtime($this->getCompiledPath($path));
        return $this->twig->isTemplateFresh($path, $time);
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string  $path
     * @return void
     */
    public function compile($path)
    {
        try{
            $this->twig->loadTemplate($path);
        }catch(\Exception $e){
            // Something went wrong..
        }
    }
}
