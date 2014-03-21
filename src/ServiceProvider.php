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
        $this->registerTwigEngine();
        $this->registerCommands();
    }

    /**
     * Register the Twig engine.
     *
     * @return void
     */
    public function registerTwigEngine()
    {
        $app = $this->app;

        // Extensions
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

        // Loader
        $app->bindIf('twig.loader.path', function () {
            return new Twig\Loader\Path;
        });

        $app->bindIf('twig.loader.viewfinder', function () use ($app) {
            return new Twig\Loader\Viewfinder(
                $app['view']->getFinder(),
                $app['twig.bridge']->getExtension()
            );
        });

        $app->bindIf('twig.loader', function () use ($app) {
            return new Twig_Loader_Chain(array(
                $app['twig.loader.path'],
                $app['twig.loader.viewfinder'],
            ));
        });

        // Twig
        $app->bindIf('twig.options', function () use ($app) {
            $options = $app['config']->get('twigbridge::twig.environment', array());

            // Check whether we have the cache path set
            if (empty($options['cache'])) {
                // No cache path set for Twig, lets set to the Laravel views storage folder
                $options['cache'] = $app['path.storage'].'/views/twig';
            }

            return $options;
        });

        $app->bindIf('twig.bridge', function () use ($app) {
            return new TwigBridge($app);
        });

        $app->bindIf('twig', function () use ($app) {
            return $app['twig.bridge']->getTwig();
        });

        // Engine
        $app->bindIf('twig.engine', function () use ($app) {
            return new Engine\Twig(
                $app['twig'],
                $app['config']->get('twigbridge::twig.globals', array())
            );
        });

        $app['view']->addExtension(
            $app['config']->get('twigbridge::twig.extension', 'twig'),
            'twig',
            function () use ($app) {
                return $app['twig.engine'];
            }
        );
    }

    /**
     * Register the cache related console commands.
     *
     * @return void
     */
    public function registerCommands()
    {
        $this->app[''] = $this->app->bindIf('command.twig', function ($app) {
            return new Command\TwigBridge;
        });

        $this->app->bindIf('command.twig.clean', function ($app) {
            return new Command\Clean;
        });

        $this->app->bindIf('command.twig.lint', function ($app) {
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
