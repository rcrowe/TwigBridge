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
use Twig_Loader_Filesystem;
use InvalidArgumentException;
use ReflectionProperty;

/**
 * Provides Laravel with an instance of Twig in order to render Twig templates.
 */
class TwigBridge
{
    /**
     * @var string TwigBridge version
     */
    const VERSION = '0.5.0';

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

        $this->setTwigOptions($app['config']->get('twigbridge::twig', array()));
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
    public function setTwigOptions(array $options)
    {
        // Check whether we have the cache path set
        if (!isset($options['cache']) OR $options['cache'] === null) {

            // No cache path set for Twig, lets set to the Laravel views storage folder
            $options['cache'] = $this->app['path.storage'].'/views/twig';
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
    public function getLexer(Twig_Environment $twig = null, array $delimiters = null)
    {
        if ($this->lexer !== null) {
            return $this->lexer;
        } elseif ($twig === null) {
            // You must pass in an instance of Twig if the lexer has not already been set
            throw new InvalidArgumentException('No lexer set, you must pass an instance of Twig_Environment in!');
        }

        if ($delimiters === null) {
            $delimiters = $this->app['config']->get(
                'twigbridge::delimiters',
                array(
                    'tag_comment'  => array('{#', '#}'),
                    'tag_block'    => array('{%', '%}'),
                    'tag_variable' => array('{{', '}}'),
                )
            );
        }

        $lexer = new Twig\Lexer(
            (isset($delimiters['tag_comment'])) ? $delimiters['tag_comment'] : array(),
            (isset($delimiters['tag_block'])) ? $delimiters['tag_block'] : array(),
            (isset($delimiters['tag_variable'])) ? $delimiters['tag_variable'] : array()
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
        $loader = new Twig\Loader\Filesystem($this->app['view']->getFinder(), $this->extension);
        $twig   = new Twig_Environment($loader, $this->options);

        // Load extensions
        foreach ($this->getExtensions() as $twig_extension) {

            // Get an instance of the extension
            // Support for string, closure and an object
            if (is_string($twig_extension)) {
                $twig_extension = new $twig_extension($this->app, $twig);
            } elseif (is_callable($twig_extension)) {
                $twig_extension = $twig_extension($this->app, $twig);
            } elseif (!is_object($twig_extension)) {
                throw new InvalidArgumentException('Incorrect extension type');
            }

            // Add extension to twig
            $twig->addExtension($twig_extension);
        }

        $this->app['events']->fire('twigbridge.twig', array('twig' => $twig));

        // Allow template tags to be changed
        $twig->setLexer($this->getLexer($twig));

        return $twig;
    }
}
