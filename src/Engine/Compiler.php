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

use Exception;
use Illuminate\View\Compilers\CompilerInterface;
use InvalidArgumentException;
use Twig\Environment;
use Twig\TemplateWrapper;

/**
 * Compiles Twig templates.
 */
class Compiler implements CompilerInterface
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * Create a new instance of the Twig compiler.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Returns the instance of Twig used to render the template.
     *
     * @return Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * Twig handles this for us. Here to satisfy interface.
     *
     * {@inheritdoc}
     */
    public function getCompiledPath($path)
    {
        return $this->twig->getCacheFilename($path);
    }

    /**
     * Twig handles this for us. Here to satisfy interface.
     *
     * {@inheritdoc}
     */
    public function isExpired($path)
    {
        $time = filemtime($this->getCompiledPath($path));

        return $this->twig->isTemplateFresh($path, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function compile($path)
    {
        try {
            $this->load($path);
        } catch (Exception $ex) {
            // Unable to compile
            // Try running `php artisan twig:lint`
        }
    }

    /**
     * Compile the view at the given path.
     *
     * @param string $path
     *
     * @return TemplateWrapper
     * @throws \InvalidArgumentException
     *
     */
    public function load($path)
    {
        // Load template
        try {
            $tmplWrapper = $this->twig->load($path);
        } catch (Exception $e) {
            throw new InvalidArgumentException("Error loading $path: ". $e->getMessage(), $e->getCode(), $e);
        }

        return $tmplWrapper;
    }
}
