<?php

namespace TwigBridge\Tests\Extension\Laravel;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Laravel\Form;

class FormTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testName()
    {
        $this->assertInternalType('string', $this->getForm()->getName());
    }

    public function testFunctionCallback()
    {
        $builder = m::mock('Illuminate\Html\FormBuilder');
        $builder->shouldReceive('fooBar')->once();

        $form = new Form($builder);

        $this->assertInternalType('array', $form->getFunctions());

        call_user_func($form->getFunctions()[0]->getCallable(), 'foo_bar');
    }

    public function testIsSafe()
    {
        $form     = $this->getForm();
        $function = $form->getFunctions()[0];

        $this->assertTrue(in_array('html', $function->getSafe(m::mock('Twig_Node'))));
    }

    protected function getForm()
    {
        return new Form(m::mock('Illuminate\Html\FormBuilder'));
    }
}
