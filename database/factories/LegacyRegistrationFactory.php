<?php

namespace Database\Factories;

use App\Models\LegacyRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyRegistrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_aluno' => static fn () => LegacyStudentFactory::new()->create(),
            'ref_ref_cod_serie' => static fn () => LegacyGradeFactory::new()->create(),
            'ref_ref_cod_escola' => static fn () => LegacySchoolFactory::new()->create(),
            'ref_cod_curso' => static fn () => LegacyCourseFactory::new()->create(),
            'data_cadastro' => now(),
            'ano' => now()->year,
            'ref_usuario_cad' => 1,
            'aprovado' => 3,
        ];
    }
}
