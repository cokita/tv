<?php

class AdminUsuario extends Control {

    protected $js = array("usuario", "libs/jquery/plugins/jform/hints/jquery.tipsy.js");
    protected $css = array("hints/tipsy.css");
    protected $usuario;

    public function __construct($params, $interface = "InterfaceUsuario.html") {
        $this->usuario = new ModelUsuario();
        if (ControleSessao::$id) {
            $this->usuario->set("id_usuario", ControleSessao::$id);
        } else {
            $this->usuario->setNull("id_usuario");
        }
        parent::__construct($params, $interface);
    }

    public function index() {
        if ($this->usuario->get("id_usuario")) {
            $this->view->setVariable("id_usuario", $this->usuario->get("id_usuario"));
            $this->buscarDadosUsuario();
            $this->view->touchBlock("BotaoVoltar");
        }else{
            $this->view->setVariable("ddi_usuario", '55');
            $this->retornarBancos();
        }
        $this->view->touchBlock("Cabeca");
        return $this->view;
    }

    public function cadastrarUsuario() {
        if (($this->params["nome_usuario"]) && ($this->params["cpf_usuario"]) && ($this->params["ddi_usuario"]) &&
                ($this->params["ddd_usuario"]) && ($this->params["telefone_usuario"]) && ($this->params["agencia_usuario"]) && ($this->params["conta_usuario"])) {
            $this->usuario->set("nome", $this->params["nome_usuario"]);
            $this->usuario->set("cpf", "'" . $this->params["cpf_usuario"] . "'");
            $this->usuario->set("cod_pais", $this->params["ddi_usuario"]);
            $this->usuario->set("cod_regiao", $this->params["ddd_usuario"]);
            $this->usuario->set("telefone", $this->params["telefone_usuario"]);
            $this->usuario->set("agencia", $this->params["agencia_usuario"]);
            $this->usuario->set("conta", $this->params["conta_usuario"]);
            $this->usuario->set("id_banco", $this->params["banco_usuario"]);

            if (!$this->params["id_usuario"]) {
                UsuarioDAO::incluir($this->usuario);
                $lastId = UsuarioDAO::ultimoId();
                $this->usuario->set("id_usuario", $lastId[0]->get("id_usuario"));
            } else {
                $this->usuario->set("id_usuario", $this->params["id_usuario"]);
                UsuarioDAO::alterar($this->usuario);
            }
            return $this->usuario;
        }
    }

    public function buscarDadosUsuario() {
        if ($this->usuario->get("id_usuario")) {
            $usuario = UsuarioDAO::consultar(new ModelUsuario(array("id_usuario" => $this->usuario->get("id_usuario"))));
            if ($usuario) {
                $this->view->setVariable("nome_usuario", $usuario[0]->get("nome"));
                $this->view->setVariable("cpf_usuario", $usuario[0]->get("cpf"));
                $this->view->setVariable("ddi_usuario", $usuario[0]->get("cod_pais"));
                $this->view->setVariable("ddd_usuario", $usuario[0]->get("cod_regiao"));
                $this->view->setVariable("telefone_usuario", $usuario[0]->get("telefone"));
                $this->view->setVariable("agencia_usuario", $usuario[0]->get("agencia"));
                $this->view->setVariable("conta_usuario", $usuario[0]->get("conta"));
                $this->retornarBancos($usuario[0]->get("id_banco"));
                return $this->view;
            }
        }
    }

    public function checarCPF() {
        if ($this->params["id_usuario"] && $this->params["cpf_usuario"]) {
            $modelUsuario = new ModelUsuario(array("cpf" => $this->params["cpf_usuario"], "id_usuario" => $this->params["id_usuario"]));
        } else {
            if ($this->params["cpf_usuario"]) {
                $modelUsuario = new ModelUsuario(array("cpf" => removeAcentos(strtolower($this->params["cpf_usuario"]))));
            }
        }
        $usuario = UsuarioDAO::consultarCPFUsuario($modelUsuario);
        if (!$usuario) {
            return true;
        } else {
            return false;
        }
    }

    public function excluirUsuario() {
        if ($this->params["id_usuario"]) {
            $usuario = UsuarioDAO::removerLogico(new ModelUsuario(array("id_usuario" => $this->params["id_usuario"])));
            if ($usuario) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function retornarBancos($id_banco = false) {
        $sucesso = false;
        $modelBanco = new ModelBanco();

        $bancos = BancoDAO::consultar($modelBanco);
          
        if ($bancos) {
            $this->view->setCurrentBlock("Bancos");
            foreach ($bancos as $banco) {
                $this->view->setVariable("id_banco", $banco->get("id_banco"));
                if($banco->get("codigo")){
                    $this->view->setVariable("banco", str_pad($banco->get("codigo"), 3, "0", STR_PAD_LEFT)." - ".$banco->get("descricao"));
                }else{
                    $this->view->setVariable("banco", $banco->get("descricao"));
                }
                
                if ($id_banco == $banco->get("id_banco")) {
                    $this->view->setVariable("selected", "selected='selected'");
                }
                
                $this->view->parseCurrentBlock();
            }
        }
        return $sucesso;
    }
    

}

?>
