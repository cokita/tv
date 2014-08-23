<?php
class AdminUsuario_Detalhe extends Control {
    protected $usuario;

    public function __construct($params, $interface = "InterfaceUsuario_Detalhe.html") {
        $this->usuario = new ModelUsuario();
        
        if(ControleSessao::$id){
            $this->usuario->set("id_usuario", ControleSessao::$id);
        }else if($this->params["id_usuario"]){
            $this->usuario->set("id_usuario", $this->params["id_usuario"]);
        }
        parent::__construct($params, $interface);
    }

    public function index() {
        $this->dadosUsuario();
        return $this->view;
    }

    public function dadosUsuario(){
        $modelTransacao = new ModelTransacao();
        $modelTransacao->set("id_usuario", $this->usuario->get("id_usuario"));
        $dadosUsuario = UsuarioDAO::consultarDadosUsuario($this->usuario);
        if($dadosUsuario){
            $this->view->setVariable("nome_usuario", $dadosUsuario[0]->get("nome"));
            $this->view->setVariable("cpf_usuario", $dadosUsuario[0]->get("cpf"));
            $this->view->setVariable("agencia_usuario", $dadosUsuario[0]->get("agencia"));
            $this->view->setVariable("conta_usuario", $dadosUsuario[0]->get("conta"));
            $this->view->setVariable("banco_usuario", $dadosUsuario[0]->get("banco"));
            $this->view->setVariable("telefone_usuario", $dadosUsuario[0]->get("cod_pais")." (".$dadosUsuario[0]->get("cod_regiao").") ".$dadosUsuario[0]->get("telefone"));
        }
        $transacoes = TransacaoDAO::consultarTransacoesUsuario($modelTransacao);
        if($transacoes) {
            $this->view->touchBlock("Conteudo_Transacoes");
            $this->view->setCurrentBlock("Transacoes");
            foreach ($transacoes as $transacao) {
                //_debug($transacao);
                
                if($transacao->get("tipo_transacao") == "C"){
                    $nome = $transacao->get("nome_credor");
                    $telefone = $transacao->get("cod_pais_devedor")." (".$transacao->get("cod_regiao_devedor").") ".$transacao->get("telefone_devedor");
                    $tipo_transacao = "Crédito";
                    $class = "credito";
                    $preposicao = "de";
                }else{
                    $class = "debito";
                    $tipo_transacao = "Débito";
                    $telefone = $transacao->get("cod_pais_credor")." (".$transacao->get("cod_regiao_credor").") ".$transacao->get("telefone_credor");
                    $nome = $transacao->get("nome_devedor");
                    $preposicao = "para";
                    
                }
                $this->view->setVariable("id_usuario", $transacao->get("id_usuario"));
                $this->view->setVariable("data", $transacao->getdatetime("dt_aceite_processamento"));
                $this->view->setVariable("nome", $nome);
                $this->view->setVariable("cpf", $cpf);
                $this->view->setVariable("telefone", $telefone);
                $this->view->setVariable("valor", "R$ ".money_format('%n', $transacao->get("valor")));
                $this->view->setVariable("tipo_transacao", $tipo_transacao);
                $this->view->setVariable("class", $class);
                $this->view->setVariable("preposicao", $preposicao);
                $this->view->parseCurrentblock();
            }
        }else {
            $this->view->touchBlock("nenhumaTransacao");
        }
        return $this->view;
    }

}

?>
