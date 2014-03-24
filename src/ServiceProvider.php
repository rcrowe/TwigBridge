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
use Illuminate\Foundation\Application;
use Twig_Loader_Chain;
use Twig_Environment;

/**
 * Bootstrap Laravel TwigBridge.
 *
 * You need to include this `ServiceProvider` in your app.php file:
 *
 * <code>
 *     'providers' => array(
 *         'TwigBridge\ServiceProvider'
 *     );
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
        $app = $this->app;

        $this->registerCommands($app);
        $this->registerTwigBridge($app);
        $this->registerTwigLoaders($app);
        $this->registerTwigOptions($app);
        $this->registerTwigEngine($app);

        $app['view']->addExtension(
            $app['twig.extension'],
            'twig',
            function () use ($app) {
                $bridge = $app['twig.bridge'];
                $lexer  = $app['twig.lexer'];

                $bridge->addExtension($app['twig.extensions']);

                if (is_a($lexer, 'Twig_LexerInterface')) {
                    $bridge->setLexer($lexer);
                }

                return $app['twig.engine'];
            }
        );
    }

    /**
     * Register TwigBridge bindings.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function registerTwigBridge(Application $app)
    {
        $app->bindIf('twig.bridge', function () use ($app) {
            return new Bridge($app);
        });
    }

    /**
     * Register Twig loader bindings.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function registerTwigLoaders(Application $app)
    {
        $app->bindIf('twig.loader.path', function () {
            return new Twig\Loader\Path;
        });

        $app->bindIf('twig.loader.viewfinder', function () use ($app) {
            return new Twig\Loader\Viewfinder(
                $app['view']->getFinder(),
                $app['twig.extension']
            );
        });

        $app->bindIf('twig.loader', function () use ($app) {
            return new Twig_Loader_Chain(array(
                $app['twig.loader.path'],
                $app['twig.loader.viewfinder'],
            ));
        });
    }

    /**
     * Register Twig config option bindings.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function registerTwigOptions(Application $app)
    {
        $app->bindIf('twig.extension', function () use ($app) {
            return $app['config']->get('twigbridge::twig.extension');
        });

        $app->bindIf('twig.options', function () use ($app) {
            $options = $app['config']->get('twigbridge::twig.environment', array());

            // Check whether we have the cache path set
            if (empty($options['cache'])) {
                // No cache path set for Twig, lets set to the Laravel views storage folder
                $options['cache'] = $app['path.storage'].'/views/twig';
            }

            return $options;
        });

        $app->bindIf('twig.extensions', function () use ($app) {
            $extensions = $app['config']->get('twigbridge::extensions.enabled', array());

            // Is debug enabled?
            $options = $app['twig.options'];
            $debug   = (bool) (isset($options['debug'])) ? $options['debug'] : false;

            if ($debug) {
                array_unshift($extensions, 'Twig_Extension_Debug');
            }

            return $extensions;
        });

        $app->bindIf('twig.lexer', function () use ($app) {
            return null;
        });
    }

    /**
     * Register Twig engine bindings.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function registerTwigEngine(Application $app)
    {
        if (!$app->bound('twig')) {
            $app->singleton('twig', function () use ($app) {
                return new Twig_Environment($app['twig.loader'], $app['twig.options']);
            });
        }

        $app->bindIf('twig.engine', function () use ($app) {
            return new Engine\Twig(
                $app['twig'],
                $app['config']->get('twigbridge::twig.globals', array())
            );
        });
    }

    /**
     * Register console command bindings.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function registerCommands(Application $app)
    {
        $app->bindIf('command.twig', function () {
            return new Command\TwigBridge;
        });

        $app->bindIf('command.twig.clean', function () {
            return new Command\Clean;
        });

        $app->bindIf('command.twig.lint', function () {
            return new Command\Lint;
        });

        $this->commands(
            'command.twig',
            'command.twig.clean',
            'command.twig.lint'
        );
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
