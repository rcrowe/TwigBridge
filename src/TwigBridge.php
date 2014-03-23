<?php


// Move Twig to the service provider
// Facade, calls Twig_Environment plus custom functions
// Remove / clean up the Lexer
// Move to using separate extensions


/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge;

use Illuminate\Foundation\Application;
use InvalidArgumentException;

/**
 * TwigBridge deals with creating an instance of Twig.
 */
class TwigBridge
{
    /**
     * @var string TwigBridge version
     */
    const VERSION = '0.6.0';

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Create a new instance.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __call($method, $args)
    {
        $instance = $this->app['twig'];

        switch (count($args)) {
            case 0:
                return $instance->$method();

            case 1:
                return $instance->$method($args[0]);

            case 2:
                return $instance->$method($args[0], $args[1]);

            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);

            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }

    public function addExtension($extensions)
    {
        $twig       = $this->app['twig'];
        $extensions = (!is_array($extensions)) ? array($extensions) : $extensions;

        foreach ($extensions as $extension) {
            // Get an instance of the extension
            // Support for string, closure and an object
            if (is_string($extension)) {
                $extension = $this->app->make($extension);
            } elseif (is_callable($extension)) {
                $extension = $extension($this->app, $twig);
            } elseif (!is_object($extension)) {
                throw new InvalidArgumentException('Incorrect extension type');
            }

            // Add extension to twig
            $twig->addExtension($extension);
        }
    }
}
