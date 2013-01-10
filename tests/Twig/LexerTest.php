<?php

namespace TwigBridgeTests\Twig;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use TwigBridge\Twig\Lexer;
use TwigBridge\Twig\Loader\Filesystem;
use Twig_Environment;
use ReflectionProperty;
use InvalidArgumentException;
use Exception;

class LexerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    private function param($callback)
    {
        try {
            $callback();
            $this->assertFalse(true);
        } catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
        catch (Exception $ex) {
            $this->assertFalse(true);
        }
    }

    public function testArrayParams()
    {
        $this->param(function() {
            $lexer = new Lexer(
                array(),
                array(),
                array()
            );
        });

        $this->param(function() {
            $lexer = new Lexer(
                array('{#'),
                array(),
                array()
            );
        });

        $this->param(function() {
            $lexer = new Lexer(
                array('{#', '#}'),
                array(),
                array()
            );
        });

        $this->param(function() {
            $lexer = new Lexer(
                array('{#', '#}'),
                array('{%'),
                array()
            );
        });

        $this->param(function() {
            $lexer = new Lexer(
                array('{#', '#}'),
                array('{%', '%}'),
                array()
            );
        });

        $this->param(function() {
            $lexer = new Lexer(
                array('{#', '#}'),
                array('{%', '%}'),
                array('{{')
            );
        });

        // Make sure it goes through cleanly
        try {
            $lexer = new Lexer(
                array('{#', '#}'),
                array('{%', '%}'),
                array('{{', '}}')
            );
            $this->assertTrue(true);
        }
        catch (Exception $ex) {
            $this->assertFalse(true);
        }
    }

    public function testGetTags()
    {
        $lexer = new Lexer(
            array('{#', '#}'),
            array('{%', '%}'),
            array('{#', '/}')
        );

        $this->assertEquals($lexer->getTags(), array(
            'tag_comment'  => array('{#', '#}'),
            'tag_block'    => array('{%', '%}'),
            'tag_variable' => array('{#', '/}'),
        ));
    }

    public function testTwigLexerDelimiters()
    {
        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('getPaths')->once()->andReturn(array());
        $finder->shouldReceive('getHints')->once()->andReturn(array());

        $loader = new Filesystem($finder, 'twig');
        $twig   = new Twig_Environment($loader, array());

        $lexer = new Lexer(
            array('{@', '@}'),
            array('{*', '*}'),
            array('{#', '/}')
        );

        $lexer = $lexer->getLexer($twig);
        $this->assertEquals(get_class($lexer), 'Twig_Lexer');

        $prop = new ReflectionProperty('Twig_Lexer', 'options');
        $prop->setAccessible(true);
        $options = $prop->getValue($lexer);

        // Comment
        $this->assertEquals('{@', $options['tag_comment'][0]);
        $this->assertEquals('@}', $options['tag_comment'][1]);

        // Block
        $this->assertEquals('{*', $options['tag_block'][0]);
        $this->assertEquals('*}', $options['tag_block'][1]);

        // Variable
        $this->assertEquals('{#', $options['tag_variable'][0]);
        $this->assertEquals('/}', $options['tag_variable'][1]);
    }
}