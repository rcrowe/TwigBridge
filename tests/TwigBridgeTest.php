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

    public function testViewPath()
    {
        var_dump( new TwigBridge($this->getApplication()) );
    }

    public function getApplication()
    {
        $app = new Application;

        $config  = new Repository(m::mock('Illuminate\Config\LoaderInterface'), 'production');
        $options = array('foo' => 'bar', 'baz' => array('boom' => 'breeze'));
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'app', null)->andReturn($options);

        die(var_dump( $config->get('app.foo') ));

        // $app['config'] = $config;

        return $app;
    }
}