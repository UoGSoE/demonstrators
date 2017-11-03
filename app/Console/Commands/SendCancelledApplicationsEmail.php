<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class SendCancelledApplicationsEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demonstrators:applicationcancelled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel any neglected applications and notify student and academic.';

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
        User::students()->get()->each->cancelIgnoredApplications();
    }
}
