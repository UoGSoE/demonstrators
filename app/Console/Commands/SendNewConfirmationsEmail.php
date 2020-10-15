<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SendNewConfirmationsEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demonstrators:newconfirmations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email to each academic that contains 
    a list of all the confirmed/accepted positions by students.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        User::staff()->get()->each->sendNewConfirmationsEmail();
    }
}
