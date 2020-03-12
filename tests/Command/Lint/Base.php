<?php

namespace TwigBridge\Tests\Command\Lint;

use TwigBridge\Tests\Base as BridgeBase;
use Mockery as m;
use Symfony\Component\Console\Output\StreamOutput;

class Base extends BridgeBase
{
    protected function getApplication(array $customConfig = [])
    {
        $app = parent::getApplication($customConfig);
        $app['twig.extension'] = 'twig';
        $app['twig']    = m::mock('TwigBridge\Bridge');

        $finder = m::mock('Illuminate\View\ViewFinderInterface');
        $finder->shouldReceive('name');
        $finder->shouldReceive('in')->andReturn($finder);

        $viewFinder = m::mock('Illuminate\View\ViewFinderInterface');
        $viewFinder->shouldReceive('getPaths')->andReturn([]); // paths
        $viewFinder->shouldReceive('files')->andReturn($finder);

        $app['view'] = m::mock('Illuminate\View\Factory');
        $app['view']->shouldReceive('getFinder')->andReturn($viewFinder);

        return $app;
    }
}
