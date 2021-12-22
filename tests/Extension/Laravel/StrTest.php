<?php

namespace TwigBridge\Tests\Extension\Laravel;

use Mockery as m;
use Twig\Node\Node;
use TwigBridge\Extension\Laravel\Str;
use TwigBridge\Tests\Base;

class StrTest extends Base
{
    protected $customFilters = [
        'camel_case',
        'snake_case',
        'studly_case',
    ];

    public function tearDown(): void
    {
        m::close();
    }

    public function testCallback()
    {
        $string = $this->getString();

        $this->assertEquals('Illuminate\Support\Str', $string->getCallback());
        $string->setCallback('FooBar');
        $this->assertEquals('FooBar', $string->getCallback());
    }

    public function testName()
    {
        $this->assertTrue(is_string($this->getString()->getName()));
    }

    public function testFunctionCallback()
    {
        $mock = m::mock('Illuminate\Support\Str');
        $mock->shouldReceive('fooBar')->once();

        $string = $this->getString();
        $string->setCallback($mock);

        $this->assertTrue(is_array($string->getFunctions()));

        call_user_func($string->getFunctions()[0]->getCallable(), 'foo_bar');
    }

    public function testFunctionIsNotSafe()
    {
        $string   = $this->getString();
        $function = $string->getFunctions()[0];

        $this->assertFalse(in_array('html', $function->getSafe(m::mock(Node::class))));
    }

    public function testCustomFilters()
    {
        $string  = $this->getString();
        $filters = $string->getFilters();

        $this->assertTrue(is_array($filters));

        foreach ($filters as $filter) {
            if (!in_array($filter->getName(), $this->customFilters)) {
                continue;
            }

            $this->assertEquals('Illuminate\Support\Str', $filter->getCallable()[0]);
        }
    }

    public function testWildcardFilters()
    {
        $mock = m::mock('Illuminate\Support\Str');
        $mock->shouldReceive('fooBar')->once();

        $string  = $this->getString();
        $string->setCallback($mock);
        $filters = $string->getFilters();

        foreach ($filters as $filter) {
            if (in_array($filter->getName(), $this->customFilters)) {
                continue;
            }

            call_user_func($filter->getCallable(), 'foo_bar');
        }
    }


    protected function getString()
    {
        return new Str;
    }
}
