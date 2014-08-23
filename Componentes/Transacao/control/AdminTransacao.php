<?php
class AdminTransacao extends Control {
    protected $usuario;

    public function __construct($params, $interface = "InterfaceTransacao.html") {
        parent::__construct($params, $interface);
    }

    public function index() {
        $this->view->touchBlock("Cabeca");
        return $this->view;
    }

}

?>
