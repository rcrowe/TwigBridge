<?php

use TwigBridge\Twig\Lexer;
use TwigBridge\Twig\Loader\Filesystem;

class LexerTest extends PHPUnit_Framework_TestCase
{
    public function testArrayParams()
    {
        try {
            $lexer = new Lexer(
                array(),
                array(),
                array()
            );
            $this->assertFalse(true);
        }
        catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
        catch (Exception $ex) {
            $this->assertFalse(true);
        }

        try {
            $lexer = new Lexer(
                array('{#'),
                array(),
                array()
            );
            $this->assertFalse(true);
        }
        catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
        catch (Exception $ex) {
            $this->assertFalse(true);
        }

        try {
            $lexer = new Lexer(
                array('{#', '#}'),
                array(),
                array()
            );
            $this->assertFalse(true);
        }
        catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
        catch (Exception $ex) {
            $this->assertFalse(true);
        }

        try {
            $lexer = new Lexer(
                array('{#', '#}'),
                array('{%'),
                array()
            );
            $this->assertFalse(true);
        }
        catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
        catch (Exception $ex) {
            $this->assertFalse(true);
        }

        try {
            $lexer = new Lexer(
                array('{#', '#}'),
                array('{%', '%}'),
                array()
            );
            $this->assertFalse(true);
        }
        catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
        catch (Exception $ex) {
            $this->assertFalse(true);
        }

        try {
            $lexer = new Lexer(
                array('{#', '#}'),
                array('{%', '%}'),
                array('{{')
            );
            $this->assertFalse(true);
        }
        catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
        catch (Exception $ex) {
            $this->assertFalse(true);
        }

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

    public function testGetLexer()
    {
        $loader = new Filesystem(array(), 'twig');
        $twig   = new Twig_Environment($loader, array());

        $lexer = new Lexer(
            array('{#', '#}'),
            array('{%', '%}'),
            array('{{', '}}')
        );

        $lexer = $lexer->getLexer($twig);

        $this->assertEquals(get_class($lexer), 'Twig_Lexer');
    }
}