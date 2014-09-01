<?php

class AdminHome extends Control {
    protected $js = array("home", "processamento");
    public function __construct($params, $interface = "InterfaceHome_Admin.html") {
        parent::__construct($params, $interface);
    }

    public function index(){
        session_destroy();
        $this->view->setVariable("saudacao", "As alterações efetuadas aqui podem influenciar no seu sistema, tenha responsabilidade.");
        
        return $this->view;
    }
}
?>
