<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userType = $this->faker->randomElement(['admin', 'pegawai', 'dosen', 'mahasiswa']);

        $identityData = [];
        if ($userType === 'admin' || $userType === 'pegawai') {
            $identityData = [
                'username' => fake()->userName(),
                'identity_number' => null,
            ];
        } else {
            $identityData = [
                'username' => fake()->userName(),
                'identity_number' => $userType === 'dosen'
                    ? $this->faker->numerify('NUP####')
                    : $this->faker->numerify('NIM########'),
            ];
        }

        return [
            'name' => fake()->name(),
            'username' => $identityData['username'],
            'identity_number' => $identityData['identity_number'],
            'user_type' => $userType,
            'vehicle_type' => $this->faker->randomElement(['motor', 'car']),
            'vehicle_plate_number' => $this->faker->bothify('??-####-??'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Admin user
     */
    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'username' => 'admin',
                'identity_number' => null,
                'user_type' => 'admin',
                'vehicle_type' => 'motor',
                'vehicle_plate_number' => $this->faker->bothify('??-####-??'),
            ];
        });
    }

    /**
     * Pegawai user
     */
    public function pegawai(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => 'pegawai',
                'vehicle_type' => 'motor',
                'vehicle_plate_number' => $this->faker->bothify('??-####-??'),
            ];
        });
    }

    /**
     * Dosen user
     */
    public function dosen(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => 'dosen',
                'identity_number' => $this->faker->numerify('NIP####'),
                'vehicle_type' => 'motor',
                'vehicle_plate_number' => $this->faker->bothify('??-####-??'),
            ];
        });
    }

    /**
     * Mahasiswa user
     */
    public function mahasiswa(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => 'mahasiswa',
                'identity_number' => $this->faker->numerify('NIM########'),
                'vehicle_type' => 'motor',
                'vehicle_plate_number' => $this->faker->bothify('??-####-??'),
            ];
        });
    }
}
