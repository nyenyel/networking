<?php

namespace Database\Seeders;

use App\Models\Libraries\LibTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LibTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        LibTransaction::upsert([
            ['id' => '1', 'desc' => 'Points Redeem'],
            ['id' => '2', 'desc' => 'Company Revenue'],
        ],['id']);
    }
}
