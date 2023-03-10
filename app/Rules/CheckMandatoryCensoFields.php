<?php

namespace App\Rules;

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App_Model_LocalFuncionamentoDiferenciado;
use App_Model_TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\Model\EstruturaCurricular;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use Illuminate\Contracts\Validation\Rule;

class CheckMandatoryCensoFields implements Rule
{
    public const ETAPAS_ENSINO_REGULAR = [
        1,
        2,
        3,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        25,
        26,
        27,
        28,
        29,
        35,
        36,
        37,
        38,
        41,
        56
    ];

    public const ETAPAS_ESPECIAL_SUBSTITUTIVAS = [
        1,
        2,
        3,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        25,
        26,
        27,
        28,
        29,
        30,
        31,
        32,
        33,
        34,
        35,
        36,
        37,
        38,
        41,
        56,
        39,
        40,
        69,
        70,
        71,
        72,
        73,
        74,
        64,
        67,
        68
    ];

    public string $message = '';

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $params
     *
     * @return bool
     */
    public function passes($attribute, $params)
    {
        if ($this->validarCamposObrigatoriosCenso($params->ref_cod_instituicao)) {
            if (!$this->validaCamposHorario($params)) {
                return false;
            }
            if (!$this->validaEtapaEducacenso($params)) {
                return false;
            }
            if (!$this->validaCampoAtividadesComplementares($params)) {
                return false;
            }
            if (!$this->validaCampoEstruturaCurricular($params)) {
                return false;
            }
            if (!$this->validaCampoEtapaEnsino($params['tipo_atendimento'])) {
                return false;
            }
            if (!$this->validaCampoFormasOrganizacaoTurma($params)) {
                return false;
            }
            if (!$this->validaCampoUnidadeCurricular($params)) {
                return false;
            }
            if (!$this->validaCampoTipoAtendimento($params)) {
                return false;
            }
            if (!$this->validaCampoLocalFuncionamentoDiferenciado($params)) {
                return false;
            }
        }

        return true;
    }

    protected function validarCamposObrigatoriosCenso($refCodInstituicao)
    {
        return (new LegacyInstitution())::query()
            ->find(['cod_instituicao' => $refCodInstituicao])
            ->first()
            ->isMandatoryCensoFields();
    }

    protected function validaCamposHorario($params)
    {
        if ($params->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL) {
            if (empty($params->hora_inicial)) {
                $this->message = 'O campo hora inicial ?? obrigat??rio';

                return false;
            }
            if (empty($params->hora_final)) {
                $this->message = 'O campo hora final ?? obrigat??rio';

                return false;
            }
            if (empty($params->hora_inicio_intervalo)) {
                $this->message = 'O campo hora in??cio intervalo ?? obrigat??rio';

                return false;
            }
            if (empty($params->hora_fim_intervalo)) {
                $this->message = 'O campo hora fim intervalo ?? obrigat??rio';

                return false;
            }
            if (empty($params->dias_semana)) {
                $this->message = 'O campo dias da semana ?? obrigat??rio';

                return false;
            }
        }

        return true;
    }

    private function validaEtapaEducacenso($params)
    {
        $course = LegacyCourse::find($params->ref_cod_curso);
        $estruturaCurricular = $this->getEstruturaCurricularValues($params);

        if (empty($params->etapa_educacenso) &&
            is_array($estruturaCurricular) &&
            (in_array(1, $estruturaCurricular, true) || in_array(3, $estruturaCurricular, true))) {
            $this->message = 'O campo <b>"Etapa de ensino"</b> deve ser obrigat??rio quando o campo "Estrutura curricular" for preenchido com "Forma????o geral b??sica" ou "N??o se aplica"';

            return false;
        }

        if ((int)$course->modalidade_curso === ModalidadeCurso::ENSINO_REGULAR &&
            isset($params->etapa_educacenso) &&
            !in_array((int)$params->etapa_educacenso, self::ETAPAS_ENSINO_REGULAR)) {
            $this->message = 'Quando a modalidade do curso ??: Ensino regular, o campo: Etapa de ensino deve ser uma das seguintes op????es:'
                . implode(',', self::ETAPAS_ENSINO_REGULAR) . '.';

            return false;
        }

        if ((int)$course->modalidade_curso === ModalidadeCurso::EDUCACAO_ESPECIAL &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, self::ETAPAS_ESPECIAL_SUBSTITUTIVAS)) {
            $this->message = 'Quando a modalidade do curso ??: Educa????o especial, o campo: Etapa de ensino deve ser uma das seguintes op????es:'
                . implode(',', self::ETAPAS_ESPECIAL_SUBSTITUTIVAS) . '.';

            return false;
        }

        if ((int)$course->modalidade_curso === ModalidadeCurso::EJA &&
            isset($params->etapa_educacenso) &&
            !in_array($params->etapa_educacenso, [69, 70, 71, 72])) {
            $this->message = 'Quando a modalidade do curso ??: Educa????o de Jovens e Adultos (EJA), o campo: Etapa de ensino deve ser uma das seguintes op????es: 69, 70, 71 ou 72.';

            return false;
        }

        if ($course->modalidade_curso == 4 &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, [30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67, 68])) {
            $this->message = 'Quando a modalidade do curso ??: Educa????o Profissional, o campo: Etapa de ensino deve ser uma das seguintes op????es: 30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67 ou 68.';

            return false;
        }

        if ($params->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL &&
            isset($params->etapa_educacenso) &&
            !in_array($params->etapa_educacenso, [69, 70, 71, 72])) {
            $this->message = 'Quando o campo: Tipo de media????o did??tico-pedag??gica ??: Semipresencial, o campo: Etapa de ensino deve ser uma das seguintes op????es: 69, 70, 71 ou 72.';

            return false;
        }

        if ($params->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 70, 71, 73, 74, 67, 68], true)) {
            $this->message = 'Quando o campo: Tipo de media????o did??tico-pedag??gica ??: Educa????o a Dist??ncia, o campo: Etapa de ensino deve ser uma das seguintes op????es: 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 70, 71, 73, 74, 67 ou 68';

            return false;
        }

        $localDeFuncionamentoData = [
            App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO,
            App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL
        ];

        if (in_array($params->local_funcionamento_diferenciado, $localDeFuncionamentoData) &&
            isset($params->etapa_educacenso) &&
            in_array($params->etapa_educacenso, [1, 2, 3, 56])) {
            $nomeOpcao = (App_Model_LocalFuncionamentoDiferenciado::getInstance()->getEnums())[$params->local_funcionamento_diferenciado];
            $this->message = "Quando o campo: Local de funcionamento diferenciado ??: {$nomeOpcao}, o campo: Etapa de ensino n??o pode ser nenhuma das seguintes op????es: 1, 2, 3 ou 56.";

            return false;
        }

        return true;
    }

    protected function validaCampoAtividadesComplementares($params)
    {
        if ($params->tipo_atendimento == TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR
            && empty($params->atividades_complementares)
        ) {
            $this->message = 'Campo atividades complementares ?? obrigat??rio.';

            return false;
        }

        return true;
    }

    protected function validaCampoEtapaEnsino($tipoAtendimento)
    {
        if (!empty($tipoAtendimento) && !in_array($tipoAtendimento, [-1, 4, 5])) {
            $this->message = 'Campo etapa de ensino ?? obrigat??rio';

            return false;
        }

        return true;
    }

    protected function validaCampoTipoAtendimento($params)
    {
        if ($params->tipo_atendimento != TipoAtendimentoTurma::ESCOLARIZACAO && in_array(
            $params->tipo_mediacao_didatico_pedagogico,
            [
                App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL,
                App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA
            ]
        )) {
            $this->message = 'O campo: Tipo de atendimento deve ser: Escolariza????o quando o campo: Tipo de media????o did??tico-pedag??gica for: Semipresencial ou Educa????o a Dist??ncia.';

            return false;
        }

        $course = LegacyCourse::find($params->ref_cod_curso);
        if ((int)$params->tipo_atendimento === TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR && (int) $course->modalidade_curso === ModalidadeCurso::EJA) {
            $this->message = 'Quando a modalidade do curso ??: <b>Educa????o de Jovens e Adultos (EJA)</b>, o campo <b>Tipo de atendimento</b> n??o pode ser <b>Atividade complementar</b>';

            return false;
        }

        return true;
    }

    protected function validaCampoLocalFuncionamentoDiferenciado($params)
    {
        $school = LegacySchool::find($params->ref_ref_cod_escola);
        $localFuncionamentoEscola = $school->local_funcionamento;
        if (is_string($localFuncionamentoEscola)) {
            $localFuncionamentoEscola = explode(',', str_replace(['{', '}'], '', $localFuncionamentoEscola));
        }

        $localFuncionamentoEscola = (array)$localFuncionamentoEscola;

        if (!in_array(
            9,
            $localFuncionamentoEscola
        ) && $params->local_funcionamento_diferenciado == App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO) {
            $this->message = 'N??o ?? poss??vel selecionar a op????o: Unidade de atendimento socioeducativo quando o local de funcionamento da escola n??o for: Unidade de atendimento socioeducativo.';

            return false;
        }

        if (!in_array(
            10,
            $localFuncionamentoEscola
        ) && $params->local_funcionamento_diferenciado == App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL) {
            $this->message = 'N??o ?? poss??vel selecionar a op????o: Unidade prisional quando o local de funcionamento da escola n??o for: Unidade prisional.';

            return false;
        }

        return true;
    }

    public function validaCampoEstruturaCurricular(mixed $params)
    {
        $estruturaCurricular = $this->getEstruturaCurricularValues($params);

        if (is_array($estruturaCurricular) && in_array(2, $estruturaCurricular, true) && count($estruturaCurricular) === 1) {
            $params->etapa_educacenso = null;
        }

        if ($params->tipo_atendimento == TipoAtendimentoTurma::ESCOLARIZACAO && empty($estruturaCurricular)) {
            $this->message = 'Campo "Estrutura Curricular" ?? obrigat??rio quando o campo tipo de atentimento ?? "Escolariza????o".';

            return false;
        }

        if (is_array($estruturaCurricular) && count($estruturaCurricular) > 1 && in_array(3, $estruturaCurricular, true)) {
            $this->message = 'N??o ?? poss??vel informar mais de uma op????o no campo: <b>Estrutura curricular</b>, quando a op????o: <b>N??o se aplica</b> estiver selecionada';

            return false;
        }

        if (
            is_array($estruturaCurricular) &&
            !in_array(EstruturaCurricular::FORMACAO_GERAL_BASICA, $estruturaCurricular, true) &&
            $params->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL
        ) {
            $this->message = 'Quando o campo: <b>Tipo de media????o did??tico-pedag??gica</b> ??: <b>Semipresencial</b>, o campo: <b>Estrutura curricular</b> deve ter a op????o <b>Forma????o geral b??sica</b> informada.';

            return false;
        }

        $etapaEnsinoCanNotContainsWithFormacaoGeralBasica = [1, 2, 3, 39, 40, 64, 68];
        if (is_array($estruturaCurricular) &&
            in_array(1, $estruturaCurricular, true) &&
            isset($params->etapa_educacenso) &&
            in_array((int) $params->etapa_educacenso, $etapaEnsinoCanNotContainsWithFormacaoGeralBasica)) {
            $this->message = 'Quando o campo: <b>Estrutura curricular</b> for preenchido com: <b>Forma????o geral b??sica</b>, o campo: <b>Etapa de ensino</b> deve ser uma das seguintes op????es: 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 41, 69, 70, 71, 72, 56, 73, 74 ou 67';

            return false;
        }

        $etapaEnsinoCanNotContainsWithItinerarioFormativo = [1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 39, 40, 41, 56, 64, 68, 69, 70, 72, 73];
        if (is_array($estruturaCurricular) &&
            in_array(2, $estruturaCurricular, true) &&
            isset($params->etapa_educacenso) &&
            in_array((int) $params->etapa_educacenso, $etapaEnsinoCanNotContainsWithItinerarioFormativo)) {
            $this->message = 'Quando o campo: <b>Estrutura curricular</b> for preenchido com: <b>Itiner??rio formativo</b>, o campo: <b>Etapa de ensino</b> deve ser uma das seguintes op????es: 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 71, 74, 67';

            return false;
        }

        $etapaEnsinoCanNotContainsWithNaoSeAplica = [14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 56, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 69, 70, 72, 71, 73, 67, 74];
        if (is_array($estruturaCurricular) &&
            in_array(3, $estruturaCurricular, true) &&
            isset($params->etapa_educacenso) &&
            in_array((int) $params->etapa_educacenso, $etapaEnsinoCanNotContainsWithNaoSeAplica)) {
            $this->message = 'Quando o campo: <b>Estrutura curricular</b> for preenchido com: <b>N??o se aplica</b>, o campo: <b>Etapa de ensino</b> deve ser uma das seguintes op????es: 1, 2, 3, 24, 39, 40, 64, 68';

            return false;
        }

        return true;
    }

    private function validaCampoFormasOrganizacaoTurma(mixed $params)
    {
        $validOption = [
            1 => 'S??rie/ano (s??ries anuais)',
            2 => 'Per??odos semestrais',
            3 => 'Ciclo(s)',
            4 => 'Grupos n??o seriados com base na idade ou compet??ncia',
            5 => 'M??dulos',
            6 => 'Altern??ncia regular de per??odos de estudos'
        ];

        $validOptionCorrelationForEtapaEnsino = [
            FormaOrganizacaoTurma::SERIE_ANO => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 56, 64, 69, 70, 71, 72, 73, 74, 67
            ],
            FormaOrganizacaoTurma::SEMESTRAL => [
                25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 69, 70, 71, 72, 73, 74, 67, 68
            ],
            FormaOrganizacaoTurma::CICLOS => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 56
            ],
            FormaOrganizacaoTurma::NAO_SERIADO => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 56, 64, 69, 70, 71, 72, 73, 74, 67, 68
            ],
            FormaOrganizacaoTurma::MODULES => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 56, 64, 69, 70, 71, 72, 73, 74, 67,68
            ],
            FormaOrganizacaoTurma::ALTERNANCIA_REGULAR => [
                19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 69, 70, 71, 72, 73, 74, 67, 68
            ]
        ];

        if (isset($params->formas_organizacao_turma) &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, $validOptionCorrelationForEtapaEnsino[(int)$params->formas_organizacao_turma], true)
        ) {
            $todasEtapasEducacenso = loadJson(__DIR__ . '/../../ieducar/intranet/educacenso_json/etapas_ensino.json');
            $this->message = "N??o ?? poss??vel selecionar a op????o: <b>{$validOption[(int)$params->formas_organizacao_turma]}</b>, no campo: <b>Formas de organiza????o da turma</b> quando o campo: Etapa de ensino for: {$todasEtapasEducacenso[$params->etapa_educacenso]}.";

            return false;
        }

        return true;
    }

    private function validaCampoUnidadeCurricular(mixed $params): bool
    {
        $estruturaCurricular = $this->getEstruturaCurricularValues($params);

        if (empty($estruturaCurricular)) {
            return true;
        }

        if (empty($params->unidade_curricular) && !in_array(2, $estruturaCurricular, true)) {
            return true;
        }

        if (empty($params->unidade_curricular) && in_array(2, $estruturaCurricular, true)) {
            $this->message = 'Campo: <b>Unidade curricular</b> ?? obrigat??rio quando o campo: <b>Estrutura Curricular cont??m: Itiner??rio formativo</b>';

            return false;
        }

        if (!empty($params->unidade_curricular) && !in_array(2, $estruturaCurricular, true)) {
            $this->message = 'Campo: <b>Unidade curricular</b> n??o pode ser preenchido quando o campo: <b>Estrutura Curricular n??o cont??m: Itiner??rio formativo</b>';

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    private function getEstruturaCurricularValues(mixed $params): ?array
    {
        if ($params->estrutura_curricular === null) {
            return null;
        }

        return array_map(
            'intval',
            explode(',', str_replace(['{', '}'], '', $params->estrutura_curricular))
                ?: []
        );
    }
}
