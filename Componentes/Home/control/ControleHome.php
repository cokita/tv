<?php

class ControleHome extends Control {
    protected $js  = array("home");

    public function __construct($params, $interface = "InterfaceHome.html") {
        parent::__construct($params, $interface);
    }

    public function index(){
        $this->view->touchBlock("Home");
        $this->recuperarGaleria();
        return $this->view;
    }

    private function recuperarGaleria(){
        $galeriaItemDao = new Galeria_ItemDAO();

        $items = $galeriaItemDao->recuperarItems(new ModelGaleria_Item());
        if($items){
//_debug($items); die();
            $this->view->touchBlock("Conteudo_Galeria");
            $this->view->setCurrentBlock("Galeria");
            $i = 0;
            foreach($items as $item){
                $active = "";
                if($i == 0){
                    $active = "active";
                }
                if($item->get('tipo') == 'v'){
                    $url_code = $this->getYoutubeCode($item->get('caminho'));
                    $html = '<span class="hidden url_code">'.$url_code.'</span>';
                }else{
                    $html = "<img src='".$item->get("caminho")."'>";
                }
                $this->view->setVariable("html",  $html);
                $this->view->setVariable("index",   $i);
                $this->view->setVariable("active",  $active);
                $this->view->setVariable("id",      $item->get("id"));
                $this->view->setVariable("caminho", $item->get("caminho"));
                $this->view->setVariable("duracao", $item->get("duracao"));
                $this->view->setVariable("ordem",   $item->get("ordem"));
                $this->view->setVariable("tipo",    $item->get("tipo"));
                $i++;
                $this->view->parseCurrentblock();
            }
        }else {
            $this->view->touchBlock("nenhumItem");
        }
    }

    public function getYoutubeCode ($string)
    {
        if (preg_match('!(?<=v=)[a-zA-Z0-9_-]+(?=&)|(?<=v\/)[^&\?\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\?\n]+!', $string, $matches)) {
            return $matches[0];
        }
        return NULL;
    }
}
?>
