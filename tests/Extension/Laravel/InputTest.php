<?php

namespace TwigBridge\Tests\Extension\Laravel;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Laravel\Input;

class InputTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getInput()->getName());
    }

    public function testFunctions()
    {
        $input     = $this->getInput();
        $functions = $input->getFunctions();

        $this->assertInternalType('array', $functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf('Illuminate\Http\Request', $function->getCallable()[0]);
        }
    }

    protected function getInput()
    {
        return new Input(m::mock('Illuminate\Http\Request'));
    }
}
