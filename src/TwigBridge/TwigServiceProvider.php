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
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register the package configuration with the loader.
        $this->app['config']->package('rcrowe/twigbridge', __DIR__.'/../config');

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

        $app['view']->addExtension($app['config']->get('twigbridge::extension', 'twig'), 'twig', function () use ($app)
        {
            // Grab Twig
            $bridge = new TwigBridge($app);
            $twig   = $bridge->getTwig();

            // Get any global variables
            $globals = $app['config']->get('twigbridge::globals', array());

            return new Engines\TwigEngine($twig, $globals);
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
