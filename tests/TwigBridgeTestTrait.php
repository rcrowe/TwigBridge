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


    public function setUp(): void
    {
        $this->twigBridgeRoot = realpath(__DIR__ . '/../src');
    }

    public function tearDown(): void
    {
        m::close();
    }

    /**
     * @param array $customConfig that will override the default config.
     *      A recursive merge is apply except for $customConfig['extensions'] which
     *      will replace the whole 'extensions' if present
     * @return Application
     */
    protected function getApplication(array $customConfig = [])
    {
        $app = new Application(__DIR__);

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

        if (!isset($customConfig['extensions'])) {
            $config = include $this->twigBridgeRoot . '/../config/twigbridge.php';
            $customConfig['extensions'] = $config['extensions'];
        }

        $configData = [
            'twigbridge' => [
                'twig' => [
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
