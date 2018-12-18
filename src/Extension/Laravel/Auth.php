<?php

namespace TwigBridge\Extension\Laravel;

use Illuminate\Auth\AuthManager;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Access Laravels auth class in your Twig templates.
 */
class Auth extends AbstractExtension
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
            new TwigFunction('auth_check', [$this->auth, 'check']),
            new TwigFunction('auth_guest', [$this->auth, 'guest']),
            new TwigFunction('auth_user', [$this->auth, 'user']),
            new TwigFunction('auth_guard', [$this->auth, 'guard']),
        ];
    }
}
