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
     * @var array Twig environment options.
     */
    protected $options = array();

    /**
     * @var string Twig template extension.
     */
    protected $extension;

    /**
     * @var array Extensions to add to Twig.
     */
    protected $extensions = array();

    /**
     * @var TwigBridge\Twig\Lexer Twig_Lexer wrapper.
     */
    protected $lexer;

    /**
     * Create a new instance.
     *
     * @param Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app        = $app;
        $this->extension  = $app['config']->get('twigbridge::extension');
        $this->extensions = $app['config']->get('twigbridge::extensions', array());

        $this->setOptions($app['config']->get('twigbridge::twig', array()));
    }

    /**
     * Get the view paths that Twig should search on.
     *
     * Currently this is hacked to work, but hopefully my pull request gets accepted
     * soon as I can change some of this code out.
     *
     * @param array $extra_paths Add any paths to search at runtime. Will look in these first.
     * @return array Merged paths to look on.
     */
    public function getPaths(array $extra_paths = array())
    {
        $finder = $this->app['view']->getFinder();

        // FIXME: Super hack until pull-request gets accepted
        // This will work for now

        // Get view paths
        $prop = new ReflectionProperty('Illuminate\View\FileViewFinder', 'paths');
        $prop->setAccessible(true);

        // $paths = $finder->getPaths();
        $paths = $prop->getValue($finder);

        // Get all paths for registered namespaces
        $prop = new ReflectionProperty('Illuminate\View\FileViewFinder', 'hints');
        $prop->setAccessible(true);

        // $namespace_paths = $finder->getHints();
        $namespace_paths = array();

        foreach ($prop->getValue($finder) as $namespace => $paths) {
            foreach ($paths as $path) {
                $namespace_paths[] = $path;
            }
        }

        // Combine package and view paths
        // View paths take precedence
        return array_merge($extra_paths, $paths, $namespace_paths);
    }

    /**
     * Get options passed to Twig_Environment.
     *
     * @return array
     */
    public function getTwigOptions()
    {
        return $this->options;
    }

    /**
     * Set options passed to Twig_Environment.
     *
     * Will set the cache path for you if one is not set.
     *
     * @param array $options Twig options.
     * @return void
     */
    public function setOptions(array $options)
    {
        // Check whether we have the cache path set
        if (!isset($options['cache']) OR $options['cache'] === null) {

            // No cache path set for Twig, lets set to the Laravel views storage folder
            $options['cache'] = $this->app['path'].'/storage/views/twig';
        }

        $this->options = $options;
    }

    /**
     * Get Twig template extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the extension of Twig templates.
     *
     * @param string $extension File extension without leading dot.
     * @return void
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * Get extensions that Twig should add.
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Set the extensions that Twig should add.
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Get the lexer for Twig to use.
     *
     * @param Twig_Environment $twig
     * @param array            $delimiters Opening & closing tags for comments, blocks & variables.
     * @return Twig_Lexer
     */
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

    /**
     * Set the lexer Twig should use.
     *
     * @param Twig_Lexer $lexer
     */
    public function setLexer(Twig_Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * Gets an instance of Twig that can be used to render a view.
     *
     * @return Twig_Environment
     */
    public function getTwig()
    {
        $loader = new Twig\Loader\Filesystem($this->getPaths(), $this->extension);
        $twig   = new Twig_Environment($loader, $this->options);

        // Allow template tags to be changed
        $twig->setLexer($this->getLexer($twig));

        // Load extensions
        foreach ($this->getExtensions() as $twig_extension) {

            // We support both a closure and class based extension
            $twig_extension = (!is_callable($twig_extension)) ? new $twig_extension($this->app, $twig) : $twig_extension($this->app, $twig);

            // Add extension to twig
            $twig->addExtension($twig_extension);
        }

        return $twig;
    }
}