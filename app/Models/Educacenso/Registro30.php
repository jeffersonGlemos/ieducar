<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\Deficiencias;

class Registro30 implements RegistroEducacenso
{
    public const TIPO_MANAGER = 'manager';
    public const TIPO_TEACHER = 'teacher';
    public const TIPO_STUDENT = 'student';

    public $tipos = [];

    public $registro;

    public $inepEscola;

    public $codigoEscola;

    public $codigoPessoa;

    public $codigoAluno;

    public $codigoServidor;

    public $cpf;

    public $nomePessoa;

    public $dataNascimento;

    public $filiacao;

    public $filiacao1;

    public $filiacao2;

    public $sexo;

    public $raca;

    public $nacionalidade;

    public $paisNacionalidade;

    public $municipioNascimento;

    public $deficiencia;

    public $deficienciaCegueira;

    public $deficienciaBaixaVisao;

    public $deficienciaSurdez;

    public $deficienciaAuditiva;

    public $deficienciaSurdoCegueira;

    public $deficienciaFisica;

    public $deficienciaIntelectual;

    public $deficienciaMultipla;

    public $deficienciaAltasHabilidades;

    public $deficienciaAutismo;

    public $inepAluno;

    public $recursoLedor;

    public $recursoTranscricao;

    public $recursoGuia;

    public $recursoTradutor;

    public $recursoLeituraLabial;

    public $recursoProvaAmpliada;

    public $recursoProvaSuperampliada;

    public $recursoAudio;

    public $recursoLinguaPortuguesaSegundaLingua;

    public $recursoVideoLibras;

    public $recursoBraile;

    public $recursoNenhum;

    public $nis;

    public $certidaoNascimento;

    public $justificativaFaltaDocumentacao;

    public $inepServidor;

    public $codigoInstituicao;

    public $escolaridade;

    public $tipoEnsinoMedioCursado;

    public $formacaoCurso;

    public $formacaoAnoConclusao;

    public $formacaoInstituicao;

    public $complementacaoPedagogica;

    public $posGraduacoes;

    public $posGraduacaoNaoPossui;

    public $countFormacaoContinuada;

    public $formacaoContinuadaCreche;

    public $formacaoContinuadaPreEscola;

    public $formacaoContinuadaAnosIniciaisFundamental;

    public $formacaoContinuadaAnosFinaisFundamental;

    public $formacaoContinuadaEnsinoMedio;

    public $formacaoContinuadaEducacaoJovensAdultos;

    public $formacaoContinuadaEducacaoEspecial;

    public $formacaoContinuadaEducacaoIndigena;

    public $formacaoContinuadaEducacaoCampo;

    public $formacaoContinuadaEducacaoAmbiental;

    public $formacaoContinuadaEducacaoDireitosHumanos;

    public $formacaoContinuadaGeneroDiversidadeSexual;

    public $formacaoContinuadaDireitosCriancaAdolescente;

    public $formacaoContinuadaEducacaoRelacoesEticoRaciais;

    public $formacaoContinuadaEducacaoGestaoEscolar;

    public $formacaoContinuadaEducacaoOutros;

    public $formacaoContinuadaEducacaoNenhum;

    public $email;

    public $paisResidencia;

    public $cep;

    public $municipioResidencia;

    public $localizacaoResidencia;

    public $localizacaoDiferenciada;

    public $nomeEscola;

    public $nomeNacionalidade;

    public $arrayDeficiencias;

    public $recursosProvaInep;

    /**
     * @var Registro60
     */
    public $dadosAluno;

    public $inepPessoa;

    /**
     * @return bool
     */
    public function isManager()
    {
        return isset($this->tipos[self::TIPO_MANAGER]);
    }

    /**
     * @return bool
     */
    public function isTeacher()
    {
        return isset($this->tipos[self::TIPO_TEACHER]);
    }

    /**
     * @return bool
     */
    public function isStudent()
    {
        return isset($this->tipos[self::TIPO_STUDENT]);
    }

    public function semDocumentacao()
    {
        return empty($this->cpf) && empty($this->nis) && empty($this->certidaoNascimento);
    }

    public function getInep()
    {
        if ($this->isStudent()) {
            return $this->inepAluno;
        }

        return $this->inepServidor;
    }

    /**
     * @return integer
     */
    public function deficienciaMultipla()
    {
        $arrayDeficienciasMultiplas = [
            $this->deficienciaCegueira,
            $this->deficienciaBaixaVisao,
            $this->deficienciaSurdez,
            $this->deficienciaAuditiva,
            $this->deficienciaSurdoCegueira,
            $this->deficienciaFisica,
            $this->deficienciaIntelectual,
        ];

        if (empty($this->arrayDeficiencias)) {
            return null;
        }

        return count(array_keys($arrayDeficienciasMultiplas, 1)) > 1 ? 1 : 0;
    }

    /**
     * @return array
     */
    public function cursosDeFormacaoSuperiorExtintos()
    {
        return [
            '145F14' => 'Letras - L??ngua Estrangeira - Licenciatura',
            '145F17' => 'Letras - L??ngua Portuguesa e Estrangeira - Licenciatura',
            '220L03' => 'Letras - L??ngua Portuguesa e Estrangeira - Bacharelado',
            '222L01' => 'Letras - L??ngua Estrangeira - Bacharelado',
            '443C01' => 'Ci??ncia da Terra - Licenciatura',
            '999990' => 'Outro curso de forma????o superior - Licenciatura',
            '999991' => 'Outro curso de forma????o superior - Bacharelado',
            '999992' => 'Outro curso de forma????o superior - Tecnol??gico',
        ];
    }

    /**
     * Remove "Altas habilidades/Superdota????o" do array de defici??ncias informado
     *
     * @param $arrayDeficiencias
     *
     * @return string
     */
    public static function removeAltasHabilidadesArrayDeficiencias($arrayDeficiencias)
    {
        $altasHabilidadesKey = array_search(Deficiencias::ALTAS_HABILIDADES_SUPERDOTACAO, $arrayDeficiencias);

        if ($altasHabilidadesKey !== false) {
            unset($arrayDeficiencias[$altasHabilidadesKey]);
        }

        return $arrayDeficiencias;
    }
}
