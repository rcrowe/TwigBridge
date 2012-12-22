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
        $bridge = new TwigBridge($this->getApplication(array('cache' => '/hello/world')));
        $options = $bridge->getOptions();

        $this->assertEquals($options['cache'], '/hello/world');
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
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'twig', 'twigbridge')->andReturn($twig_options);

        $app['config'] = $config;

        return $app;
    }
}