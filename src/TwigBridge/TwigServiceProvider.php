<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge;

use Illuminate\View\ViewServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Twig_Environment;
use Twig_Lexer;

/**
 * Bootstrap TwigBridge with Laravel.
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
        // Override Environment
        // We need to do this in order to set the name of the generated/compiled twig templates
        // Laravel by default only passes the full path to the requested view, we also need the view name
        // that relates to defined view composers.
        $this->app['view'] = $this->app->share(function($app) {
            $env = new View\Environment($app['view.engine.resolver'], $app['view.finder'], $app['events']);
            $env->setContainer($app);
            $env->share('app', $app);

            return $env;
        });

        $this->registerTwigEngine();
        $this->registerCommands();
    }

    /**
     * Register the PHP engine implementation.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @param  Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerTwigEngine()
    {
        $app = $this->app;

        // Add TwigBridge to the IoC
        $app['twig.bridge'] = $app->share(function () use ($app) {
            return new TwigBridge($app);
        });

        // Add Twig to the IoC
        $app['twig'] = $app->share(function () use ($app) {
            return $app['twig.bridge']->getTwig();
        });

        // Register Twig engine
        $app['view']->addExtension($app['config']->get('twigbridge::extension', 'twig'), 'twig', function () use ($app)
        {
            // Get any global variables
            $globals = $app['config']->get('twigbridge::globals', array());

            return new Engines\TwigEngine($app['twig'], $globals);
        });
    }

    /**
     * Register the artisan commands.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    public function registerCommands()
    {
        // Info command
        $this->app['command.twigbridge'] = $this->app->share(
            function ($app) {
                return new Console\TwigBridgeCommand;
            }
        );

        // Empty Twig cache command
        $this->app['command.twigbridge.clean'] = $this->app->share(
            function ($app) {
                return new Console\CleanCommand;
            }
        );

        // Compile command
        $this->app['command.twigbridge.compile'] = $this->app->share(
            function ($app) {
                return new Console\CompileCommand;
            }
        );

        // Lint command
        $this->app['command.twigbridge.lint'] = $this->app->share(
            function ($app) {
                return new Console\LintCommand;
            }
        );

        $this->commands(
            'command.twigbridge',
            'command.twigbridge.clean',
            'command.twigbridge.compile',
            'command.twigbridge.lint'
        );
    }
}
