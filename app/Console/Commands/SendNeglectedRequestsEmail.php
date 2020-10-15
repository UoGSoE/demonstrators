<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SendNeglectedRequestsEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demonstrators:neglectedrequests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email to academics if they have negelected requests.';

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
        User::staff()->get()->each->notifyAboutOutstandingRequests();
    }
}
