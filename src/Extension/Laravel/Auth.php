<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Extension\Laravel;

use Twig_Extension;
use Twig_SimpleFunction;
use Illuminate\Auth\AuthManager;

/**
 * Access Laravels auth class in your Twig templates.
 */
class Auth extends Twig_Extension
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    protected $auth;

    /**
     * Create a new auth extension.
     *
     * @param \Illuminate\Auth\AuthManager
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Auth';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('auth_check', $this->generateCallable('check')),
            new Twig_SimpleFunction('auth_guest', $this->generateCallable('guest')),
            new Twig_SimpleFunction('auth_user', $this->generateCallable('user')),
            new Twig_SimpleFunction('auth_guard', [$this->auth, 'guard']),
        ];
    }

    /**
     * Generates a callable using a guard for the AuthManager
     *
     * @param $methodName
     *
     * @return \Closure
     */
    private function generateCallable($methodName)
    {
        return function ($guard = 'web') use ($methodName) {
            $params = func_get_args();
            $guard = array_shift($params);

            return $this->auth->guard($guard)->$methodName($params);
        };
    }
}
