<?php

namespace TwigBridge\Tests\Extension\Laravel;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Laravel\Auth;

class AuthTest extends Base
{
    public function tearDown(): void
    {
        m::close();
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getAuth()->getName());
    }

    public function testFunctions()
    {
        $auth      = $this->getAuth();
        $functions = $auth->getFunctions();

        $this->assertInternalType('array', $functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf('Illuminate\Auth\AuthManager', $function->getCallable()[0]);
        }
    }

    protected function getAuth()
    {
        return new Auth(m::mock('Illuminate\Auth\AuthManager'));
    }
}
