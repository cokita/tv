<?php

class ControleImagem_Galeria extends Control {

    protected $veiculo;

    public function __construct($params=null, $interface = "", $veiculo=null) {
        if ($veiculo) {
            $this->veiculo = $veiculo;
        } else {
            $this->veiculo = new ModelVeiculo();
            if (ControleSessao::$id){
                $this->veiculo->set("id_veiculo", ControleSessao::$id);
            }else{
                $this->veiculo->set("id_veiculo", $params["id"]);
            }
        }
        $this->retornarXmlGaleria();
        parent::__construct($params, $interface, $objeto);
    }

    public function index() {
        return $this->view;
    }

    public function retornarXmlGaleria() {
        $imagens = ImagemDAO::consultarImagensPorVeiculo(new ModelImagem_Veiculo(array("id_veiculo" => $this->veiculo->get("id_veiculo"))));
        if ($imagens) {
            $rss = "<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"?>
                <rss version=\"2.0\" xmlns:media=\"http://search.yahoo.com/mrss/\" xmlns:atom=\"http://www.w3.org/2005/Atom\">";
            foreach ($imagens as $imagem) {
                $thumb = SITE . "thumbs" . $imagem->get("caminho") . $imagem->get("imagem");
                $image = SITE . $imagem->get("caminho") . $imagem->get("imagem");
                $rss .= "<channel>
                      <title>Feed title</title>
                      <description>Feed Description</description>
                      <link></link>
                      <item>
                           <title>Picture A</title>
                           <media:description> This one's my favorite.</media:description>
                           <link>pl_images/A.jpg</link>
                           <media:thumbnail url=\"$thumb\"/>
                           <media:content url=\"$image\"/>
                      </item>
                  </channel>";
            }
            $rss .= "</rss>";
            //header('Content-type: text/xml');
            return $rss;
        } 
    }

}

?>
