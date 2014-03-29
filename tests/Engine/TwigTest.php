<?php

namespace TwigBridge\Tests\Engine;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Engine\Twig as Engine;
use Twig_Environment;
use Twig_Loader_Array;
use InvalidArgumentException;

class TwigTest extends Base
{
    public function tearDown()
    {
        m::close();
    }

    public function testInstance()
    {
        $global = array('name' => 'Rob');
        $engine = new Engine(new Twig_Environment, $global);

        $this->assertInstanceOf('Twig_Environment', $engine->getTwig());
        $this->assertEquals($global, $engine->getGlobalData());
    }

    public function testSetGlobalData()
    {
        $global = array('package' => 'TwigBridge');
        $engine = new Engine(new Twig_Environment);
        $engine->setGlobalData($global);

        $this->assertEquals($global, $engine->getGlobalData());
    }

    public function testLoad()
    {
        $loader = new Twig_Loader_Array(array(
            'index.html' => 'Hello {{ name }}',
        ));
        $twig     = new Twig_Environment($loader);
        $engine   = new Engine($twig);
        $template = $engine->load('index.html');

        $this->assertEquals('index.html', $template->getTemplateName());
        $this->assertEquals('Hello Rob', $template->render(array('name' => 'Rob')));
    }

    public function testTemplateNotFound()
    {
        $loader = new Twig_Loader_Array(array(
            'index.html' => 'Hello {{ name }',
        ));
        $twig     = new Twig_Environment($loader);
        $engine   = new Engine($twig);

        try {
            $engine->load('home.html');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Error in home.html: Template "home.html" is not defined.', $e->getMessage());
        }
    }

    public function testLoadTemplate()
    {
        $twig     = m::mock('Twig_Environment');
        $template = m::mock('TwigBridge\Twig\Template');

        $twig->shouldReceive('loadTemplate')->andReturn($template);
        $template->shouldReceive('setFiredEvents')->with(true)->once();

        $engine   = new Engine($twig);
        $response = $engine->load('home.html');
    }

    public function testGetWithGlobalData()
    {
        $loader = new Twig_Loader_Array(array(
            'index.html' => 'Hello {{ first }} {{ last }}',
        ));
        $twig     = new Twig_Environment($loader);
        $engine   = new Engine($twig, array('last' => 'Crowe'));
        $template = $engine->get('index.html', array('first' => 'Rob'));

        $this->assertEquals('Hello Rob Crowe', $template);
    }
}
