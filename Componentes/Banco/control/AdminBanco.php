<?php
class AdminBanco extends Control {
    protected $banco;

    public function __construct($params, $interface = "InterfaceBanco.html") {
        parent::__construct($params, $interface);
    }

    public function index() {
        $this->view->touchBlock("Cabeca");
        return $this->view;
    }
    
}

?>
