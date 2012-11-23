<?php

namespace TwigBridge;

class FileViewFinder extends Illuminate\View\FileViewFinder
{
    /**
     * Register a view extension with the finder.
     *
     * @var array
     */
    protected $extensions = array('php', 'blade.php', 'twig');
}