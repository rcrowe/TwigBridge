<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge;

use Illuminate\Foundation\Application;
use Twig_Environment;
use Twig_Lexer;
use ReflectionProperty;

/**
 * Provides Laravel with an instance of Twig in order to render Twig templates.
 */
class TwigBridge
{
    /**
     * @var string TwigBridge version
     */
    const VERSION = '0.1.0';

    /**
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $paths = array();
    protected $options = array();
    protected $extension;
    protected $extensions;
    protected $lexer;

    public function __construct(Application $app)
    {
        $this->app        = $app;
        $this->paths      = $app['config']->get('view.paths', array());
        $this->extension  = $app['config']->get('twigbridge::extension');
        $this->extensions = $app['config']->get('twigbridge::extensions', array());

        $this->setOptions($app['config']->get('twigbridge::twig', array()));
    }

    public function getPaths()
    {
        // Super hack until pull-request gets accepted
        // This will work for now
        // Get all paths for registered namespaces
        $finder = $this->app['view']->getFinder();

        $prop = new ReflectionProperty('Illuminate\View\FileViewFinder', 'hints');
        $prop->setAccessible(true);

        $namespace_paths = array();

        foreach ($prop->getValue($finder) as $namespace => $paths) {
            foreach ($paths as $path) {
                $namespace_paths[] = $path;
            }
        }

        // Combine package and view paths
        // View paths take precedence
        return array_merge($this->paths, $namespace_paths);
    }

    public function setPaths(array $paths)
    {
        $this->paths = $paths;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        // Check whether we have the cache path set
        if (!isset($options['cache']) OR $options['cache'] === null) {

            // No cache path set for Twig, lets set to the Laravel views storage folder
            $options['cache'] = $this->app['path'].'/storage/views/twig';
        }

        $this->options = $options;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;
    }

    public function getLexer(Twig_Environment $twig, array $delimiters = null)
    {
        if ($this->lexer !== null) {
            return $this->lexer;
        }

        if ($delimiters === null) {
            $delimiters = $this->app['config']->get('twigbridge::delimiters', array(
                'tag_comment'  => array('{#', '#}'),
                'tag_block'    => array('{%', '%}'),
                'tag_variable' => array('{{', '}}'),
            ));
        }

        $lexer = new Twig\Lexer(
            $delimiters['tag_comment'],
            $delimiters['tag_block'],
            $delimiters['tag_variable']
        );

        return $lexer->getLexer($twig);
    }

    public function setLexer(Twig_Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    public function getTwig()
    {
        $loader = new Twig\Loader\Filesystem($this->getPaths(), $this->extension);
        $twig   = new Twig_Environment($loader, $this->options);

        // Allow template tags to be changed
        $twig->setLexer($this->getLexer($twig));

        // Load extensions
        foreach ($this->getExtensions() as $extension) {

            // We support both a closure and class based extension
            $extension = (!is_callable($extension)) ? new $extension($this->app, $twig) : $extension($this->app, $twig);

            // Add extension to twig
            $twig->addExtension($extension);
        }

        return $twig;
    }
}