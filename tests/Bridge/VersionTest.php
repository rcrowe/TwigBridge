<?php

namespace TwigBridge\Tests\Bridge;

use TwigBridge\Tests\Base;
use TwigBridge\Bridge;

class VersionTest extends Base
{
    public function testVersion()
    {
        $version = Bridge::BRIDGE_VERSION;
        $semver  = explode('.', $version);

        $this->assertCount(3, $semver);

        foreach ($semver as $number) {
            $this->assertTrue(is_numeric($number));
            $number = (int) $number;
            $this->assertTrue($number >= 0);
        }
    }
}
