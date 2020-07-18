<?php
/**
 * This file is part of the TwigBridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;

class AppVariable
{
    protected $request;
    protected $environment;
    protected $debug;

    public function __construct(Request $request, Repository $config)
    {
        $this->request = $request;
        $this->environment = $config->get('app.env');
        $this->debug = $config->get('app.debug');
    }

    /**
     * Returns the current session.
     *
     * @return \Illuminate\Session\Store|null The session
     */
    public function getSession()
    {
        return $this->request->session();
    }

    /**
     * @return \Illuminate\Auth\Authenticatable
     */
    public function getUser()
    {
        return $this->request->user();
    }

    /**
     * Returns the current request.
     *
     * @return Request|null The HTTP request object
     */
    public function getRequest()
    {
        if (null === $this->request) {
            throw new \RuntimeException('The "app.request" variable is not available.');
        }

        return $this->request;
    }

    /**
     * Returns the current app environment.
     *
     * @return string The current environment string (e.g 'dev')
     */
    public function getEnvironment()
    {
        if (null === $this->environment) {
            throw new \RuntimeException('The "app.environment" variable is not available.');
        }

        return $this->environment;
    }

    /**
     * Returns the current app debug mode.
     *
     * @return bool The current debug mode
     */
    public function getDebug()
    {
        if (null === $this->debug) {
            throw new \RuntimeException('The "app.debug" variable is not available.');
        }

        return $this->debug;
    }
}