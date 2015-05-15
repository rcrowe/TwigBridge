<?php

namespace TwigBridge\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\View\Factory;
use Illuminate\Config\Repository;
use Illuminate\View\Engines\EngineResolver;

abstract class Base extends PHPUnit_Framework_TestCase
{
    protected $twigBridgeRoot;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->twigBridgeRoot = realpath(__DIR__.'/../src');
    }

    public function tearDown()
    {
        m::close();
    }

    protected function getApplication(array $customConfig = [])
    {
        $app = new Application;
        $app->instance('path', __DIR__);

        $app['env']          = 'production';
        $app['path.storage'] = __DIR__.'/storage';

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

        

        $extensionsData = include $this->twigBridgeRoot.'/../config/extensions.php';
        
        $configData = array(
        	'twigbridge' => array(
                'extensions' => $extensionsData,
                'twig' => array(
                    'extension' => 'twig',
                    'environment' => array(
                        'debug'               => false,
                        'charset'             => 'utf-8',
                        'base_template_class' => 'TwigBridge\Twig\Template',
                        'cache'               => null,
                        'auto_reload'         => true,
                        'strict_variables'    => false,
                        'autoescape'          => true,
                        'optimizations'       => -1,
                    ),
                    'globals' => array(),
                ),
    	    ),
        );
        
        // Config
        $app['config'] = new Repository($configData);

        $app->bind('Illuminate\Config\Repository', function () use ($app) {
            return $app['config'];
        });

        return $app;
    }
}
