<?php

namespace Database\Factories;

use App\Models\LegacyTransferType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegacyDisciplinaryOccurrenceType>
 */
class LegacyTransferTypeFactory extends Factory
{
    protected $model = LegacyTransferType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'ref_usuario_exc' => LegacyUserFactory::new()->unique()->make(),
            'nm_tipo' => $this->faker->firstName(),
            'desc_tipo' => $this->faker->paragraph(),
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->unique()->make()
        ];
    }
}
