<?php

namespace TwigBridge\Tests\Extension\Loader\Facade;

use Illuminate\Support\Facades\Config;
use Twig\Markup;
use TwigBridge\Tests\Base;
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

    public function testSafeMagicMethods(): void
    {
        $app = $this->getApplication();
        Config::setFacadeApplication($app);
        $caller = new Caller(Config::class, ['is_safe' => true]);

        $this->assertNotInstanceOf(Markup::class, $caller->all());
    }

    public function testUnsafeMagicMethods(): void
    {
        $app = $this->getApplication();
        Config::setFacadeApplication($app);
        $caller = new Caller(Config::class, ['is_safe' => true]);

        $this->assertInstanceOf(Markup::class, $caller->get('twigbridge.twig.extension'));
    }
}
