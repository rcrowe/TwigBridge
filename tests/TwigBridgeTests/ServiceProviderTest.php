<?php

namespace TwigBridgeTests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\View\Environment;
use Illuminate\Config\Repository;

use TwigBridge\TwigServiceProvider;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testBound()
    {
        $bindings = array(
            'twig.extensions',
            'twig.loader.path',
            'twig.loader.viewfinder',
            'twig.loader.filesystem',
            'twig.loader',
            'twig.options',
            'twig.bridge',
            'twig',
            'twig.engine',
        );

        $app      = $this->getApplication();
        $provider = new TwigServiceProvider($app);

        // Make sure not found
        foreach ($bindings as $binding) {
            $this->assertFalse($app->bound($binding));
        }

        // Boot provider
        $provider->boot();

        // Now make sure bounded
        foreach ($bindings as $binding) {
            $this->assertTrue($app->bound($binding));
        }
    }

    protected function getApplication()
    {
        $app = new Application;
        $app->instance('path', __DIR__);

        $app['env']          = 'production';
        $app['path.storage'] = __DIR__.'/storage';

        // View
        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');

        $engine = m::mock('Illuminate\View\Engines\EngineResolver');
        $engine->shouldReceive('register');

        $app['view'] = new Environment(
            $engine,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        // Config
        $config = new Repository(m::mock('Illuminate\Config\LoaderInterface'), 'production');

        $config->getLoader()->shouldReceive('addNamespace')->with('twigbridge', __DIR__);
        $config->getLoader()->shouldReceive('cascadePackage')->andReturnUsing(
            function ($env, $package, $group, $items) {
                return $items;
            }
        );
        $config->getLoader()->shouldReceive('exists')->with('extension', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('extensions', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('delimiters', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('twig', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->with('production', 'config', 'twigbridge')->andReturn(
            array(
                'extension'  => 'twig',
                'twig'       => array(),
                'extensions' => array(
                    'TwigBridge\Extension\Dummy',
                )
            )
        );

        $config->package('foo/twigbridge', __DIR__);
        $app['config'] = $config;

        return $app;
    }
}
