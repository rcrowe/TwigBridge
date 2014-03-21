<?php

namespace TwigBridge\Tests;

use TwigBridge\Tests\Base;
use TwigBridge\ServiceProvider;
use TwigBridge\TwigBridge;

class TwigBridgeTest extends Base
{
    public function testVersion()
    {
        $version = TwigBridge::VERSION;
        $semver  = explode('.', $version);

        $this->assertCount(3, $semver);

        foreach ($semver as $number) {
            $this->assertTrue(is_numeric($number));
            $number = (int) $number;
            $this->assertTrue($number >= 0);
        }
    }

    public function testExtension()
    {
        $app    = $this->getApplication();
        $bridge = new TwigBridge($app);

        $this->assertEquals('twig', $bridge->getExtension());
    }

    public function testExtensions()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);

        $provider->boot();

        $bridge     = $app['twig.bridge'];
        $extensions = $bridge->getExtensions();

        $this->assertTrue(is_array($extensions));
    }

    public function testOptions()
    {
        $app      = $this->getApplication();
        $provider = new ServiceProvider($app);

        $provider->boot();

        $bridge     = $app['twig.bridge'];
        $options    = $bridge->getTwigOptions();

        $this->assertTrue(is_array($options));
        $this->assertEquals($options['cache'], __DIR__.'/storage/views/twig');
    }

    public function testLexer()
    {
        $delimiters = array(
            'tag_comment'  => array('1-##', '##'),
            'tag_block'    => array('2-%%', '%%'),
            'tag_variable' => array('3-@@', '@@'),
        );
        $app = $this->getApplication(array(
            'twig' => array(
                'delimiters' => $delimiters,
            ),
        ));

        $provider = new ServiceProvider($app);
        $provider->boot();

        $lexer = $app['twig']->getLexer();
        $this->assertInstanceOf('TwigBridge\Twig\Lexer', $lexer);

        $options = $lexer->getOptions();

        foreach ($delimiters as $type => $tag) {
            $this->assertTrue(array_key_exists($type, $options));
            $this->assertEquals($tag[0], $options[$type][0]);
            $this->assertEquals($tag[1], $options[$type][1]);
        }
    }
}
