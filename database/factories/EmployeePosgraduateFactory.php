<?php

namespace Database\Factories;

use App\Models\EmployeePosgraduate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeePosgraduateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeePosgraduate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'employee_id' => EmployeeFactory::new()->create(),
            'entity_id' => LegacyInstitutionFactory::new()->unique()->make(),
            'type_id' => $this->faker->randomDigitNotZero(),
            'area_id' => $this->faker->randomDigitNotZero(),
            'completion_year' => now()->year,
        ];
    }
}
