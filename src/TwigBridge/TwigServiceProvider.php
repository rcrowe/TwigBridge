<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge;

use Illuminate\View\ViewServiceProvider;
use Twig_Loader_Chain;

/**
 * Bootstrap Laravel Twig(Bridge).
 */
class TwigServiceProvider extends ViewServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Register the package configuration with the loader.
        $this->app['config']->package('rcrowe/twigbridge', __DIR__.'/../config');
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerTwigEngine();
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
        $app['twig.extensions'] = $app->share(function () use ($app) {
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
        $app['twig.loader.path'] = $app->share(function () {
            return new Twig\Loader\Path;
        });

        $app['twig.loader.viewfinder'] = $app->share(function () use ($app) {
            return new Twig\Loader\Viewfinder(
                $app['view']->getFinder(),
                $app['twig.bridge']->getExtension()
            );
        });

        $app['twig.loader.filesystem'] = $app->share(function () use ($app) {
            return new Twig\Loader\Filesystem(
                $app['view']->getFinder(),
                $app['twig.bridge']->getExtension()
            );
        });

        $app['twig.loader'] = $app->share(function () use ($app) {
            return new Twig_Loader_Chain(array(
                $app['twig.loader.path'],
                $app['twig.loader.viewfinder'],
                $app['twig.loader.filesystem'],
            ));
        });

        // Twig
        $app['twig.options'] = $app->share(function () use ($app) {
            $options = $app['config']->get('twigbridge::twig', array());

            // Check whether we have the cache path set
            if (empty($options['cache'])) {
                // No cache path set for Twig, lets set to the Laravel views storage folder
                $options['cache'] = $app['path.storage'].'/views/twig';
            }

            return $options;
        });

        $app['twig.bridge'] = $app->share(function () use ($app) {
            return new TwigBridge($app);
        });

        $app['twig'] = $app->share(function () use ($app) {
            return $app['twig.bridge']->getTwig();
        });

        // Engine
        $app['twig.engine'] = $app->share(function () use ($app) {
            return new Engine\Twig(
                $app['twig'],
                $app['config']->get('twigbridge::globals', array())
            );
        });

        $app['view']->addExtension(
            $app['config']->get('twigbridge::extension', 'twig'),
            'twig',
            function () use ($app) {
                return $app['twig.engine'];
            }
        );
    }
}
