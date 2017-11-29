<?php

namespace App\Console\Commands;

use App\User;
use App\Auth\Ldap;
use Illuminate\Console\Command;

class UpdateUserEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demonstrators:fixemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix users emails to be their real one from LDAP';

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
        foreach (User::staff()->get() as $user) {
            try {
                $info = Ldap::lookUp($user->username);
                if ($info['email']) {
                    $user->email = $info['email'];
                    $user->save();
                }
            } catch (\Exception $e) {
                \Log::info("Couldn't look up $user->username in LDAP");
            }
        }
    }
}
