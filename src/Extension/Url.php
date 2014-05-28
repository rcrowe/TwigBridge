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
use Illuminate\Routing\UrlGenerator;
use  Illuminate\Support\Str;

class Url extends Twig_Extension
{
    protected $url;

    public function __construct(UrlGenerator $url){
        $this->url = $url;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(){
        return 'TwigBridge_Extension_Url';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(){
        $url = $this->url;
        return array(
            new Twig_SimpleFunction('asset', array($url, 'asset'), array('is_safe' => array('html'))),
            new Twig_SimpleFunction('action', array($url, 'action'), array('is_safe' => array('html'))),
            new Twig_SimpleFunction('url', array($this, 'url'), array('is_safe' => array('html'))),
            new Twig_SimpleFunction('route', array($url, 'route'), array('is_safe' => array('html'))),
            new Twig_SimpleFunction('secure_url', array($url, 'secure'), array('is_safe' => array('html'))),
            new Twig_SimpleFunction('secure_asset', array($url, 'secureAsset'), array('is_safe' => array('html'))),
            new Twig_SimpleFunction('url_*', function($name) use($url){
                    $arguments = array_slice(func_get_args(), 1);
                    $name = Str::camel($name);
                    return call_user_func_array(array($url, $name), $arguments);
                })
        );
    }

    public function url($path = null, $parameters = array(), $secure = null){
        return $this->url->to($path, $parameters, $secure);
    }
}
