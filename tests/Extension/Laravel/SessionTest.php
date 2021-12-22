<?php

namespace TwigBridge\Tests\Extension\Laravel;

use Mockery as m;
use Twig\Node\Node;
use TwigBridge\Extension\Laravel\Session;
use TwigBridge\Tests\Base;

class SessionTest extends Base
{
    public function tearDown(): void
    {
        m::close();
    }

    public function testName()
    {
        $this->assertTrue(is_string($this->getSession()->getName()));
    }

    public function testFunctions()
    {
        $session   = $this->getSession();
        $functions = $session->getFunctions();

        $this->assertTrue(is_array($functions));

        foreach ($functions as $function) {
            if (is_array($function->getCallable())) {
                $this->assertInstanceOf('Illuminate\Session\Store', $function->getCallable()[0]);
            }
        }
    }

    public function testIsSafe()
    {
        $session   = $this->getSession();
        $functions = $session->getFunctions();
        $node      = m::mock(Node::class);
        $check     = [
            'csrf_token'
        ];

        foreach ($functions as $function) {
            if (!in_array($function->getName(), $check)) {
                continue;
            }

            $this->assertTrue(in_array('html', $function->getSafe($node)));
        }
    }

    protected function getSession()
    {
        return new Session(m::mock('Illuminate\Session\Store'));
    }
}
