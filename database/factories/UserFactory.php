<?php

namespace Database\Factories;

use App\User;
use App\UserProfile;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    public function configure()
    {
        return $this->afterCreating(function ($user) {
            $user->profile()->save(UserProfile::factory()->make());
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $password;
        return [
        'name' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => Str::random(10),
        'role' => 'user',
        'active' => true
        ];
    }

    public function inactive()
    {
        return $this->state(function () {
            return [
                'active' => false
            ];
        });
    }
}
