<?php

namespace TwigBridge\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\View\Factory;
use Illuminate\Config\Repository;
use Illuminate\View\Engines\EngineResolver;

abstract class Base extends PHPUnit_Framework_TestCase
{
    protected $twigBridgeRoot;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->twigBridgeRoot = realpath(__DIR__.'/../src');
    }

    public function tearDown()
    {
        m::close();
    }

    protected function getApplication(array $customConfig = [])
    {
        $app = new Application;
        $app->instance('path', __DIR__);

        $app['env']          = 'production';
        $app['path.storage'] = __DIR__.'/storage';

        // View
        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');

        $app['view'] = new Factory(
            new EngineResolver,
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
        $config->getLoader()->shouldReceive('exists')->with('twig', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('extensions', 'twigbridge')->andReturn(false);

        // Get config data
        $twigData = array(
            'twig' => array(
                'extension' => 'twig',
                'environment' => array(
                    'debug'               => false,
                    'charset'             => 'utf-8',
                    'base_template_class' => 'TwigBridge\Twig\Template',
                    'cache'               => null,
                    'auto_reload'         => true,
                    'strict_variables'    => false,
                    'autoescape'          => true,
                    'optimizations'       => -1,
                ),
                'globals' => array(),
            ),
        );

        $extensionsData = include $this->twigBridgeRoot.'/config/extensions.php';
        $extensionsData = array(
            'extensions' => $extensionsData,
        );

        $configData = array_replace_recursive($twigData, $extensionsData, $customConfig);
        $config->getLoader()->shouldReceive('load')->with('production', 'config', 'twigbridge')->andReturn($configData);

        $config->package('foo/twigbridge', __DIR__);
        $app['config'] = $config;

        $app->bind('Illuminate\Config\Repository', function () use ($app) {
            return $app['config'];
        });

        return $app;
    }
}
