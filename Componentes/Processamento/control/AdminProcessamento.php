<?php
class AdminProcessamento extends Control {
    protected $css = array("processamento.css");
    protected $js = array("processamento");
    protected $usuario;

    public function __construct($params, $interface = "InterfaceProcessamento.html") {
        parent::__construct($params, $interface);
    }

    public function index() {
        
        $processamentos = ProcessamentoDAO::consultarProcessamentos(new ModelProcessamento());
        if($processamentos){
            $this->view->setVariable("count", count($processamentos));
            //_debug($processamentos);
            $this->view->setCurrentBlock("Processamentos");
            foreach ($processamentos as $processamento){
                $this->view->setVariable("id_transacao",    $processamento->get("id_transacao"));
                $this->view->setVariable("nome_credor",     $processamento->get("nome_credor"));
                $this->view->setVariable("conta_credor",    $processamento->get("conta_credor"));
                $this->view->setVariable("agencia_credor",  $processamento->get("agencia_credor"));
                $this->view->setVariable("banco_credor",    $processamento->get("banco_credor"));
                $this->view->setVariable("nome_devedor",    $processamento->get("nome_devedor"));
                $this->view->setVariable("conta_devedor",   $processamento->get("conta_devedor"));
                $this->view->setVariable("agencia_devedor", $processamento->get("agencia_devedor"));
                $this->view->setVariable("banco_devedor",   $processamento->get("banco_devedor"));
                $this->view->setVariable("data_transacao",  $processamento->getDateTime("dt_aceite_processamento"));
                $this->view->setVariable("valor",           "R$ ".money_format('%n', $processamento->get("valor")));
                $this->view->parseCurrentBlock();
            }
        }else{
            $this->view->touchBlock("Nenhum_Processamento");
        }
        
        return $this->view;
    }
    
    public function processarTransacao(){
        if($this->params["id_transacao"]){
            $modelProcessamento = new ModelProcessamento();
            $modelProcessamento->set("id_transacao", $this->params["id_transacao"]);
            $modelProcessamento->set("status", ModelProcessamento::STATUS_TRANSFERIDO);
            $modelProcessamento->set("aceite", ModelProcessamento::ACEITE_SIM);
            $modelProcessamento->set("dt_aceite", date("Y-m-d H:i:s"));
            //_debug($modelProcessamento);
            return ProcessamentoDAO::incluir($modelProcessamento);
        }
    }

}

?>
