<?php

namespace Database\Factories;

use App\Models\Pet;
use Faker\Provider\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $species = $this->faker->randomElement(['Dog', 'Cat', 'Bird', 'Fish', 'Rabbit', 'Hamster']);
        $gender = $this->faker->randomElement([Person::GENDER_MALE, Person::GENDER_FEMALE, 'unknown']);
        $dateOfBirth = $this->faker->boolean(70) // 70% chance of having a date of birth
            ? Carbon::now()->subYears($this->faker->numberBetween(0, 10))->subMonths($this->faker->numberBetween(0, 11))
            : null;

        return [
            'title' => $this->faker->firstName($gender !== 'unknown' ? $gender : null),
            'image' => "https://placedog.net/500/280?id={$this->faker->numberBetween(1, 100)}",
            'description' => $this->faker->paragraph(3),
            'species' => $species,
            'gender' => $gender,
            'date_of_birth' => $dateOfBirth,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
