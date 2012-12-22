<?php

namespace TwigBridge;

use Illuminate\View\ViewServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Twig_Environment;
use Twig_Lexer;

class TwigServiceProvider extends ViewServiceProvider
{
    /**
     * @var string TwigBridge version
     */
    const VERSION = '0.1.0';

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
        $bridge = new TwigBridge($this->app);

        var_dump($bridge->getOptions());

        die(var_dump( get_class($this->app) ));

        // $paths     = $this->app['config']['view.paths'];
        // $options   = $this->app['config']->get('twigbridge::twig', array());

        // if (!isset($options['cache']) OR $options['cache'] === null) {
        //     // No cache path set for Twig, lets set to the Laravel views storage folder
        //     $options['cache'] = $this->app['path'].'/storage/views/twig';
        //     $this->app['config']->set('twigbridge::twig.cache', $options['cache']);
        // }

        // $loader = new Twig\Loader\Filesystem($paths, $extension);
        // $twig   = new Twig_Environment($loader, $options);

        // Allow block delimiters to be changes
        // $lexer = new Twig_Lexer($twig, $this->app['config']->get('twigbridge::delimiters', array(
        //     'tag_comment'  => array('{#', '#}'),
        //     'tag_block'    => array('{%', '%}'),
        //     'tag_variable' => array('{{', '}}'),
        // )));

        // $twig->setLexer($lexer);

        // Load config defined extensions
        $extensions = $this->app['config']->get('twigbridge::extensions', array());

        foreach ($extensions as $extension) {

            // Create a new instance of the extension
            $obj = new $extension;

            // If of correct type, set the application object on the extension
            if (get_parent_class($obj) === 'TwigBridge\Extensions\Extension') {
                $obj->setApp($this->app);
            }

            $twig->addExtension($obj);
        }

        // // Alias loader
        // // We look for the Twig function in our aliases
        // // It takes the pattern alias_function(...)
        // $aliases   = $this->app['config']->get('app.aliases', array());
        // $shortcuts = $this->app['config']->get('twigbridge::alias_shortcuts', array());

        // // Allow alias functions to be disabled
        // if (!$this->app['config']->get('twigbridge::disable_aliases', false)) {
        //     $twig->registerUndefinedFunctionCallback(function($name) use($aliases, $shortcuts) {
        //         // Allow any method on aliased classes
        //         // Classes are aliased in your config/app.php file
        //         $alias = new Extensions\AliasLoader($aliases, $shortcuts);
        //         return $alias->getFunction($name);
        //     });
        // }

        // Register twig engine
        // $app = $this->app;

        // $resolver->register('twig', function() use($app, $twig)
        // {
        //     // Give anyone listening the chance to alter Twig
        //     // Perfect example is adding Twig extensions.
        //     // Another package can automatically add Twig function support.
        //     $app['events']->fire('twigbridge.twig', array($twig));

        //     return new Engines\TwigEngine($twig);
        // });
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
        $this->app['command.twigbridge'] = $this->app->share(function($app)
        {
            return new Console\TwigBridgeCommand;
        });

        // Empty Twig cache command
        $this->app['command.twigbridge.clean'] = $this->app->share(function($app)
        {
            return new Console\CleanCommand;
        });

        $this->commands('command.twigbridge', 'command.twigbridge.clean');
    }
}