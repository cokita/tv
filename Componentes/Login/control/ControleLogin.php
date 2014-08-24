<?php
class ControleLogin extends Control{
     protected $js  = array("login");
     protected $css = array("login.css");
     protected $usuario;
    public function __construct($params, $interface = "InterfaceLogin.html") {
        $this->usuarioAdmin = new ModelUsuario_Administrador();
        parent::__construct($params, $interface);
    }

    public function index(){
        $this->view->touchBlock("Login");
        return $this->view;
    }

     function checarLogineSenha() {

        $this->usuarioAdmin->set('email', $this->params['email']);
        $this->usuarioAdmin->set('login', $this->params['email']);
        $this->usuarioAdmin->set('senha', $this->params['senha']);
        $resultado = Usuario_AdministradorDAO::consultarLoginSenha($this->usuarioAdmin);

        return $resultado ? 1 : 0;
    }

    function logout() {


        session_start();
        session_unset();
        session_destroy();

        header("location:index.php");
        exit();
    }

}

?>
