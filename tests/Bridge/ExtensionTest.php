<?php

namespace TwigBridge\Tests\Bridge;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Bridge;
use Twig_Extension_Debug;

class ExtensionTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testString()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('addExtension')->with(m::on(function ($param) {
            return (is_a($param, 'Twig_Extension_Debug'));
        }));

        $app = $this->getApplication();
        $app->instance('twig', $twig);

        $bridge = new Bridge($app);

        $bridge->addExtension('Twig_Extension_Debug');
    }

    public function testStringArray()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('addExtension')->with(m::on(function ($param) {
            return (is_a($param, 'Twig_Extension_Debug'));
        }));

        $app = $this->getApplication();
        $app->instance('twig', $twig);

        $bridge = new Bridge($app);

        $bridge->addExtension(array(
            'Twig_Extension_Debug'
        ));
    }

    public function testCallable()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('addExtension')->with(m::on(function ($param) {
            return (is_a($param, 'Twig_Extension_Debug'));
        }));

        $app = $this->getApplication();
        $app->instance('twig', $twig);

        $bridge = new Bridge($app);

        $bridge->addExtension(function () {
            return new Twig_Extension_Debug;
        });
    }

    public function testCallableArray()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('addExtension')->with(m::on(function ($param) {
            return (is_a($param, 'Twig_Extension_Debug'));
        }));

        $app = $this->getApplication();
        $app->instance('twig', $twig);

        $bridge = new Bridge($app);

        $bridge->addExtension(array(
            function () {
                return new Twig_Extension_Debug;
            }
        ));
    }

    public function testInstance()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('addExtension')->with(m::on(function ($param) {
            return (is_a($param, 'Twig_Extension_Debug'));
        }));

        $app = $this->getApplication();
        $app->instance('twig', $twig);

        $bridge = new Bridge($app);

        $bridge->addExtension(new Twig_Extension_Debug);
    }

    public function testInstanceArray()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('addExtension')->with(m::on(function ($param) {
            return (is_a($param, 'Twig_Extension_Debug'));
        }));

        $app = $this->getApplication();
        $app->instance('twig', $twig);

        $bridge = new Bridge($app);

        $bridge->addExtension(array(
            new Twig_Extension_Debug
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidInstance()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('addExtension')->with(m::on(function ($param) {
            return (is_a($param, 'Twig_Extension_Debug'));
        }));

        $app = $this->getApplication();
        $app->instance('twig', $twig);

        $bridge = new Bridge($app);

        $bridge->addExtension($bridge);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidInstanceArray()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('addExtension')->with(m::on(function ($param) {
            return (is_a($param, 'Twig_Extension_Debug'));
        }));

        $app = $this->getApplication();
        $app->instance('twig', $twig);

        $bridge = new Bridge($app);

        $bridge->addExtension(array(
            $bridge
        ));
    }
}
