<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
     {
    //     return [
    //         'name' => $this->faker->name(),
    //         'email' => $this->faker->unique()->safeEmail(),
    //         'email_verified_at' => now(),
    //         'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    //         'remember_token' => Str::random(10),
    //         'role' => $this->faker->randomElement([User::USER_ROLE,User::ADMIN_ROLE]),
    //         'is_verified' => $verfied = $this->faker->randomElement([User::VERIFIED_USER,User::UNVERIFIED_USER]),
    //         'is_active' => $this->faker->randomElement([User::ACTIVE_USER,User::INACTIVE_USER]),
    //         'verification_token' => $verfied == '1' ? null : User::generateVerificationToken()
    //     ];

        return [
            'name' => 'admin',
            'email' => 'admin@yopmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            // 'role' => User::ADMIN_ROLE,
            // 'is_verified' => $verfied = User::VERIFIED_USER,
            // 'is_active' => User::ACTIVE_USER,
            // 'verification_token' => $verfied == '1' ? null : User::generateVerificationToken()
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
