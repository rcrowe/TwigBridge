<?php

namespace TwigBridgeTests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\View\Environment;
use Illuminate\Config\Repository;

abstract class Base extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    protected function getApplication(array $customConfig = array())
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

        // Get config data
        $configData     = include __DIR__.'/../../src/config/config.php';
        $extensionsData = include __DIR__.'/../../src/config/extensions.php';
        $extensionsData = array(
            'extensions' => $extensionsData,
        );
        $configData = array_replace_recursive($configData, $extensionsData, $customConfig);

        $config->getLoader()->shouldReceive('load')->with('production', 'config', 'twigbridge')->andReturn($configData);

        $config->package('foo/twigbridge', __DIR__);
        $app['config'] = $config;

        return $app;
    }
}
