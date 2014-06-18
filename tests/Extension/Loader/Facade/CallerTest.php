<?php

namespace TwigBridge\Tests\Extension\Loader\Facade;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Loader\Facade\Caller;

class CallerTest extends Base
{
    public function testInstance()
    {
        $caller = new Caller('foo', ['name' => 'Rob']);

        $this->assertEquals($caller->getFacade(), 'foo');
        $this->assertEquals($caller->getOptions(), [
            'is_safe' => null,
            'charset' => null,
            'name'    => 'Rob',
        ]);
    }
}
