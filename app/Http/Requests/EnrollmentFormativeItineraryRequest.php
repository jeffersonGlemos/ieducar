<?php

namespace App\Http\Requests;

use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentFormativeItineraryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $itineraryTypes = array_keys(TipoItinerarioFormativo::getDescriptiveValues());
        $itineraryCompositions = array_keys(TipoItinerarioFormativo::getDescriptiveValuesOfItineraryComposition());

        return [
            'itinerary_type' => 'nullable|array|max:4',
            'itinerary_type.*' => ['required', 'integer', Rule::in($itineraryTypes)],
            'itinerary_composition' => ['nullable', 'array', 'max:4'],
            'itinerary_composition.*' => ['required', 'integer', Rule::in($itineraryCompositions)],
            'itinerary_course' => ['nullable', 'in:1,2'],
            'concomitant_itinerary' => ['nullable', 'boolean'],
        ];
    }

    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();

        $validator->sometimes(
            'itinerary_composition',
            'required',
            function ($input) {
                return in_array(TipoItinerarioFormativo::ITINERARIO_INTEGRADO, $input->itinerary_type ?: []);
            }
        );

        $validator->sometimes(
            'itinerary_course',
            'required',
            function ($input) {
                return in_array(TipoItinerarioFormativo::FORMACAO_TECNICA, $input->itinerary_composition ?: []);
            }
        );

        $validator->sometimes(
            'concomitant_itinerary',
            'required',
            function ($input) {
                return in_array(TipoItinerarioFormativo::FORMACAO_TECNICA, $input->itinerary_composition ?: []);
            }
        );

        return $validator;
    }

    public function messages()
    {
        return [
            'itinerary_type.max' => 'O campo <b>Tipo do itiner??rio formativo</b> n??o pode ter mais de 4 op????es selecionadas.',
            'itinerary_composition.max' => 'O campo <b>Composi????o do itiner??rio formativo integrado</b> n??o pode ter mais de 4 op????es selecionadas.',
            'itinerary_composition.required' => 'O campo <b>Tipo do curso do itiner??rio de forma????o t??cnica e profissional</b> deve ser preenchido quando o campo <b>Composi????o do itiner??rio formativo integrado</b> for <b>Forma????o t??cnica profissional</b>.',
            'itinerary_course.required' => 'O campo <b>Tipo do curso do itiner??rio de forma????o t??cnica e profissional</b> deve ser preenchido quando o campo <b>Composi????o do itiner??rio formativo integrado</b> for <b>Forma????o t??cnica profissional</b>.',
            'concomitant_itinerary.required' => 'O campo <b>Itiner??rio concomitante intercomplementar ?? matr??cula de forma????o geral b??sica</b> deve ser preenchido quando o campo <b>Composi????o do itiner??rio formativo integrado</b> for <b>Forma????o t??cnica profissional</b>.',
        ];
    }

    public function attributes()
    {
        return [
            'itinerary_type' => 'Tipo do itiner??rio formativo',
            'itinerary_composition' => 'Composi????o do itiner??rio formativo integrado',
            'itinerary_course' => 'Tipo do curso do itiner??rio de forma????o t??cnica e profissional',
            'concomitant_itinerary' => 'Itiner??rio concomitante intercomplementar ?? matr??cula de forma????o geral b??sica',
        ];
    }
}
