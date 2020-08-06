<?php
/*
 * This file is part of TwigBridge.
 *
 * (c) Jin <j@shopar.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Tests;

use Mockery as m;
use Illuminate\Http\Request;
use Illuminate\Session\NullSessionHandler;
use Illuminate\Session\Store;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use TwigBridge\AppVariable;
use TwigBridge\Tests\Node\TemplateForTest;

class AppVariableTest extends Base
{
    protected $object;
    protected $twig;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $app = $this->getApplication();
        /**
         * request with session and user resolver
         */
        $sessionStore = new Store('session', new NullSessionHandler());
        $request = new Request();
        $request->setLaravelSession($sessionStore);
        $request->setUserResolver(function () {
            return m::mock('Illuminate\Contracts\Auth\Authenticatable');
        });

        $this->object = new AppVariable($request, $app['config']);

        $this->twig = new Environment($this->createMock(LoaderInterface::class), [
            'cache' => false,
            'autoescape' => false,
        ]);
    }

    protected function getAttribute($item)
    {
        $template = new TemplateForTest($this->twig);
        return twig_get_attribute($this->twig, $template->getSourceContext(), $this->object, $item);
    }

    public function testGetAppVariableRequest()
    {
        // {{ app.request }}
        $request = $this->getAttribute('request');
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testGetAppVariableSession()
    {
        // {{ app.session }}
        $session = $this->getAttribute('session');
        $this->assertInstanceOf(Store::class, $session);
    }

    public function testGetAppVariableUser()
    {
        // {{ app.user }}
        $user = $this->getAttribute('user');
        $this->assertInstanceOf('Illuminate\Contracts\Auth\Authenticatable', $user);
    }

    public function testGetAppVariableConfigShouldBeNull()
    {
        $config = $this->getAttribute('config');
        $this->assertNull($config);
    }
}