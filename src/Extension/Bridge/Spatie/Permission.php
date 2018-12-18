<?php

namespace TwigBridge\Extension\Bridge\Spatie;

use Twig_Extension;
use Twig_Function;

/**
 * @see https://github.com/spatie/laravel-permission
 */
class Permission extends Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Spatie_Permission';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_Function('role', function ($roleName = "") {
                if (auth()->check()) {
                    return auth()->user()->hasRole($roleName);
                }

                return false;
            }),
            new Twig_Function('hasrole', function ($roleName = "") {
                if (auth()->check()) {
                    return auth()->user()->hasRole($roleName);
                }

                return false;
            }),
            new Twig_Function('hasanyrole', function ($roleNames = []) {
                if (auth()->check()) {
                    return auth()->user()->hasAnyRole($roleNames);
                }

                return false;
            }),
            new Twig_Function('hasallroles', function ($roleNames = []) {
                if (auth()->check()) {
                    return auth()->user()->hasAllRoles($roleNames);
                }

                return false;
            }),
        ];
    }
}
