<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AwardBirthdayPoints extends Command
{
    protected $signature = 'loyalty:birthdays';
    protected $description = 'Award 100 loyalty points to users whose birthday is today';

    public function handle()
    {
        $today = now()->format('-m-d'); // matches the month and day part of DATE

        $users = User::whereNotNull('dob')
            ->where('dob', 'like', "%$today")
            ->get();

        $count = 0;
        foreach ($users as $user) {
            // Prevent duplicate awards for the same year
            $alreadyAwardedThisYear = $user->loyaltyPoints()
                ->where('type', 'earned')
                ->where('description', 'like', 'Birthday bonus%')
                ->whereYear('created_at', now()->year)
                ->exists();

            if (!$alreadyAwardedThisYear) {
                $user->addPoints(100, 'earned', 'Birthday bonus 🎉');
                $count++;
            }
        }

        $this->info("Awarded birthday bonus to $count users.");
    }
}
