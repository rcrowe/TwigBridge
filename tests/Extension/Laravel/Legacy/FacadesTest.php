<?php

namespace TwigBridge\Tests\Extension\Laravel\Legacy;

use TwigBridge\Tests\Base;
use Mockery as m;
use Illuminate\Config\Repository;
use Twig_Environment;
use TwigBridge\Extension\Laravel\Legacy\Facades;

class FacadesTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getFacade()->getName());
    }

    public function testUndefinedHandlerRegistered()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('registerUndefinedFunctionCallback')->with(m::on(function ($callback) {
            return !call_user_func($callback, 'fooBar');
        }));

        $this->getFacade($twig);
    }

    public function testAliases()
    {
        $facade  = $this->getFacade();
        $aliases = [
            'FOO_BAR' => 'hello_world',
        ];

        $this->assertEquals([], $facade->getAliases());
        $facade->setAliases($aliases);

        $aliases = array_change_key_case($aliases, CASE_LOWER);
        $this->assertEquals($aliases, $facade->getAliases());
    }

    public function testShortcuts()
    {
        $facade    = $this->getFacade();
        $shortcuts = [
            'FOO_BAR' => 'HELLO_WORLD',
        ];

        $this->assertEquals([], $facade->getShortcuts());
        $facade->setShortcuts($shortcuts);

        $lowered = [];
        foreach ($shortcuts as $key => $value) {
            $lowered[strtolower($key)] = strtolower($value);
        }

        $this->assertEquals($lowered, $facade->getShortcuts());
    }

    public function testAliasParts()
    {
        $facade = $this->getFacade();

        $this->assertFalse($facade->getAliasParts('foo'));
        $this->assertFalse($facade->getAliasParts('foo_'));
        $this->assertFalse($facade->getAliasParts('_foo'));

        $this->assertEquals(['foo', 'bar'], $facade->getAliasParts('foo_bar'));
        $this->assertEquals(['foo', 'bar_baz'], $facade->getAliasParts('foo_bar_baz'));
    }

    public function testLookup()
    {
        $facade = $this->getFacade();

        $this->assertFalse($facade->getLookup('FOO'));
        $facade->setLookup('FoO', m::mock('Twig_Function_Function'));
        $this->assertInstanceOf('Twig_Function_Function', $facade->getLookup('foo'));
    }

    public function testFunctionLookup()
    {
        $facade = $this->getFacade();

        $this->assertFalse($facade->getFunction('foo'));
        $facade->setLookup('FOO', m::mock('Twig_Function_Function'));
        $this->assertInstanceOf('Twig_Function_Function', $facade->getFunction('foo'));
    }

    public function testFunctionNotAliased()
    {
        $facade = $this->getFacade();

        $this->assertFalse($facade->getFunction('foo_bar'));
    }

    public function testGetFunction()
    {
        $aliases = [
            'foo' => 'Baz',
        ];

        $facade = $this->getFacade();
        $facade->setAliases($aliases);

        $this->assertFalse($facade->getLookup('foo_bar'));

        $result = $facade->getFunction('foo_bar');
        $this->assertInstanceOf('Twig_Function_Function', $result);
        $this->assertEquals('Baz::bar', $result->compile());

        // Check lookup now set
        $this->assertInstanceOf('Twig_Function_Function', $facade->getFunction('foo_bar'));
    }

    protected function getFacade(Twig_Environment $twig = null)
    {
        $app = $this->getApplication();

        $app['twig'] = $twig;

        if (!$twig) {
            $app['twig'] = m::mock('Twig_Environment');
            $app['twig']->shouldReceive('registerUndefinedFunctionCallback');
        }

        return new Facades($app, $app['config']);
    }
}
