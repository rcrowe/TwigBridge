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

    public function testInstance()
    {
        $bridge = new TwigBridge($this->getApplication());

        $paths = $bridge->getPaths();
        $this->assertTrue(count($paths) === 1);
        $this->assertEquals($paths[0], __DIR__.'/fixtures/');

        $options = $bridge->getOptions();
        $this->assertTrue(count($options) === 2);
        $this->assertEquals($options['egg'], 'fried');
        $this->assertEquals($options['cache'], __DIR__.'/storage/views/twig');
    }

    public function testSetOptions()
    {
        // $bridge = new TwigBridge($this->getApplication());
        // $options = $bridge->getOptions();

        // $this->assertEquals($options['cache'], '/hello/world');
    }

    public function testCachePathAlreadySet()
    {
        $bridge = new TwigBridge($this->getApplication(array('cache' => '/hello/world')));
        $options = $bridge->getOptions();

        $this->assertEquals($options['cache'], '/hello/world');
    }

    public function testGetExtension()
    {

    }

    public function testSetExtension()
    {

    }

    public function testGetTwig()
    {

    }

    public function getApplication(array $twig_options = array())
    {
        $app = new Application;
        $app->instance('path', __DIR__);

        $config  = new Repository(m::mock('Illuminate\Config\LoaderInterface'), 'production');

        $view_options = array(
            'paths' => array(__DIR__.'/fixtures/')
        );
        $twig_options OR $twig_options = array(
                             'egg' => 'fried'
                         );

        $config->getLoader()->shouldReceive('load')->once()->with('production', 'view', null)->andReturn($view_options);
        $config->getLoader()->shouldReceive('addNamespace')->with('twigbridge', __DIR__);
        $config->getLoader()->shouldReceive('cascadePackage')->andReturnUsing(function($env, $package, $group, $items) { return $items; });
        $config->getLoader()->shouldReceive('exists')->once()->with('extension', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->once()->with('twig', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'config', 'twigbridge')->andReturn(
            array('extension' => 'twig', 'twig' => $twig_options)
        );

        $config->package('foo/twigbridge', __DIR__);

        $app['config'] = $config;

        return $app;
    }
}