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

        $this->registerEngineResolver();
        $this->registerEnvironment();
        $this->registerCommands();
    }

    /**
     * Register the engine resolver instance.
     *
     * @return void
     */
    public function registerEngineResolver()
    {
        $me = $this;

        $this->app['view.engine.resolver'] = $this->app->share(
            function ($app) use ($me) {

                $resolver = new EngineResolver;

                // Next we will register the various engines with the resolver so that the
                // environment can resolve the engines it needs for various views based
                // on the extension of view files. We call a method for each engines.
                foreach (array('php', 'blade', 'twig') as $engine) {
                    $me->{'register'.ucfirst($engine).'Engine'}($resolver);
                }

                return $resolver;
            }
        );
    }

    /**
     * Register the PHP engine implementation.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @param  Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerTwigEngine($resolver)
    {
        $app = $this->app;

        $resolver->register(
            'twig',
            function () use ($app) {
                // Grab Twig
                $bridge = new TwigBridge($app);
                $twig   = $bridge->getTwig();

                $app['events']->fire('twigbridge.twig', array($twig));

                // Get any global variables
                $globals = $app['config']->get('twigbridge::globals', array());

                return new Engines\TwigEngine($twig, $globals);
            }
        );
    }

    /**
     * Register the view environment.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    public function registerEnvironment()
    {
        $this->app['view']->addExtension($this->app['config']->get('twigbridge::extension', 'twig'), 'twig');
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

        $this->commands('command.twigbridge', 'command.twigbridge.clean');
    }
}
