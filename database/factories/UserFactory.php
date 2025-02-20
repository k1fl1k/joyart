<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use k1fl1k\joyart\Enums\Gender;
use k1fl1k\joyart\Enums\Role;

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
        return [
            'id' => (string) Str::ulid(), // Generate ULID
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'), // Default password
            'remember_token' => Str::random(10),
            'birthday' => fake()->date('Y-m-d', '2005-01-01'), // Random birthday before 2005
            'gender' => fake()->randomElement(array_column(Gender::cases(), 'value')), // Random gender
            'role' => Role::USER->value, // Default role as USER
            'avatar' => asset('storage/images/' . 'avatar-female.png'), // Random avatar image
            'backdrop' => fake()->imageUrl(1200, 400, 'nature', true, 'backdrop'), // Random backdrop image
            'description' => fake()->sentence(15), // Short user description
            'allow_adult' => fake()->boolean(20), // 20% chance of allowing adult content
        ];
    }

    /**
     * Indicate that the user should be an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::ADMIN->value,
        ]);
    }

    public function male(): static{
        return $this->state(fn (array $attributes) => [
            "avatar" => asset('storage/images/' . 'avatar-male.png'),
        ]);
    }

    /**
     * Indicate that the user should have an unverified email.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
