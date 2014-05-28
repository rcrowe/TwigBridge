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
use Illuminate\Session\SessionManager;


class Session extends Twig_Extension
{
    protected $session;

    public function __construct(SessionManager $session){
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(){
        return 'TwigBridge_Extension_Session';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(){
        return array(
            new Twig_SimpleFunction('csrf_token', array($this->session, 'token'), array('is_safe' => array('html'))),
            new Twig_SimpleFunction('session_get', array($this->session, 'get')),
            new Twig_SimpleFunction('session_has', array($this->session, 'has')),
        );
    }

}
