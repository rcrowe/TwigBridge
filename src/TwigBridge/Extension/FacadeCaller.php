<?php

/**
 * Brings Twig to Laravel.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @license MIT
 */
namespace TwigBridge\Extension;

class FacadeCaller{

    /**
     * @var string The name of the Facade that has to be called
     */
    protected $facade;

    /*
     * Create a new FacadeCaller for a facade.
     */
    public function __construct($facade){
        $this->facade = $facade;
    }

    /**
     * Make a 'magic' call to a Facade (or static class method)
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments){
        return call_user_func_array($this->facade.'::'.$name, $arguments);
    }
}