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

use Illuminate\View\Compilers\CompilerInterface;
use Twig_Environment;
use Twig_Error_Loader;
use InvalidArgumentException;
use TwigBridge\Twig\Template;

class Compiler implements CompilerInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Create a new instance of the Twig compiler.
     *
     * @param \Twig_Environment $twig
     * @param array             $globalData
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
     * Twig handles this for us.
     *
     * {@inheritdoc}
     */
    public function getCompiledPath($path)
    {
        return $this->twig->getCacheFilename($path);
    }

    /**
     * Twig handles this for us.
     *
     * {@inheritdoc}
     */
    public function isExpired($path)
    {
        $time = filemtime($this->getCompiledPath($path));

        return $this->twig->isTemplateFresh($path, $time);
    }

    /**
     * Compile the view at the given path.
     *
     * @param string $path
     *
     * @return string \TwigBridge\Twig\Template
     */
    public function compile($path)
    {
        // Load template
        try {
            $template = $this->twig->loadTemplate($path);
        } catch (Twig_Error_Loader $e) {
            throw new InvalidArgumentException("Error in $name: ". $e->getMessage(), $e->getCode(), $e);
        }

        if ($template instanceof Template) {
            // Events are already fired by the View Environment
            $template->setFiredEvents(true);
        }

        return $template;
    }
}
