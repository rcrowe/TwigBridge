<?php

use TwigBridge\Twig\Loader\Filesystem;

class FilesystemTest extends PHPUnit_Framework_TestCase
{
    public function testExtendsType()
    {
        $filesystem = new Filesystem(array());
        $this->assertEquals(get_parent_class($filesystem), 'Twig_Loader_Filesystem');
    }

    public function testDefaultExtension()
    {
        $filesystem = new Filesystem(array());
        $this->assertEquals($filesystem->getExtension(), 'twig');
    }

    public function testCustomExtension()
    {
        $filesystem = new Filesystem(array(), 'twig.html');
        $this->assertEquals($filesystem->getExtension(), 'twig.html');
    }

    public function testSetExtension()
    {
        $filesystem = new Filesystem(array());
        $filesystem->setExtension('twig.html');

        $this->assertEquals($filesystem->getExtension(), 'twig.html');
    }

    public function testFileNoExtension()
    {
        $filesystem = $this->getFilesystem();
        $this->assertEquals($filesystem->getSource('base'), 'I am a base');
    }

    public function testFileCustomExtension()
    {
        $filesystem = $this->getFilesystem('twig.html');
        $this->assertEquals($filesystem->getSource('base'), 'I am a base with a custom extension');
    }

    public function testFileNotFound()
    {
        try {
            $filesystem = $this->getFilesystem('test.html');
            $filesystem->getSource('base');
            $this->assertFalse(true);
        } catch (Twig_Error_Loader $ex) {
            $this->assertTrue(true);
        }
    }

    public function testFileWithExtension()
    {
        $filesystem = $this->getFilesystem();
        $this->assertEquals($filesystem->getSource('base.twig'), 'I am a base');
    }

    public function testFileWithCustomExtension()
    {
        $filesystem = $this->getFilesystem('twig.html');
        $this->assertEquals($filesystem->getSource('base.twig.html'), 'I am a base with a custom extension');
    }

    private function getFilesystem($extension = null)
    {
        $filesystem = new Filesystem(array(__DIR__.'/../fixtures/'));

        if ($extension !== null) {
            $filesystem->setExtension($extension);
        }

        return $filesystem;
    }
}