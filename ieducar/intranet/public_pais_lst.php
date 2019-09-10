<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/public/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Pais" );
        $this->processoAp = "753";
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $__titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $__limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $__offset;

    var $idpais;
    var $nome;
    var $geom;

    function Gerar()
    {
        $this->__titulo = "Pais - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Nome"
        ) );

        // Filtros de Foreign Keys


        // outros Filtros
        $this->campoTexto( "nome", "Nome", $this->nome, 30, 60, false );


        // Paginador
        $this->__limite = 20;
        $this->__offset = isset($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite-$this->__limite : 0;

        $obj_pais = new clsPublicPais();
        $obj_pais->setOrderby( "nome ASC" );
        $obj_pais->setLimite( $this->__limite, $this->__offset );

        $lista = $obj_pais->lista(
            null,
            $this->nome
        );

        $total = $obj_pais->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $this->addLinhas( array(
                    "<a href=\"public_pais_det.php?idpais={$registro["idpais"]}\">{$registro["nome"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "public_pais_lst.php", $total, $_GET, $this->nome, $this->__limite );

        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(753, $this->pessoa_logada,7,null,true))
        {
            $this->acao = "go(\"public_pais_cad.php\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_enderecamento_index.php"    => "Endereçamento",
         ""                                  => "Listagem de pa&iacute;ses"
    ));
    $this->enviaLocalizacao($localizacao->montar());
    }
}
// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
