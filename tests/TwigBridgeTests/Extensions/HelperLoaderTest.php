<?php

namespace TwigBridgeTests\Extensions;

use PHPUnit_Framework_TestCase;
use TwigBridge\Extensions\HelperLoader;
use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Twig_Environment;

class HelperLoaderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testGetFunctions()
    {
        $loader    = $this->getLoader();
        $functions = $loader->getFunctions();

        $this->assertTrue(is_array($functions));

        // array_get
        $this->assertInstanceOf('Twig_SimpleFunction', $functions[0]);
        $function = $functions[0];
        $result   = call_user_func_array($function->getCallable(), array(
            array(
                'user' => array(
                    'name' => 'TwigBridge',
                ),
            ),
            'user.name',
        ));
        $this->assertEquals($result, 'TwigBridge');

        // fooBar
        $this->assertInstanceOf('Twig_SimpleFunction', $functions[1]);
        $function = $functions[1];
        $result   = call_user_func_array($function->getCallable(), array());
        $this->assertEquals($result, 'FOOBAR');
    }

    public function testGetFilters()
    {
        $loader  = $this->getLoader();
        $filters = $loader->getFilters();

        $this->assertTrue(is_array($filters));

        // camel_case
        $this->assertInstanceOf('Twig_SimpleFilter', $filters[0]);
        $filter = $filters[0];
        $result = call_user_func_array($filter->getCallable(), array('convert_this_string'));
        $this->assertEquals($result, 'convertThisString');

        // snakeCase
        $this->assertInstanceOf('Twig_SimpleFilter', $filters[1]);
        $filter = $filters[1];
        $result = call_user_func_array($filter->getCallable(), array());
        $this->assertEquals($result, 'snakeCASE');
    }

    private function getLoader()
    {
        // Mock application
        $functions = array(
            'functions' => array(
                'array_get',
                'fooBar' => function() {
                    return 'FOOBAR';
                },
            )
        );

        $filters = array(
            'filters' => array(
                'camel_case',
                'snakeCase' => function() {
                    return 'snakeCASE';
                },
            )
        );

        $app = new Application;
        $app->instance('path', __DIR__);

        $config = new Repository(m::mock('Illuminate\Config\LoaderInterface'), 'production');

        $config->getLoader()->shouldReceive('addNamespace')->with('twigbridge', __DIR__);
        $config->getLoader()->shouldReceive('cascadePackage')->andReturnUsing(function($env, $package, $group, $items) { return $items; });
        $config->getLoader()->shouldReceive('exists')->with('functions', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('filters', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->with('production', 'config', 'twigbridge')->andReturn(array_merge(
            $functions,
            $filters
        ));

        $config->package('foo/twigbridge', __DIR__);
        $app['config'] = $config;


        // Get instance of HelperLoader
        return new HelperLoader($app, new Twig_Environment);
    }
}
