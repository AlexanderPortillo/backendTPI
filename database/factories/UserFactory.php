<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => $first_name = $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'user_name' => $user_name = Str::slug($first_name . Str::random(4)),
            'age' => $this->faker->numberBetween(15, 60),
            'gender' => $this->faker->word(),
            'img_profile' => '../public/img/profile.png',
            'email' => $this->faker->unique()->email(),
            'password' => $this->faker->password(8, 10),
            'country' => $this->faker->word(),
            'main_address' => $this->faker->word(),
            'shipping_address' => $this->faker->word(),
            'rol' => 0,
            // 'rol' => $this->faker->numberBetween(0, 1),
            'referral_link' => Str::slug($user_name . $first_name . Str::random(6)),
        ];
    }
}
