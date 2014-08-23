<?php
class AdminUsuario_Visualizar extends Control {
    protected $usuario;
    protected $js = array("usuario_visualizar");

    public function __construct($params, $interface = "InterfaceUsuario_Visualizar.html") {
        $this->usuario = new ModelUsuario();
        parent::__construct($params, $interface);
    }

    public function index() {
        $this->dadosUsuario();
        return $this->view;
    }

    public function dadosUsuario(){
        $usuarios = UsuarioDAO::consultarDadosUsuario(new ModelUsuario());
        if($usuarios) {
            $this->view->touchBlock("Conteudo_Usuarios");
            $this->view->setCurrentBlock("Usuarios");
            foreach ($usuarios as $usuario) {
                $this->view->setVariable("id_usuario", $usuario->get("id_usuario"));
                $this->view->setVariable("nome", $usuario->get("nome"));
                $this->view->setVariable("cpf", $usuario->get("cpf"));
                $this->view->setVariable("telefone", $usuario->get("cod_pais")." (".$usuario->get("cod_regiao").") ".$usuario->get("telefone"));
                $this->view->setVariable("agencia", $usuario->get("agencia"));
                $this->view->setVariable("conta", $usuario->get("conta"));
                $this->view->setVariable("banco", $usuario->get("banco"));
                $this->view->parseCurrentblock();
            }
        }else {
            $this->view->touchBlock("nenhumUsuario");
        }
        return $this->view;
    }

}

?>
