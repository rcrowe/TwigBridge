<?php

namespace TwigBridge\Command;

use Illuminate\Console\Command;
use Twig\Environment;
use TwigBridge\Bridge;

/**
 * Artisan command to show details about the TwigBridge package.
 */
class TwigBridge extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'twig';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get information about TwigBridge';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line('<info>Twig</info> version        <comment>'.Environment::VERSION.'</comment>');
        $this->line('<info>Twig Bridge</info> version <comment>'.Bridge::BRIDGE_VERSION.'</comment>');
    }

    public function fire()
    {
        return $this->handle();
    }
}
