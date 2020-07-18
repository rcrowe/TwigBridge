<?php

namespace TwigBridge\Tests;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\View\Factory;
use Illuminate\Config\Repository;
use Illuminate\View\Engines\EngineResolver;

abstract class Base extends TestCase
{
    protected $twigBridgeRoot;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->twigBridgeRoot = realpath(__DIR__ . '/../src');
    }

    public function tearDown()
    {
        m::close();
    }

    protected function getApplication(array $customConfig = [])
    {
        $app = new Application;
        $app->instance('path', __DIR__);

        $app['env'] = 'production';
        $app['path.config'] = __DIR__ . '/config';
        $app['path.storage'] = __DIR__ . '/storage';

        // Filesystem
        $files = m::mock('Illuminate\Filesystem\Filesystem');
        $app['files'] = $files;

        // View
        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('addExtension');

        // Request
        $request = m::mock('Illuminate\Http\Request');
        $app['request'] = $request;

        $app['view'] = new Factory(
            new EngineResolver,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        $config = include $this->twigBridgeRoot . '/../config/twigbridge.php';

        $configData = [
            'twigbridge' => [
                'extensions' => $config['extensions'],
                'twig'       => [
                    'extension'   => 'twig',
                    'environment' => [
                        'debug'               => false,
                        'charset'             => 'utf-8',
                        'base_template_class' => 'TwigBridge\Twig\Template',
                        'cache'               => null,
                        'auto_reload'         => true,
                        'strict_variables'    => false,
                        'autoescape'          => true,
                        'optimizations'       => -1,
                    ],
                    'globals'     => [],
                ],
            ],
        ];

        $configData['twigbridge'] = array_replace_recursive($configData['twigbridge'], $customConfig);

        // Config
        $app['config'] = new Repository($configData);

        $app->bind('Illuminate\Config\Repository', function () use ($app) {
            return $app['config'];
        });

        return $app;
    }
}
