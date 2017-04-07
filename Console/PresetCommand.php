<?php

namespace Illuminate\Foundation\Console;

use InvalidArgumentException;
use Illuminate\Console\Command;

class PresetCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'preset { type : The preset type (fresh, bootstrap, react) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Swap the front-end scaffolding for the application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! in_array($this->argument('type'), ['fresh', 'bootstrap', 'react'])) {
            throw new InvalidArgumentException('Invalid preset.');
        }

        return $this->{$this->argument('type')}();
    }

    /**
     * Install the "fresh" preset.
     *
     * @return void
     */
    protected function fresh()
    {
        Presets\Fresh::install();

        $this->info('Front-end scaffolding removed successfully.');
    }

    /**
     * Install the "fresh" preset.
     *
     * @return void
     */
    protected function bootstrap()
    {
        Presets\Bootstrap::install();

        $this->info('Bootstrap scaffolding installed successfully.');
        $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }

    /**
     * Install the "react" preset.
     *
     * @return void
     */
    public function react()
    {
        Presets\React::install();

        $this->info('React scaffolding installed successfully.');
        $this->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');
    }
}
