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

    public function testInstance()
    {
        $this->assertInstanceOf('Twig_Environment', new Bridge);
    }
}
