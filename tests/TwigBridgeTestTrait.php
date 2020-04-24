<?php

namespace TwigBridge\Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Mockery as m;
use TwigBridge\ServiceProvider;

trait TwigBridgeTestTrait
{
    protected $twigBridgeRoot;


    public function setup()
    {
        $this->twigBridgeRoot = realpath(__DIR__ . '/../src');
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @param array $customConfig
     * @param array|bool $extensions if an array, it will replace the extensions list as set in the default twigbridge.php config file
     * @return Application
     */
    protected function getApplication(array $customConfig = [], $extensions = false)
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

        $app['view'] = new Factory(
            new EngineResolver,
            $finder,
            m::mock('Illuminate\Events\Dispatcher')
        );

        if (!is_array($extensions)) {
            $config = include $this->twigBridgeRoot . '/../config/twigbridge.php';
            $extensions = $config['extensions'];
        }

        $configData = [
            'twigbridge' => [
                'extensions' => $extensions,
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

    protected function addBridgeServiceToApplication(Application $app)
    {
        $provider = new ServiceProvider($app);

        // Register and boot provider
        $provider->register();
        $provider->boot();
    }
}
