<?php

class ControleHome extends Control {


    public function __construct($params, $interface = "InterfaceHome.html") {
        parent::__construct($params, $interface);
    }

    public function index(){
        $this->view->touchBlock("Home");
        return $this->view;
    }
}
?>
