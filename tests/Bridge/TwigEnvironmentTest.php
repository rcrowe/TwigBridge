<?php

namespace TwigBridge\Tests\Bridge;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Bridge;

class TwigEnvironmentTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testNoParams()
    {
        $twig = m::mock('Twig_Environment');
        $twig->shouldReceive('getExtensions');

        $app = $this->getApplication();
        $app->instance('twig', $twig);

        $bridge = new Bridge($app);

        $bridge->getExtensions();
    }

    public function testMultipleParams()
    {
        for ($i = 0; $i <= 6; $i++) {
            $twig = m::mock('Twig_Environment');
            $twig->shouldReceive('getExtensions');

            $app = $this->getApplication();
            $app->instance('twig', $twig);

            $bridge = new Bridge($app);

            call_user_func_array(array($bridge, 'getExtensions'), range(0, $i));
        }
    }
}
