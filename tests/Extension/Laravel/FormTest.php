<?php

namespace TwigBridge\Tests\Extension\Laravel;

use Mockery as m;
use Twig\Node\Node;
use TwigBridge\Extension\Laravel\Form;
use TwigBridge\Tests\Base;

class FormTest extends Base
{
    public function tearDown(): void
    {
        m::close();
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getForm()->getName());
    }

    public function testFunctionCallback()
    {
        $builder = m::mock('Collective\Html\FormBuilder');
        $builder->shouldReceive('fooBar')->once();

        $form = new Form($builder);

        $this->assertInternalType('array', $form->getFunctions());

        call_user_func($form->getFunctions()[0]->getCallable(), 'foo_bar');
    }

    public function testIsSafe()
    {
        $form     = $this->getForm();
        $function = $form->getFunctions()[0];

        $this->assertTrue(in_array('html', $function->getSafe(m::mock(Node::class))));
    }

    protected function getForm()
    {
        return new Form(m::mock('Collective\Html\FormBuilder'));
    }
}
