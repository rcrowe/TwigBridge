<?php

namespace TwigBridgeTests\Extensions;

use PHPUnit_Framework_TestCase;
use TwigBridge\Extensions\AliasLoader;
use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Twig_Environment;

class AliasLoaderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $loader    = $this->getLoader();
        $aliases   = $loader->getAliases();
        $shortcuts = $loader->getShortcuts();

        $alias_keys = array_keys($aliases);
        $this->assertTrue(ctype_lower($alias_keys[0]));
        $this->assertEquals($aliases['auth'], 'Illuminate\\Support\\Facades\\Auth');

        $shortcut_keys = array_keys($shortcuts);
        $this->assertTrue(ctype_lower($shortcut_keys[0]));
        $this->assertEquals($shortcuts['url'], 'url_to');
    }

    public function testShortcut()
    {
        $loader = $this->getLoader();

        $this->assertEquals($loader->getShortcut('URL'), 'url_to');
        $this->assertEquals($loader->getShortcut('not_found_test'), 'not_found_test');
    }

    public function testAliasPart()
    {
        $loader = $this->getLoader();

        $this->assertFalse($loader->getAliasParts('test'));
        $this->assertFalse($loader->getAliasParts('_test'));
        $this->assertFalse($loader->getAliasParts('test_'));

        $parts = $loader->getAliasParts('url_to');
        $this->assertTrue(is_array($parts));
        $this->assertTrue(count($parts) === 2);
        $this->assertEquals('url', $parts[0]);
        $this->assertEquals('to', $parts[1]);
    }

    public function testTwigFunction()
    {
        $loader = $this->getLoader();

        $this->assertEquals('Twig_Function_Function', get_class($loader->getFunction('auth_check')));
        $this->assertEquals('Illuminate\\Support\\Facades\\Auth::check', $loader->getFunction('auth_check')->getCallable());
    }

    public function testTwigFunctionNotFound()
    {
        $loader = $this->getLoader();

        $this->assertFalse($loader->getFunction('test_'));
        $this->assertFalse($loader->getFunction('test_rcrowe'));
    }

    public function testLookup()
    {
        $loader = $this->getLoader();

        $this->assertFalse($loader->getLookup('auth_check'));
        $loader->getFunction('auth_check');
        $this->assertEquals('Twig_Function_Function', get_class($loader->getFunction('auth_check')));
        $this->assertEquals('Illuminate\\Support\\Facades\\Auth::check', $loader->getFunction('auth_check')->getCallable());
    }

    public function testLookupSnakeCase()
    {
        $loader = $this->getLoader();

        $this->assertFalse($loader->getLookup('lookup_snake_case'));
        $loader->getFunction('lookup_snake_case');
        $this->assertEquals('Twig_Function_Function', get_class($loader->getFunction('lookup_snake_case')));
        $this->assertEquals('TwigBridgeTests\Fixtures\Extension\Lookup::snake_case', $loader->getFunction('lookup_snake_case')->getCallable());

        $this->assertFalse($loader->getLookup('lookup_snake_case_that_is_really_long'));
        $loader->getFunction('lookup_snake_case_that_is_really_long');
        $this->assertEquals('Twig_Function_Function', get_class($loader->getFunction('lookup_snake_case_that_is_really_long')));
        $this->assertEquals('TwigBridgeTests\Fixtures\Extension\Lookup::snake_case_that_is_really_long', $loader->getFunction('lookup_snake_case_that_is_really_long')->getCallable());
    }

    public function testLookupCamelCase()
    {
        $loader = $this->getLoader();

        $this->assertFalse($loader->getLookup('lookup_camelCase'));
        $loader->getFunction('lookup_camelCase');
        $this->assertEquals('Twig_Function_Function', get_class($loader->getFunction('lookup_camelCase')));
        $this->assertEquals('TwigBridgeTests\Fixtures\Extension\Lookup::camelCase', $loader->getFunction('lookup_camelCase')->getCallable());

        $this->assertFalse($loader->getLookup('lookup_camelCaseThatIsReallyLong'));
        $loader->getFunction('lookup_camelCaseThatIsReallyLong');
        $this->assertEquals('Twig_Function_Function', get_class($loader->getFunction('lookup_camelCaseThatIsReallyLong')));
        $this->assertEquals('TwigBridgeTests\Fixtures\Extension\Lookup::camelCaseThatIsReallyLong', $loader->getFunction('lookup_camelCaseThatIsReallyLong')->getCallable());
    }

    private function getLoader()
    {
        // Mock application
        $aliases = array(
            'aliases' => array(
                'Auth'   => 'Illuminate\Support\Facades\Auth',
                'Lookup' => 'TwigBridgeTests\Fixtures\Extension\Lookup',
            )
        );

        $shortcuts = array(
            'alias_shortcuts' => array(
                'URL'       => 'URL_TO',
                'logged_in' => 'auth_check',
            )
        );

        $app = new Application;
        $app->instance('path', __DIR__);

        $config = new Repository(m::mock('Illuminate\Config\LoaderInterface'), 'production');

        $config->getLoader()->shouldReceive('load')->once()->with('production', 'app', null)->andReturn($aliases);
        $config->getLoader()->shouldReceive('addNamespace')->with('twigbridge', __DIR__);
        $config->getLoader()->shouldReceive('cascadePackage')->andReturnUsing(function($env, $package, $group, $items) { return $items; });
        $config->getLoader()->shouldReceive('exists')->once()->with('alias_shortcuts', 'twigbridge')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'config', 'twigbridge')->andReturn($shortcuts);

        $config->package('foo/twigbridge', __DIR__);
        $app['config'] = $config;


        // Get instance of AliasLoader
        return new AliasLoader($app, new Twig_Environment);
    }
}