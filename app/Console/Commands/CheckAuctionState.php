<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Auction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckAuctionState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auction:check-auction-state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the auction state if necessary.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Find auctions that have started
        DB::table('auction')
            ->where('initdate', '<=', $now)
            ->where('state', '=', 'Created')
            ->update(['state' => 'Started', 'initdate' => DB::raw('initdate')]);

        // Find and update auctions that have ended
        DB::table('auction')
            ->where('closedate', '<=', $now)
            ->where('state', '=', 'Started')
            ->update(['state' => 'Ended', 'closedate' => DB::raw('closedate')]);
    }
}
