<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\Users\UserRole;
use App\Enums\Users\UserStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'role' => UserRole::EMPLOYEE->value,
            'status' => UserStatus::ACTIVE->value,
            'email_verified_at' => Carbon::now(),
            'profile_image' => null,
            'phone_number' => $this->faker->phoneNumber,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Indicate that the user is an admin.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => UserRole::ADMIN->value,
                'email' => 'admin@admin.com',
            ];
        });
    }

    /**
     * Indicate that the user is an HR.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function hr()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => UserRole::HR->value,
                'email' => 'hr@admin.com',
            ];
        });
    }
}
