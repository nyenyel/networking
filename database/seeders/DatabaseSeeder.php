<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\User\StoreInfo;
use App\Models\User\InvitationCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            LibTransactionSeeder::class,
            WeeklyMonitoringDashboardSeeder::class,
            WalletSeeder::class
        ]);

        // Create users and store info for each
        User::factory(5)->create()->each(function ($user) {
            // Create store info for the new user
            $storeInfo = StoreInfo::factory()->create([
                'user_id' => $user->id,
                'invited_by' => User::inRandomOrder()->first()->id, // Assuming you want random invitation links
                'points' => 0,
                'status' => true,
            ]);

            // Insert data into invited_users table
            DB::table('invited_users')->insert([
                'user_id' => $storeInfo->invited_by,
                'invited_user' => $storeInfo->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Generate a unique invitation code for the user
            $invitationCode = Str::random(20); // Generate random 8-character string

            // Insert the invitation code into the invitation_codes table
            InvitationCode::create([
                'user_id' => $user->id,
                'code' => $invitationCode,
                'used_count' => 0, // Initialize usage count to 0
            ]);
        });
    }
}
