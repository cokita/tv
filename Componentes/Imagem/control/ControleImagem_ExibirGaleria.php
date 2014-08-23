<?php

class ControleImagem_ExibirGaleria extends Control {

    protected $veiculo;
    protected $js = array("libs/jquery/plugins/gallery/jquery.ad-gallery.js", "imagem_gallery");
    protected $css = array("gallery/jquery.ad-gallery.css", "imagem_gallery.css");

    public function __construct($params, $interface = "InterfaceImagem_ExibirGaleria.html") {
        $this->veiculo = new ModelVeiculo();
        parent::__construct($params, $interface);
        if (ControleSessao::$id) {
            $this->veiculo->set("id_veiculo", ControleSessao::$id);
        }else if($params["id_veiculo"]){
            $this->veiculo->set("id_veiculo", $params["id_veiculo"]);
        }
    }

    public function index() {
//        if($this->veiculo->get("id_veiculo")){
//              $xml = new ControleImagem_Galeria($this->params,null, $this->veiculo);
//              $resultado = $xml->retornarXmlGaleria();
//              $this->view->setVariable("xml", $resultado);
//        }
        if ($this->veiculo->get("id_veiculo")) {
            $this->buscarImagensVeiculo();
        } else {
            $this->view->touchBlock("SemIdVeiculo");
        }
        return $this->view;
    }

    public function buscarImagensVeiculo() {
        $imagens = ImagemDAO::consultarImagensPorVeiculo(new ModelImagem_Veiculo(array("id_veiculo" => $this->veiculo->get("id_veiculo"))));
        if ($imagens) {
            $this->view->setCurrentBlock("Imagens");
            $count = 0;
            foreach ($imagens as $imagem){
                $thumb = $imagem->get("caminho") . "thumbs/" . $imagem->get("imagem");
                $image = $imagem->get("caminho") . $imagem->get("imagem");
                $this->view->setVariable("img", $image);
                $this->view->setVariable("thumb", $thumb);
                $this->view->setVariable("count", $count);
                $this->view->setVariable("titulo", $imagem->get("titulo"));
                $count++;
                $this->view->parseCurrentBlock();
            }
        }else{
            $this->view->touchBlock("SemImagens");
        }
        return $this->view;
    }

    public function dadosVeiculo($id_veiculo) {
        $veiculo = VeiculoDAO::consultarDadosVeiculo(new ModelVeiculo(array("id_veiculo" => $id_veiculo)));
        if ($veiculo) {
            $this->view->setVariable("id_veiculo", $veiculo[0]->get("id_veiculo"));
            $this->view->setVariable("titulo", $veiculo[0]->get("titulo"));
            $this->view->setVariable("ano_fab", $veiculo[0]->get("ano_fabricacao"));
            $this->view->setVariable("ano_mod", $veiculo[0]->get("ano_modelo"));
            $this->view->setVariable("numero_passageiros", $veiculo[0]->get("numero_passageiros"));
            $this->view->setVariable("cor", $veiculo[0]->get("cor"));
            $this->view->setVariable("destaque", $veiculo[0]->get("destaque"));
            $this->view->setVariable("combustivel", $veiculo[0]->get("combustivel"));
            $this->view->setVariable("tipo", $veiculo[0]->get("tipo"));
            $this->view->setVariable("fabricante", $veiculo[0]->get("fabricante"));
            $this->view->setVariable("modelo", $veiculo[0]->get("modelo"));

            return $veiculo;
        }
    }

}

?>