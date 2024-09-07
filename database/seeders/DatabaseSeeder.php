<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\User\StoreInfo;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(20)->create()->each(function ($user) {
            $storeInfo = StoreInfo::factory()->create([
                'user_id' => $user->id,
                'invited_by' => User::inRandomOrder()->first()->id,
                'points' => 0,
                'status' => true,
            ]);

            DB::table('invited_users')->insert([
                'user_id' => $storeInfo->invited_by,
                'invited_user' => $storeInfo->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
