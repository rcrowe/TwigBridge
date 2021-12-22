<?php

namespace TwigBridge\Tests\Extension\Loader;

use TwigBridge\Tests\Base;
use Mockery as m;
use TwigBridge\Extension\Loader\Filters;

class FiltersTest extends Base
{
    public function testName()
    {
        $filters = new Filters(m::mock('Illuminate\Config\Repository'));

        $this->assertTrue(is_string($filters->getName()));
    }

    public function testNoFilters()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')->andReturn([]);

        $filters = new Filters($config);
        $filters = $filters->getFunctions();

        $this->assertTrue(is_array($filters));
        $this->assertTrue(empty($filters));
    }

    public function testAddFilter()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('get')->andReturn([
            'foo' => 'bar',
            'Baz' => function () {
                return 'bing';
            }
        ]);

        $filters = new Filters($config);
        $filter  = $filters->getFilters()[1];

        $this->assertEquals('bing', call_user_func($filter->getCallable()));
    }
}
