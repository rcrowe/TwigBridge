<?php

namespace TwigBridge\Tests\Extension\Laravel;

use Mockery as m;
use Twig\Node\Node;
use TwigBridge\Extension\Laravel\Html;
use TwigBridge\Tests\Base;

class HtmlTest extends Base
{
    protected $customFunctions = [
        'link_to',
        'link_to_asset',
        'link_to_route',
        'link_to_action',
    ];

    public function tearDown(): void
    {
        m::close();
    }

    public function testName()
    {
        $this->assertTrue(is_string($this->getHtml()->getName()));
    }

    public function testCustomFunctions()
    {
        $html      = $this->getHtml();
        $functions = $html->getFunctions();

        $this->assertTrue(is_array($functions));

        foreach ($functions as $function) {
            if (!in_array($function->getName(), $this->customFunctions)) {
                continue;
            }

            $this->assertInstanceOf('Collective\Html\HtmlBuilder', $function->getCallable()[0]);
        }
    }

    public function testWildcardFunctions()
    {
        $builder = m::mock('Collective\Html\HtmlBuilder');
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
        $mock      = m::mock(Node::class);

        foreach ($functions as $function) {
            $this->assertTrue(in_array('html', $function->getSafe($mock)));
        }
    }

    protected function getHtml()
    {
        return new Html(m::mock('Collective\Html\HtmlBuilder'));
    }
}
