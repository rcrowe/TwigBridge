<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 * @copyright Barry vd. Heuvel <barryvdh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Illuminate\Config\Repository as ConfigRepository;


class Config extends Twig_Extension
{
    protected $config;

    /**
     * Create a new config extension
     *
     * @param \Illuminate\Config\Repository
     */
    public function __construct(ConfigRepository $config){
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(){
        return 'TwigBridge_Extension_Config';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(){
        return array(
            new Twig_SimpleFunction('config_get', array($this->config, 'get'), array('is_safe' => array('html'))),
            new Twig_SimpleFunction('config_has', array($this->config, 'has'), array('is_safe' => array('html'))),
        );
    }

}
