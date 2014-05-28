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
use Illuminate\Auth\AuthManager;

class Auth extends Twig_Extension
{
    protected $auth;

    public function __construct(AuthManager $auth){
        $this->auth = $auth;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(){
        return 'TwigBridge_Extension_Auth';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(){
        return array(
            new Twig_SimpleFunction('auth_check', array($this->auth, 'check')),
            new Twig_SimpleFunction('auth_guest', array($this->auth, 'guest')),
            new Twig_SimpleFunction('auth_user', array($this->auth, 'user')),
        );
    }

}
