<?php

use App\Models\LegacyBenefit;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_aluno_beneficio;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_beneficio;
    public $desc_beneficio;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_aluno_beneficio=$_GET['cod_aluno_beneficio'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(581, $this->pessoa_logada, 3, 'educar_aluno_beneficio_lst.php');

        if (is_numeric($this->cod_aluno_beneficio)) {
            $registro = LegacyBenefit::find($this->cod_aluno_beneficio)->toArray();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(581, $this->pessoa_logada, 3);
                //**

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_aluno_beneficio_det.php?cod_aluno_beneficio={$registro['cod_aluno_beneficio']}" : 'educar_aluno_beneficio_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' benefícios de alunos', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_aluno_beneficio', $this->cod_aluno_beneficio);

        // text
        $this->campoTexto('nm_beneficio', 'Benefício', $this->nm_beneficio, 30, 255, true);
        $this->campoMemo('desc_beneficio', 'Descrição Benefício', $this->desc_beneficio, 60, 5, false);
    }

    public function Novo()
    {
        $classType = new LegacyBenefit();
        $classType->ref_usuario_cad = $this->pessoa_logada;
        $classType->nm_beneficio = $this->nm_beneficio;
        $classType->desc_beneficio = $this->desc_beneficio;

        if ($classType->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_aluno_beneficio_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';
        return false;
    }

    public function Editar()
    {
        $classType = LegacyBenefit::find($this->cod_aluno_beneficio);
        $classType->ref_usuario_cad = $this->pessoa_logada;
        $classType->ativo = 1;
        $classType->nm_beneficio = $this->nm_beneficio;
        $classType->desc_beneficio = $this->desc_beneficio;

        if ($classType->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_aluno_beneficio_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';
        return false;
    }

    public function Excluir()
    {
        $classType = LegacyBenefit::find($this->cod_aluno_beneficio);
        $classType->ativo = 0;

        if ($classType->save()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_aluno_beneficio_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';
        return false;
    }

    public function Formular()
    {
        $this->title = 'Benefício Aluno';
        $this->processoAp = '581';
    }
};
