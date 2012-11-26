<?php

namespace TwigBridge;

use Illuminate\View\ViewServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\Support\MessageBag;
use Twig_Environment;
use Twig_Lexer;

class TwigServiceProvider extends ViewServiceProvider
{
    /**
     * Register the service provider.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    public function register()
    {
        // Register the package configuration with the loader.
        $this->app['config']->package('rcrowe/twigbridge', __DIR__.'/../');

        $this->registerEngineResolver();
        $this->registerViewFinder();
        $this->registerEnvironment();
    }

    /**
     * Register the engine resolver instance.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    public function registerEngineResolver()
    {
        list($me, $app) = array($this, $this->app);

        $app['view.engine.resolver'] = $app->share(function($app) use ($me)
        {
            $resolver = new EngineResolver;

            // Next we will register the various engines with the resolver so that the
            // environment can resolve the engines it needs for various views based
            // on the extension of view files. We call a method for each engines.
            foreach (array('php', 'blade', 'twig') as $engine)
            {
                $me->{'register'.ucfirst($engine).'Engine'}($resolver);
            }

            return $resolver;
        });
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
        $paths = $this->app['config']['view.paths'];

        // Grab the environment options from the config
        $options = $this->app['config']->get('twigbridge::environment', array());

        // If no cache path is set, we will try using the default file storage path
        if (!isset($options['cache'])) {
            $options['cache'] = $this->app['config']->get('cache.path').'/twig';
        }

        $loader = new Twig\Loader\Filesystem($paths);
        $twig   = new Twig_Environment($loader, $options);

        // Allow block delimiters to be changes
        $lexer = new Twig_Lexer($twig, $this->app['config']->get('twigbridge::delimiters', array(
            'tag_comment'  => array('{#', '#}'),
            'tag_block'    => array('{%', '%}'),
            'tag_variable' => array('{{', '}}'),
        )));

        $twig->setLexer($lexer);

        // Register twig engine
        $resolver->register('twig', function() use($twig)
        {
            return new Engines\TwigEngine($twig);
        });
    }

    /**
     * Register the view finder implementation.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    public function registerViewFinder()
    {
        $this->app['view.finder'] = $this->app->share(function($app)
        {
            $paths = $app['config']['view.paths'];

            return new FileViewFinder($app['files'], $paths);
        });
    }

    /**
     * Register the view environment.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    public function registerEnvironment()
    {
        $me = $this;

        $this->app['view'] = $this->app->share(function($app) use ($me)
        {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];

            $finder = $app['view.finder'];

            $environment = new Environment($resolver, $finder, $app['events']);

            // If the current session has an "errors" variable bound to it, we will share
            // its value with all view instances so the views can easily access errors
            // without having to bind. An empty bag is set when there aren't errors.
            if ($me->sessionHasErrors($app))
            {
                $errors = $app['session']->get('errors');

                $environment->share('errors', $errors);
            }

            // Putting the errors in the view for every view allows the developer to just
            // assume that some errors are always available, which is convenient since
            // they don't have to continually run checks for the presence of errors.
            else
            {
                $environment->share('errors', new MessageBag);
            }

            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $environment->setContainer($app);

            $environment->share('app', $app);

            return $environment;
        });
    }
}