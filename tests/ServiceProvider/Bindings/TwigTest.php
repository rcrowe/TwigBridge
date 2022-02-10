<?php

namespace TwigBridge\Tests\ServiceProvider\Bindings;

use Mockery as m;
use Illuminate\View\Factory;
use Illuminate\View\Engines\EngineResolver;
use Twig\Environment;
use Twig\Loader\ChainLoader;
use TwigBridge\Tests\Base;
use TwigBridge\ServiceProvider;

class TwigTest extends Base
{
    public function tearDown(): void
    {
        m::close();
    }

    public function testTwigOptions()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $config  = $app['config']->get('twigbridge.twig.environment');
        $options = $app['twig.options'];

        // Make sure that twig.options sets the storage path automatically
        $this->assertEmpty($config['cache']);
        $this->assertEquals(realpath(__DIR__.'/../..') . '/storage/framework/views/twig', $options['cache']);

        // Make sure same config is returned
        $options['cache'] = null;

        $this->assertSame($options, $config);
    }

    public function testExtension()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertEquals('twig', $app['twig.extension']);

        // Change extension
        $app = $this->getApplication([
            'twig' => ['extension' => 'twig.html'],
        ]);
        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertEquals('twig.html', $app['twig.extension']);
    }

    public function testExtensions()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertSame($app['twig.extensions'], $app['config']->get('twigbridge.extensions.enabled'));
    }

    public function testExtensionsWithDebug()
    {
        $app = $this->getApplication([
            'twig' => [
                'environment' => [
                    'debug' => true,
                ],
            ]
        ]);

        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertSame($app['twig.extensions'][0], \Twig\Extension\DebugExtension::class);
    }

    public function testLoaderViewfinder()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $app['twig.bridge'] = m::mock('stdClass');
        $app['twig.bridge']->shouldReceive('getExtension')->andReturn('twig');

        $this->assertInstanceOf('TwigBridge\Twig\Loader', $app['twig.loader.viewfinder']);
    }

    public function testLoaderChain()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        // View
        $engine = m::mock('Illuminate\View\Engines\EngineResolver');
        $engine->shouldReceive('register');

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');
        $finder->shouldReceive('getPaths')->andReturn([]);
        $finder->shouldReceive('getHints')->andReturn([]);

        $app['view'] = new Factory(
            $engine,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        // TwigBridge
        $app['twig.bridge'] = m::mock('stdClass');
        $app['twig.bridge']->shouldReceive('getExtension')->andReturn('twig');

        // Loader
        $this->assertInstanceOf(ChainLoader::class, $app['twig.loader']);
    }

    public function testTwig()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        // Extensions
        $app['twig.extensions'] = [];

        // View
        $engine = m::mock('Illuminate\View\Engines\EngineResolver');
        $engine->shouldReceive('register');

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');
        $finder->shouldReceive('getPaths')->andReturn([]);
        $finder->shouldReceive('getHints')->andReturn([]);

        $app['view'] = new Factory(
            $engine,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        $this->assertInstanceOf(Environment::class, $app['twig']);
    }

    public function testTwigEngine()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        // Extensions
        $app['twig.extensions'] = [];

        // View
        $engine = m::mock('Illuminate\View\Engines\EngineResolver');
        $engine->shouldReceive('register');

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');
        $finder->shouldReceive('getPaths')->andReturn([]);
        $finder->shouldReceive('getHints')->andReturn([]);

        $app['view'] = new Factory(
            $engine,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        $this->assertInstanceOf('TwigBridge\Engine\Twig', $app['twig.engine']);
    }

    public function testRegisteredEngine()
    {
        $app = $this->getApplication();

        // View
        $engine = new EngineResolver;

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');
        $finder->shouldReceive('getPaths')->andReturn([]);
        $finder->shouldReceive('getHints')->andReturn([]);

        $app['view'] = new Factory(
            $engine,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot();

        $app['twig.extensions'] = [];

        $this->assertInstanceOf('TwigBridge\Engine\Twig', $app['view']->getEngineResolver()->resolve('twig'));
    }
}
