<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class SendNewApplicationsEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demonstrators:newapplications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email to each academic that contains a list of all their new applications for their requests.';

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
        User::staff()->get()->each->sendNewApplicantsEmail();
    }
}
