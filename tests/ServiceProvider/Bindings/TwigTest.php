<?php

namespace TwigBridge\Tests\ServiceProvider\Bindings;

use Mockery as m;
use Illuminate\View\Factory;
use TwigBridge\Tests\Base;
use TwigBridge\ServiceProvider;

class TwigTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testTwigOptions()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $config  = $app['config']->get('twigbridge::twig.environment');
        $options = $app['twig.options'];

        // Make sure that twig.options sets the storage path automatically
        $this->assertEmpty($config['cache']);
        $this->assertEquals($options['cache'], realpath(__DIR__.'/../..').'/storage/views/twig');

        // Make sure same config is returned
        $options['cache'] = null;

        $this->assertSame($options, $config);
    }

    public function testExtension()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $this->assertEquals('twig', $app['twig.extension']);

        // Change extension
        $app = $this->getApplication(array(
            'twig' => array('extension' => 'twig.html'),
        ));
        $provider = new ServiceProvider($app);
        $provider->boot();

        $this->assertEquals('twig.html', $app['twig.extension']);
    }

    public function testExtensions()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $this->assertSame($app['twig.extensions'], $app['config']->get('twigbridge::extensions.enabled'));
    }

    public function testExtensionsWithDebug()
    {
        $app = $this->getApplication(array(
            'twig' => array(
                'environment' => array(
                    'debug' => true,
                ),
            )
        ));

        $provider = new ServiceProvider($app);
        $provider->boot();

        $this->assertSame($app['twig.extensions'][0], 'Twig_Extension_Debug');
    }

    public function testLoaderPath()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $this->assertInstanceOf('TwigBridge\Twig\Loader\Path', $app['twig.loader.path']);
    }

    public function testLoaderViewfinder()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $app['twig.bridge'] = m::mock('stdClass');
        $app['twig.bridge']->shouldReceive('getExtension')->andReturn('twig');

        $this->assertInstanceOf('TwigBridge\Twig\Loader\Viewfinder', $app['twig.loader.viewfinder']);
    }

    public function testLoaderChain()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        // View
        $engine = m::mock('Illuminate\View\Engines\EngineResolver');
        $engine->shouldReceive('register');

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');
        $finder->shouldReceive('getPaths')->andReturn(array());
        $finder->shouldReceive('getHints')->andReturn(array());

        $app['view'] = new Factory(
            $engine,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        // TwigBridge
        $app['twig.bridge'] = m::mock('stdClass');
        $app['twig.bridge']->shouldReceive('getExtension')->andReturn('twig');

        // Loader
        $this->assertInstanceOf('Twig_Loader_Chain', $app['twig.loader']);
    }

    public function testTwigBridge()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        $this->assertInstanceOf('TwigBridge\Bridge', $app['twig.bridge']);
    }

    public function testTwig()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        // Extensions
        $app['twig.extensions'] = array();

        // View
        $engine = m::mock('Illuminate\View\Engines\EngineResolver');
        $engine->shouldReceive('register');

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');
        $finder->shouldReceive('getPaths')->andReturn(array());
        $finder->shouldReceive('getHints')->andReturn(array());

        $app['view'] = new Factory(
            $engine,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        $this->assertInstanceOf('Twig_Environment', $app['twig']);
    }

    public function testTwigEngine()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);
        $provider->boot();

        // Extensions
        $app['twig.extensions'] = array();

        // View
        $engine = m::mock('Illuminate\View\Engines\EngineResolver');
        $engine->shouldReceive('register');

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');
        $finder->shouldReceive('getPaths')->andReturn(array());
        $finder->shouldReceive('getHints')->andReturn(array());

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
        $engine = new \Illuminate\View\Engines\EngineResolver;

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');
        $finder->shouldReceive('getPaths')->andReturn(array());
        $finder->shouldReceive('getHints')->andReturn(array());

        $app['view'] = new Factory(
            $engine,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        $provider = new ServiceProvider($app);
        $provider->boot();

        $app['twig.extensions'] = array();


        $this->assertInstanceOf('TwigBridge\Engine\Twig', $app['view']->getEngineResolver()->resolve('twig'));
    }
}
