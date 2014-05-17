<?php

namespace TwigBridge\Tests\Command;

use TwigBridge\Tests\Base;
use TwigBridge\Command\Lint;

class LintTest extends Base
{
    public function testInstance()
    {
        $command = new Lint;

        $this->assertInstanceOf('Illuminate\Console\Command', $command);
    }
}
