<?php

namespace Database\Factories\User;

use App\Models\User;
use App\Models\User\StoreInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User\StoreInfo>
 */
class StoreInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'user_id' => User::factory(),
            'invited_by' => User::factory(),
            'points' => $this->faker->randomFloat(2, 0, 100),
            'status' => $this->faker->boolean(),
        ];
    }
}
