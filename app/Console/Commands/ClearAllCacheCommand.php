<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocont:clear_cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->call('view:clear');
        $this->call('route:clear');
        $this->call('optimize:clear');
        $this->call('config:clear');
        $this->call('cache:clear');
//        $this->call('debugbar:clear');

        return Command::SUCCESS;
    }
}
