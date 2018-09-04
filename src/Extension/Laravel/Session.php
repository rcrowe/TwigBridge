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

use Illuminate\Session\Store;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Access Laravels session class in your Twig templates.
 */
class Session extends AbstractExtension
{
    /**
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Create a new session extension
     *
     * @param \Illuminate\Session\Store
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Session';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('session', [$this->session, 'get']),
            new TwigFunction('csrf_token', [$this->session, 'token'], ['is_safe' => ['html']]),
            new TwigFunction('csrf_field', 'csrf_field', ['is_safe' => ['html']]),
            new TwigFunction('method_field', 'method_field', ['is_safe' => ['html']]),
            new TwigFunction('session_get', [$this->session, 'get']),
            new TwigFunction('session_pull', [$this->session, 'pull']),
            new TwigFunction('session_has', [$this->session, 'has']),
        ];
    }
}
