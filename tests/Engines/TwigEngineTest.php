<?php

namespace TwigBridgeTests\Engines;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use TwigBridge\Engines\TwigEngine;
use TwigBridge\Twig\Loader\Filesystem;
use Twig_Environment;

class TwigEngineTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testTwigEngineInstance()
    {
        $engine = new TwigEngine(new Twig_Environment, array('name' => 'Rob'));

        $this->assertEquals(get_class($engine->getTwig()), 'Twig_Environment');
        $this->assertEquals($engine->getGlobalData(), array('name' => 'Rob'));
    }

    public function testSetGlobalData()
    {
        $engine = new TwigEngine(new Twig_Environment);
        $engine->setGlobalData(array('package' => 'TwigBridge'));

        $this->assertEquals($engine->getGlobalData(), array('package' => 'TwigBridge'));
    }

    public function testDataMerge()
    {
        $engine = new TwigEngine(new Twig_Environment, array('package' => 'TwigBridge'));

        $data = $engine->getData(array('username' => 'rcrowe'));

        $this->assertEquals($data['package'], 'TwigBridge');
        $this->assertEquals($data['username'], 'rcrowe');
    }

    public function testDataMergeConflict()
    {
        $engine = new TwigEngine(new Twig_Environment, array('package' => 'TwigBridge'));

        $data = $engine->getData(array(
            'package' => 'bridgetwig',
            'username' => 'vivalacrowe',
        ));

        $this->assertEquals($data['package'], 'bridgetwig');
        $this->assertEquals($data['username'], 'vivalacrowe');
    }

    public function testCompileSingleFile()
    {
        $finder     = $this->getFinder(array(__DIR__.'/../fixtures/Engine'));
        $filesystem = $this->getFilesystem($finder);
        $engine     = $this->getEngine($filesystem);

        $output = $engine->get('single', array(
            'name' => 'Rob Crowe',
            'site' => array(
                'name' => 'TwigBridge'
            )
        ), 'single');

        $this->assertEquals($output, 'Hello Rob Crowe, welcome to TwigBridge!');
    }

    public function testCompileSingleFileFullPath()
    {
        $finder     = $this->getFinder(array(__DIR__.'/../fixtures'));
        $filesystem = $this->getFilesystem($finder);
        $engine     = $this->getEngine($filesystem);

        $output = $engine->get(__DIR__.'/../fixtures/Engine/single.twigbridge', array(
            'name' => 'Rob Crowe',
            'site' => array(
                'name' => 'TwigBridge'
            )
        ), 'Engine.single');

        $this->assertEquals($output, 'Hello Rob Crowe, welcome to TwigBridge!');
    }

    public function testCompileSingleFileWithGlobalData()
    {
        $finder     = $this->getFinder(array(__DIR__.'/../fixtures/Engine'));
        $filesystem = $this->getFilesystem($finder);
        $engine     = $this->getEngine($filesystem, array('name' => 'rcrowe'));

        $output = $engine->get('single', array('site' => array('name' => 'TwigBridge')), 'single');

        $this->assertEquals($output, 'Hello rcrowe, welcome to TwigBridge!');
    }

    public function testCompileChildParent()
    {
        $finder     = $this->getFinder(array(__DIR__.'/../fixtures/Engine'));
        $filesystem = $this->getFilesystem($finder);
        $engine     = $this->getEngine($filesystem, array('name' => 'Rob'));

        $output = $engine->get(__DIR__.'/../fixtures/Engine/child.twig', array(), 'child');
        $check  = <<<HTML
<h1>Hello Rob</h1>

I am the child

HTML;

        $this->assertEquals($output, $check);
    }

    public function testCompilePackageSingleView()
    {
        $hints = array(
            'twigbridge' => array(__DIR__.'/../fixtures/Engine')
        );

        $finder     = $this->getFinder(array(), $hints);
        $filesystem = $this->getFilesystem($finder);
        $engine     = $this->getEngine($filesystem);

        $output = $engine->get(__DIR__.'/../fixtures/Engine/single.twig', array(
            'name' => 'Rob Crowe',
            'site' => array(
                'name' => 'TwigBridge'
            )
        ), 'single');

        $this->assertEquals($output, 'Hello Rob Crowe, welcome to TwigBridge!');
    }

    public function testCompileSingleViewWithPackageParent()
    {
        $hints = array(
            'twigbridge' => array(__DIR__.'/../fixtures/Engine/package')
        );

        $finder = $this->getFinder(array(), $hints);
        $finder->shouldReceive('find')->times(3)->andReturn(__DIR__.'/../fixtures/Engine/package/parent.twig');

        $filesystem = $this->getFilesystem($finder);
        $engine     = $this->getEngine($filesystem);

        $output = $engine->get(__DIR__.'/../fixtures/Engine/package/child.twig', array('name' => 'Rob'), 'child');
        $check  = <<<HTML
<h1>Hello Rob</h1>

I am the child

HTML;

        $this->assertEquals($output, $check);
    }

    private function getFinder(array $paths = array(), array $hints = array())
    {
        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('getPaths')->andReturn($paths);
        $finder->shouldReceive('getHints')->andReturn($hints);

        return $finder;
    }

    private function getFilesystem($finder = null, $extension = null)
    {
        $finder OR $finder = $this->getFinder();

        $finder     = ($finder !== null) ? $finder : $this->getFinder();
        $filesystem = new Filesystem($finder);

        if ($extension !== null) {
            $filesystem->setExtension($extension);
        }

        return $filesystem;
    }

    private function getEngine($filesystem, $data = array())
    {
        $filesystem OR $filesystem = $this->getFilesystem();
        $twig = new Twig_Environment($filesystem, array());

        return new TwigEngine($twig, $data);
    }
}