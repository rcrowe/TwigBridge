<?php

namespace TwigBridge\Tests\Extension\Laravel;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Laravel\Html;

class HtmlTest extends Base
{
    protected $customFunctions = [
        'link_to',
        'link_to_asset',
        'link_to_route',
        'link_to_action',
    ];

    public function tearDown()
    {
        m::close();
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getHtml()->getName());
    }

    public function testCustomFunctions()
    {
        $html      = $this->getHtml();
        $functions = $html->getFunctions();

        $this->assertInternalType('array', $functions);

        foreach ($functions as $function) {
            if (!in_array($function->getName(), $this->customFunctions)) {
                continue;
            }

            $this->assertInstanceOf('Illuminate\Html\HtmlBuilder', $function->getCallable()[0]);
        }
    }

    public function testWildcardFunctions()
    {
        $builder = m::mock('Illuminate\Html\HtmlBuilder');
        $builder->shouldReceive('fooBar')->once();

        $html      = new Html($builder);
        $functions = $html->getFunctions();

        foreach ($functions as $function) {
            if (in_array($function->getName(), $this->customFunctions)) {
                continue;
            }

            call_user_func($function->getCallable(), 'foo_bar');
        }
    }

    public function testIsSafe()
    {
        $html      = $this->getHtml();
        $functions = $html->getFunctions();
        $mock      = m::mock('Twig_Node');

        foreach ($functions as $function) {
            $this->assertTrue(in_array('html', $function->getSafe($mock)));
        }
    }

    protected function getHtml()
    {
        return new Html(m::mock('Illuminate\Html\HtmlBuilder'));
    }
}
