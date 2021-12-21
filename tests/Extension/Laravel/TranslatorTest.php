<?php

namespace TwigBridge\Tests\Extension\Laravel;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Laravel\Translator;
use Twig\Node\Node;

class TranslatorTest extends Base
{
    public function tearDown(): void
    {
        m::close();
    }

    public function testName()
    {
        $this->assertTrue(is_string($this->getTranslator()->getName()));
    }

    public function testFunctions()
    {
        $translator = $this->getTranslator();
        $functions  = $translator->getFunctions();

        $this->assertTrue(is_array($functions));

        foreach ($functions as $function) {
            $this->assertInstanceOf('Illuminate\Translation\Translator', $function->getCallable()[0]);
        }
    }

    public function testIsNotSafe()
    {
        $translator = $this->getTranslator();
        $functions  = $translator->getFunctions();
        $node       = m::mock(Node::class);
        $check      = [
            'trans',
            'trans_choice'
        ];

        foreach ($functions as $function) {
            if (!in_array($function->getName(), $check)) {
                continue;
            }

            $this->assertFalse(in_array('html', $function->getSafe($node)));
        }
    }

    protected function getTranslator()
    {
        return new Translator(m::mock('Illuminate\Translation\Translator'));
    }
}
