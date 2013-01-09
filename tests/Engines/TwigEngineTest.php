<?php

use TwigBridge\Engines\TwigEngine;
use TwigBridge\Twig\Loader\Filesystem;

class TwigEngineTest extends PHPUnit_Framework_TestCase
{
    public function testGetTwig()
    {
        // $engine = $this->getEngine();

        // $this->assertEquals(get_class($engine->getTwig()), 'Twig_Environment');
    }

    public function testGetFile()
    {
//         $path   = array(__DIR__.'/../fixtures/');
//         $engine = $this->getEngine($path);

//         $html = <<<HTML
// <h1>Hello Rob</h1>

// I am the child

// HTML;

//         $this->assertEquals($engine->get('child', array('name' => 'Rob')), $html);
    }

    public function testGlobalData()
    {
        // $engine = $this->getEngine(array(), 'twig', array(
        //     'site' => array(
        //         'name' => 'TwigBridge Test'
        //     ),
        // ));

        // $data = $engine->getGlobalData();
        // $this->assertTrue(is_array($data));
        // $this->assertEquals($data['site']['name'], 'TwigBridge Test');

        // $engine->setGlobalData(array(
        //     'egg' => 'fried'
        // ));

        // $data = $engine->getGlobalData();
        // $this->assertFalse(isset($data['site']));
        // $this->assertEquals($data['egg'], 'fried');
    }

    public function testMergeDataWithGlobal()
    {
        // // Test no conflict
        // $engine = $this->getEngine();
        // $engine->setGlobalData(array(
        //     'company' => 'Cog Powered'
        // ));

        // $data = $engine->getData(array('country' => 'uk'));

        // $this->assertEquals($data['company'], 'Cog Powered');
        // $this->assertEquals($data['country'], 'uk');

        // // Test key conflict merge
        // $data = $engine->getData(array('company' => 'VE'));

        // $this->assertEquals($data['company'], 'VE');
    }

    private function getEngine($paths = array(), $extension = 'twig', $global_data = array())
    {
        // $loader = new Filesystem($paths, $extension);
        // $twig   = new Twig_Environment($loader, array());

        // return new TwigEngine($twig, $global_data);
    }
}