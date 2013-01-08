<?php

use Mockery as m;
use TwigBridge\TwigBridge;
use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;

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

    public function testGetPathsMergeHints()
    {
        $paths = array(__DIR__.'/views');
        $hints = array(
            array(
                __DIR__.'/test/path',
                __DIR__.'/path/test'
            )
        );

        $bridge = new TwigBridge($this->getApplication(array(), $paths, $hints));
        $paths  = $bridge->getPaths();

        $this->assertTrue(count($paths) === 3);
    }

    public function testGetPathsMergeCustom()
    {
        $bridge = new TwigBridge($this->getApplication());
        $this->assertTrue(count($bridge->getPaths()) === 0);

        $paths = $bridge->getPaths(array(__DIR__.'/test'));
        $this->assertTrue(count($paths) === 1);
        $this->assertEquals($paths[0], __DIR__.'/test');
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

    }

    public function testSetLexer()
    {

    }

    public function testGetTwig()
    {

    }

    public function getApplication(array $twig_options = array(), array $paths = array(), array $hints = array())
    {
        $app = new Application;
        $app->instance('path', __DIR__);

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->paths = $paths;
        $finder->hints = $hints;
        // $finder->shouldReceive('getPaths')->andReturn($paths);
        // $finder->shouldReceive('getHints')->andReturn($hints);

        $app['view'] = new Illuminate\View\Environment(
            m::mock('Illuminate\View\Engines\EngineResolver'),
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        $config = new Repository(m::mock('Illuminate\Config\LoaderInterface'), 'production');
        $twig_options OR $twig_options = array('egg' => 'fried');

        $config->getLoader()->shouldReceive('addNamespace')->with('twigbridge', __DIR__);
        $config->getLoader()->shouldReceive('cascadePackage')->andReturnUsing(function($env, $package, $group, $items) { return $items; });
        $config->getLoader()->shouldReceive('exists')->once()->with('extension', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->once()->with('extensions', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->once()->with('twig', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'config', 'twigbridge')->andReturn(
            array(
                'extension'  => 'twig',
                'twig'       => $twig_options,
                'extensions' => array(
                    'TwigBridge\Extensions\Html',
                ),
            )
        );

        $config->package('foo/twigbridge', __DIR__);

        $app['config'] = $config;

        return $app;
    }
}