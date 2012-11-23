<?php

namespace TwigBridge;

class Environment extends \Illuminate\View\Environment
{
    /**
     * The extension to engine bindings.
     *
     * @var array
     */
    protected $extensions = array('blade.php' => 'blade', 'php' => 'php', 'twig' => 'twig');
}