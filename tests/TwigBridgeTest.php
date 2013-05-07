<?php

namespace TwigBridgeTests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use TwigBridge\TwigBridge;
use TwigBridge\Twig\Loader\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\View\Environment;
use Twig_Environment;
use Twig_Lexer;
use ReflectionProperty;
use InvalidArgumentException;
use Exception;

class TwigBridgeTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testSetTwigOptionsNoCachePathInstance()
    {
        $bridge  = new TwigBridge($this->getApplication());
        $options = $bridge->getTwigOptions();

        $this->assertEquals($options['cache'], __DIR__.'/storage/views/twig');
        $this->assertEquals($options['egg'], 'fried');
    }

    public function testSetTwigOptionsWithCachePath()
    {
        $bridge  = new TwigBridge($this->getApplication(array('cache' => 't/e/s/t')));
        $options = $bridge->getTwigOptions();

        $this->assertEquals($options['cache'], 't/e/s/t');
    }

    public function testGetExtension()
    {
        $bridge = new TwigBridge($this->getApplication());
        $this->assertEquals($bridge->getExtension(), 'twig');
    }

    public function testSetExtension()
    {
        $bridge = new TwigBridge($this->getApplication());
        $bridge->setExtension('twig.html');
        $this->assertEquals($bridge->getExtension(), 'twig.html');
    }

    public function testGetExtensions()
    {
        $bridge = new TwigBridge($this->getApplication());
        $extensions = $bridge->getExtensions();
        $this->assertEquals($extensions[0], 'TwigBridge\\Extensions\\Html');
    }

    public function testSetExtensions()
    {
        $bridge = new TwigBridge($this->getApplication());
        $bridge->setExtensions(array('TwigBridge\\Extensions\\Dummy'));
        $extensions = $bridge->getExtensions();
        $this->assertTrue(count($extensions) === 1);
        $this->assertEquals($extensions[0], 'TwigBridge\\Extensions\\Dummy');
    }

    public function testGetLexer()
    {
        $bridge = new TwigBridge($this->getApplication());
        $lexer  = $bridge->getLexer(new Twig_Environment);

        $this->assertEquals(get_class($lexer), 'Twig_Lexer');
    }

    public function testGetLexerWithCustomDelimiters()
    {
        $bridge     = new TwigBridge($this->getApplication());
        $delimiters = array(
            'tag_comment'  => array('{@', '@}'),
            'tag_block'    => array('{#', '#}'),
            'tag_variable' => array('{/', '/}'),
        );

        $lexer = $bridge->getLexer(new Twig_Environment, $delimiters);

        $prop = new ReflectionProperty('Twig_Lexer', 'options');
        $prop->setAccessible(true);
        $options = $prop->getValue($lexer);

        // Comment
        $this->assertEquals('{@', $options['tag_comment'][0]);
        $this->assertEquals('@}', $options['tag_comment'][1]);

        // Block
        $this->assertEquals('{#', $options['tag_block'][0]);
        $this->assertEquals('#}', $options['tag_block'][1]);

        // Variable
        $this->assertEquals('{/', $options['tag_variable'][0]);
        $this->assertEquals('/}', $options['tag_variable'][1]);
    }

    public function testGetLexerWithoutTwig()
    {
        $bridge = new TwigBridge($this->getApplication());

        try {
            $bridge->getLexer();
            $this->assertFalse(true);
        } catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        } catch (Exception $ex) {
            $this->assertFalse(true);
        }
    }

    public function testSetLexer()
    {
        $bridge     = new TwigBridge($this->getApplication());
        $delimiters = array(
            'tag_comment'  => array('*', '*/'),
            'tag_block'    => array('%', '%/'),
            'tag_variable' => array('!', '!/'),
        );

        $bridge->setLexer(new Twig_Lexer(new Twig_Environment, $delimiters));
        $lexer = $bridge->getLexer();

        $prop = new ReflectionProperty('Twig_Lexer', 'options');
        $prop->setAccessible(true);
        $options = $prop->getValue($lexer);

        // Comment
        $this->assertEquals('*', $options['tag_comment'][0]);
        $this->assertEquals('*/', $options['tag_comment'][1]);

        // Block
        $this->assertEquals('%', $options['tag_block'][0]);
        $this->assertEquals('%/', $options['tag_block'][1]);

        // Variable
        $this->assertEquals('!', $options['tag_variable'][0]);
        $this->assertEquals('!/', $options['tag_variable'][1]);
    }

    public function testGetTwigExtensionLoading()
    {
        $mockAliasLoader = m::mock('TwigBridge\Extensions\AliasLoader');
        $mockAliasLoader->shouldReceive('getName')->twice()->andReturn('AliasLoader');
        $mockAliasLoader->shouldReceive('getFilters')->once()->andReturn(array());
        $mockAliasLoader->shouldReceive('getFunctions')->once()->andReturn(array());
        $mockAliasLoader->shouldReceive('getTests')->once()->andReturn(array());
        $mockAliasLoader->shouldReceive('getTokenParsers')->once()->andReturn(array());
        $mockAliasLoader->shouldReceive('getNodeVisitors')->once()->andReturn(array());
        $mockAliasLoader->shouldReceive('getOperators')->once()->andReturn(array());

        $bridge = new TwigBridge($this->getApplication());
        $bridge->setExtensions(array(
            $mockAliasLoader,
            function() use($mockAliasLoader) {
                return $mockAliasLoader;
            },
        ));

        $bridge->getTwig();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnsupportedExceptionType()
    {
        $bridge = new TwigBridge($this->getApplication());
        $bridge->setExtensions(array(12345));

        $bridge->getTwig();
    }

    public function getApplication(array $twig_options = array(), array $paths = array(), array $hints = array())
    {
        $app = new Application;
        $app->instance('path', __DIR__);

        $app['path.storage'] = __DIR__.'/storage';

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('getPaths')->andReturn($paths);
        $finder->shouldReceive('getHints')->andReturn($hints);

        $app['view'] = new Environment(
            m::mock('Illuminate\View\Engines\EngineResolver'),
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        $config = new Repository(m::mock('Illuminate\Config\LoaderInterface'), 'production');
        $twig_options OR $twig_options = array('egg' => 'fried');

        $config->getLoader()->shouldReceive('addNamespace')->with('twigbridge', __DIR__);
        $config->getLoader()->shouldReceive('cascadePackage')->andReturnUsing(function($env, $package, $group, $items) { return $items; });
        $config->getLoader()->shouldReceive('exists')->with('extension', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('extensions', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('delimiters', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('twig', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->with('production', 'config', 'twigbridge')->andReturn(
            array(
                'extension'  => 'twig',
                'twig'       => $twig_options,
                'extensions' => array(
                    'TwigBridge\Extensions\Html',
                )
            )
        );

        $config->package('foo/twigbridge', __DIR__);

        $app['config'] = $config;

        return $app;
    }
}