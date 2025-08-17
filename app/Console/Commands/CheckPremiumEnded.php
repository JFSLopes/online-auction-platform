<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckPremiumEnded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'premium:check-premium-ended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if premium user is no longer premium.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();

        // Delete all premium entries where user is no longer premium
        DB::table('premium')
        ->where('expirydate', '<=', $today)
        ->delete();
    }
}
