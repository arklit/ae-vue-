<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orchid:create-admin';

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
        if (app()->isProduction()) {
            $password = Str::random(12);
            Artisan::call('orchid:admin',
                [
                    'name' => 'admin',
                    'email' => 'admin@rocont.ru',
                    'password' => trim($password)
                ]);
            $this->output->comment('orchid:admin rocont info@rocont.ru ' . $password);
            $this->output->info('Password: ' . $password);
        } else {
            Artisan::call('orchid:admin', [
                'name' => 'admin',
                'email' => 'admin@rocont.ru',
                'password' => 'password'
            ]);
        }
        return Command::SUCCESS;
    }
}
