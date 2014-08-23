<?php
class AdminIndex extends Control{

     public function __construct($params, $interface = "InterfaceAdminIndex.html") {
        parent::__construct($params, $interface);
    }

    public function index() {
        $this->view->touchBlock("ultimos_combustiveis");
        return $this->view;
    }
}

?>
