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

use Illuminate\View\ViewServiceProvider;
use Twig_Loader_Chain;
use Twig_Environment;

/**
 * Bootstrap Laravel TwigBridge.
 *
 * You need to include this `ServiceProvider` in your app.php file:
 *
 * <code>
 *     'providers' => [
 *         'TwigBridge\ServiceProvider'
 *     ];
 * </code>
 */
class ServiceProvider extends ViewServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Register the package configuration with the loader.
        $this->app['config']->package('rcrowe/twigbridge', __DIR__.'/Config');
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerCommands();
        $this->registerTwigBridge();
        $this->registerTwigLoaders();
        $this->registerTwigOptions();
        $this->registerTwigEngine();

        $this->app['view']->addExtension(
            $this->app['twig.extension'],
            'twig',
            function () {
                $bridge = $this->app['twig.bridge'];
                $lexer  = $this->app['twig.lexer'];

                $bridge->addExtension($this->app['twig.extensions']);

                if (is_a($lexer, 'Twig_LexerInterface')) {
                    $bridge->setLexer($lexer);
                }

                return $this->app['twig.engine'];
            }
        );
    }

    /**
     * Register console command bindings.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app->bindIf('command.twig', function () {
            return new Command\TwigBridge;
        });

        $this->app->bindIf('command.twig.clean', function () {
            return new Command\Clean;
        });

        $this->app->bindIf('command.twig.lint', function () {
            return new Command\Lint;
        });

        $this->commands(
            'command.twig',
            'command.twig.clean',
            'command.twig.lint'
        );
    }

    /**
     * Register TwigBridge bindings.
     *
     * @return void
     */
    protected function registerTwigBridge()
    {
        $this->app->bindIf('twig.bridge', function () {
            return new Bridge($this->app);
        });
    }

    /**
     * Register Twig loader bindings.
     *
     * @return void
     */
    protected function registerTwigLoaders()
    {
        $this->app->bindIf('twig.loader.path', function () {
            return new Twig\Loader\Path;
        });

        $this->app->bindIf('twig.loader.viewfinder', function () {
            return new Twig\Loader\Viewfinder(
                $this->app['view']->getFinder(),
                $this->app['twig.extension']
            );
        });

        $this->app->bindIf('twig.loader', function () {
            return new Twig_Loader_Chain([
                $this->app['twig.loader.path'],
                $this->app['twig.loader.viewfinder'],
            ]);
        });
    }

    /**
     * Register Twig config option bindings.
     *
     * @return void
     */
    protected function registerTwigOptions()
    {
        $this->app->bindIf('twig.extension', function () {
            return $this->app['config']->get('twigbridge::twig.extension');
        });

        $this->app->bindIf('twig.options', function () {
            $options = $this->app['config']->get('twigbridge::twig.environment', []);

            // Check whether we have the cache path set
            if (empty($options['cache'])) {
                // No cache path set for Twig, lets set to the Laravel views storage folder
                $options['cache'] = $this->app['path.storage'].'/views/twig';
            }

            return $options;
        });

        $this->app->bindIf('twig.extensions', function () {
            $extensions = $this->app['config']->get('twigbridge::extensions.enabled', []);

            // Is debug enabled?
            $options = $this->app['twig.options'];
            $debug   = (bool) (isset($options['debug'])) ? $options['debug'] : false;

            if ($debug) {
                array_unshift($extensions, 'Twig_Extension_Debug');
            }

            return $extensions;
        });

        $this->app->bindIf('twig.lexer', function () {
            return null;
        });
    }

    /**
     * Register Twig engine bindings.
     *
     * @return void
     */
    protected function registerTwigEngine()
    {
        if (!$this->app->bound('twig')) {
            $this->app->singleton('twig', function () {
                return new Twig_Environment(
                    $this->app['twig.loader'],
                    $this->app['twig.options']
                );
            });
        }

        $this->app->bindIf('twig.engine', function () {
            return new Engine\Twig(
                $this->app['twig'],
                $this->app['config']->get('twigbridge::twig.globals', [])
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'twig',
            'twig.bridge',
            'twig.engine',
            'twig.extensions',
            'twig.options',
            'twig.loader',
            'twig.loader.path',
            'twig.loader.viewfinder',
            'command.twig',
            'command.twig.clean',
            'command.twig.lint',
        );
    }
}
