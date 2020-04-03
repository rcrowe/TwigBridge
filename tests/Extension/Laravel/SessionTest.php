<?php

namespace TwigBridge\Tests\Extension\Laravel;

use Twig\Node\Node;
use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Laravel\Session;

class SessionTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getSession()->getName());
    }

    public function testFunctions()
    {
        $session   = $this->getSession();
        $functions = $session->getFunctions();

        $this->assertInternalType('array', $functions);

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
