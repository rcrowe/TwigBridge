<?php

namespace TwigBridgeTests\Twig;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use TwigBridge\Twig\Loader\Filesystem;
use ReflectionMethod;
use Twig_Error_Loader;

class FilesystemTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testParentClass()
    {
        $this->assertEquals(get_parent_class($this->getFilesystem()), 'Twig_Loader_Filesystem');
    }

    public function testViewPaths()
    {
        $finder     = $this->getFinder(array(__DIR__.'/../fixtures'));
        $filesystem = $this->getFilesystem($finder);
        $paths      = $filesystem->getPaths();

        $this->assertTrue(count($paths) === 1);
        $this->assertEquals($paths[0], __DIR__.'/../fixtures');
    }

    public function testNamespacePaths()
    {
        $hints = array(
            'twigbridge' => array(__DIR__.'/../Extensions')
        );

        $finder     = $this->getFinder(array(), $hints);
        $filesystem = $this->getFilesystem($finder);
        $paths      = $filesystem->getPaths();

        $this->assertTrue(count($paths) === 1);
        $this->assertEquals($paths[0], __DIR__.'/../Extensions');
    }

    public function testPathsMerged()
    {
        $hints = array(
            'twigbridge' => array(__DIR__.'/../Extensions')
        );

        $finder     = $this->getFinder(array(__DIR__.'/../fixtures'), $hints);
        $filesystem = new Filesystem($finder);
        $paths      = $filesystem->getPaths();

        $this->assertTrue(count($paths) === 2);
        $this->assertEquals($paths[0], __DIR__.'/../fixtures'); // view paths always come first
        $this->assertEquals($paths[1], __DIR__.'/../Extensions');
    }

    public function testPathsMergeConflict()
    {
        $hints = array(
            'twigbridge' => array(__DIR__.'/../Extensions', __DIR__.'/../fixtures')
        );

        $finder     = $this->getFinder(array(__DIR__.'/../fixtures'), $hints);
        $filesystem = new Filesystem($finder);
        $paths      = $filesystem->getPaths();

        $this->assertTrue(count($paths) === 2);
        $this->assertEquals($paths[0], __DIR__.'/../fixtures');
        $this->assertEquals($paths[1], __DIR__.'/../Extensions');
    }

    public function testDefaultExtension()
    {
        $filesystem = new Filesystem($this->getFinder());
        $this->assertEquals($filesystem->getExtension(), 'twig');
    }

    public function testCustomExtension()
    {
        $filesystem = $this->getFilesystem(null, 'twig.html');
        $this->assertEquals($filesystem->getExtension(), 'twig.html');
    }

    public function testSetExtension()
    {
        $filesystem = $this->getFilesystem();
        $filesystem->setExtension('twig.html');

        $this->assertEquals($filesystem->getExtension(), 'twig.html');
    }

    public function testAppendExtension()
    {
        $method = new ReflectionMethod('TwigBridge\Twig\Loader\Filesystem', 'findTemplate');
        $method->setAccessible(true);

        $finder = $this->getFinder(array(__DIR__.'/../fixtures/Filesystem'));
        $path   = $method->invoke( $this->getFilesystem($finder), 'base' );

        $this->assertEquals(pathinfo($path, PATHINFO_BASENAME), 'base.twig');
    }

    public function testFindInPackage()
    {
        $finder = $this->getFinder();
        $finder->shouldReceive('find')->once()->andReturn(__DIR__.'/../fixtures/Filesystem/base.twig');

        $this->assertEquals($this->getFilesystem($finder)->getSource('twigbridge::base'), 'I am a base');
    }

    public function testFileNoExtension()
    {
        $finder     = $this->getFinder(array(__DIR__.'/../fixtures/Filesystem'));
        $filesystem = $this->getFilesystem($finder);

        $this->assertEquals($filesystem->getSource('base'), 'I am a base');
    }

    public function testFileCustomExtension()
    {
        $finder     = $this->getFinder(array(__DIR__.'/../fixtures/Filesystem'));
        $filesystem = $this->getFilesystem($finder, 'twig.html');

        $this->assertEquals($filesystem->getSource('base'), 'I am a base with a custom extension');
    }

    public function testFileNotFound()
    {
        try {
            $filesystem = $this->getFilesystem();
            $filesystem->getSource('base');
            $this->assertFalse(true);
        } catch (Twig_Error_Loader $ex) {
            $this->assertTrue(true);
        }
    }

    public function testFileWithExtension()
    {
        $finder     = $this->getFinder(array(__DIR__.'/../fixtures/Filesystem'));
        $filesystem = $this->getFilesystem($finder);
        $this->assertEquals($filesystem->getSource('base.twig'), 'I am a base');
    }

    public function testFileWithCustomExtension()
    {
        $finder     = $this->getFinder(array(__DIR__.'/../fixtures/Filesystem'));
        $filesystem = $this->getFilesystem($finder, 'twig.html');
        $this->assertEquals($filesystem->getSource('base.twig.html'), 'I am a base with a custom extension');
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
        $finder     = ($finder !== null) ? $finder : $this->getFinder();
        $filesystem = new Filesystem($finder);

        if ($extension !== null) {
            $filesystem->setExtension($extension);
        }

        return $filesystem;
    }
}