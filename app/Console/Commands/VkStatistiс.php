<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VkStatistiс extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk:statistic {group_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //$this->drip->send(User::find($this->argument('user')));

        dd($this->argument('group_id'));
        dd(123);
    }
}
