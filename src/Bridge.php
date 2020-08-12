<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Bridge functions between Laravel & Twig
 */
class Bridge extends Environment
{
    /**
     * @var string TwigBridge version
     */
    const BRIDGE_VERSION = '0.10.0';

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function __construct(LoaderInterface $loader, $options = [], Container $app = null)
    {
        // Twig 2.0 doesn't support `true` anymore
        if (isset($options['autoescape']) && $options['autoescape'] === true) {
            $options['autoescape'] = 'html';
        }

        parent::__construct($loader, $options);

        $this->app = $app;
    }

    /**
     * Get the Laravel app.
     *
     * @return \Illuminate\Contracts\Container\Container
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Set the Laravel app.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    public function setApplication(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Lint (check) the syntax of a file on the view paths.
     *
     * @param string $file File to check. Supports dot-syntax.
     *
     * @return bool Whether the file passed or not.
     */
    public function lint($file)
    {
        /** @var Source $template */
        $template = $this->app['twig.loader.viewfinder']->getSourceContext($file);

        $code = trim($template->getCode());
        if (empty($code)) {
            throw new InvalidArgumentException('Unable to find file: ' . $file);
        }

        try {
            $this->parse($this->tokenize($template, $file));
        } catch (Error $e) {
            return false;
        }

        return true;
    }
}
