<?php
class AdminUsuario_Administrador extends Control {
    protected $js  = array("usuario_administrador");
    protected $css = array("usuario_administrador.css");
    protected $usuario;

    public function __construct($params, $interface = "InterfaceUsuario_Administrador.html") {
        $this->usuario = new ModelUsuario_Administrador();
        parent::__construct($params, $interface);
    }

    public function index() {
        $this->view->setVariable("id_usuario_logado", ControleSessao::$id_usuario);
        $this->view->touchBlock("Cabeca");
        $this->dadosUsuario();
        return $this->view;
    }

    public function dadosUsuario(){
        $usuarios = Usuario_AdministradorDAO::consultar(new ModelUsuario_Administrador());
        if($usuarios) {
            $this->view->touchBlock("Conteudo_Usuarios");
            $this->view->setCurrentBlock("Usuarios");
            foreach ($usuarios as $usuario) {
                $this->view->setVariable("id_usuario", $usuario->get("id_usuario_administrador"));
                $this->view->setVariable("nome", $usuario->get("nome"));
                $this->view->setVariable("email", $usuario->get("email"));
                $this->view->setVariable("login", $usuario->get("login"));
                $this->view->setVariable("ativo", $usuario->get("ativo") == 1 ? "sim" : "não");
                $this->view->parseCurrentblock();
            }
        }else {
            $this->view->touchBlock("nenhumUsuario");
        }
        return $this->view;
    }

    public function cadastrarUsuario() {
        if(($this->params["nome_usuario"]) && ($this->params["email_usuario"]) && ($this->params["login_usuario"])) {
            $this->usuario->set("login", $this->params["login_usuario"]);
            $this->usuario->set("nome", $this->params["nome_usuario"]);
            $this->usuario->set("email", removeAcentos(strtolower($_POST["email_usuario"])));
            if($this->params["ativo"]) {
                $this->usuario->set("ativo", '1');
            }else {
                $this->usuario->set("ativo", '0');
            }
            if($this->params["senha_usuario"]){
                $this->usuario->set("alterar_senha", true);
                $this->usuario->set("senha", md5($this->params["senha_usuario"]));
            }
            if(!$this->params["id_usuario"]) {
                Usuario_AdministradorDAO::incluir($this->usuario);
                $lastId = Usuario_AdministradorDAO::ultimoId();
                $this->usuario->set("id_usuario", $lastId[0]->get("id_usuario_administrador"));
            }else {
                $this->usuario->set("id_usuario_administrador", $this->params["id_usuario"]);
                Usuario_AdministradorDAO::alterar($this->usuario);
            }

            $this->redirect('Usuario_Administrador');
        }
    }

    public function buscarDadosUsuario() {
        if($_POST["id_usuario"]) {
            $usuario = Usuario_AdministradorDAO::consultar(new ModelUsuario_Administrador(array("id_usuario_administrador" => $_POST["id_usuario"])));
            if($usuario) {
                return $usuario[0];
            }else {
                return false;
            }
        }
    }

    public function checarEmail() {
        if($this->params["id_usuario"] && $this->params["email_usuario"]) {
            $modelUsuario = new ModelUsuario_Administrador(array("email" => removeAcentos(strtolower($this->params["email_usuario"])), "id_usuario_administrador" => $this->params["id_usuario"]));
        }else {
            if($this->params["email_usuario"]) {
                $modelUsuario = new ModelUsuario_Administrador(array("email" => removeAcentos(strtolower($this->params["email_usuario"]))));
            }
        }
        $usuario = Usuario_AdministradorDAO::consultarEmailUsuario($modelUsuario);
        if(!$usuario) {
            return true;
        }else {
            return false;
        }
    }

    public function checarLogin() {
        if($this->params["id_usuario"] && $this->params["login_usuario"]) {
            $modelUsuario = new ModelUsuario_Administrador(array("login" => strtolower($this->params["login_usuario"]), "id_usuario_administrador" => $this->params["id_usuario"]));
        }else {
            if($this->params["login_usuario"]) {
                $modelUsuario = new ModelUsuario_Administrador(array("login" => strtolower($this->params["login_usuario"])));
            }
        }
        $usuario = Usuario_AdministradorDAO::consultarloginUsuario($modelUsuario);
        if(!$usuario) {
            return true;
        }else {
            return false;
        }
    }

    public function alterarSenha() {
        if($this->params["nova_senha"] && $this->params["id_usuario_senha"]) {
            $usuario = Usuario_AdministradorDAO::alterar(new ModelUsuario_Administrador(array("senha" => md5($this->params["nova_senha"]), "id_usuario_administrador" => $this->params["id_usuario_senha"])));
            if($usuario) {
                return true;
            }else {
                return false;
            }
        }
    }

    public function excluirUsuario() {
        if($this->params["id_usuario"]) {
            $usuario = Usuario_AdministradorDAO::remover(new ModelUsuario_Administrador(array("id_usuario_administrador" => $this->params["id_usuario"])));
            if($usuario) {
                return true;
            }else {
                return false;
            }
        }
    }

    public function logout() {
        _debug('oi'); die();
        session_unset();
        session_destroy();

        header("location:/");
        exit();
    }

}

?>
