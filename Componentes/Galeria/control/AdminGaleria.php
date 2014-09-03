<?php
class AdminGaleria extends Control {
    protected $js  = array("galeria");
    protected $css = array("galeria.css");
    private $group;


    public function __construct($params, $interface = "InterfaceGaleria.html")
    {
        $this->usuario = new ModelUsuario_Administrador();
        parent::__construct($params, $interface);
    }

    public function index()
    {
        $this->view->setVariable("id_usuario_logado", ControleSessao::$id_usuario);

        $this->view->touchBlock("Conteudo");


        $this->listarItensAvulsos();
        $this->listarItensNaGaleria();
        return $this->view;
    }


    private function listarItensNaGaleria(){
        $arrImagens = $this->listarImagens('consultarImagensNaGaleria');
        $arrVideos = $this->listarVideos('consultarVideosNaGaleria');

        $itemsOrdenados = $this->ordenarItens($arrImagens, $arrVideos);

        if($itemsOrdenados) {
            $this->view->touchBlock("Conteudo_Itens_Galeria");
            $this->view->setCurrentBlock("Itens_Galeria");
            foreach ($itemsOrdenados as $item) {
                $this->view->setVariable("id_imagem", $item['id']);
                $this->view->setVariable("nome", $item['nome']);
                $this->view->setVariable("caminho", $item['caminho']);
                $this->view->setVariable("caminho_thumb", $item['thumb']);
                $this->view->setVariable("ident", $item['ident']);
                $this->view->parseCurrentblock();
            }
        }else {
            $this->view->touchBlock("nenhumaItem");
        }
    }

    private function listarItensAvulsos(){
       // $arrImagens = $this->listarImagens('consultarImagensSemGaleria');
        $arrVideos = $this->listarVideos('consultarVideosSemGaleria');

        //$itemsAvulsosOrdenados = $this->ordenarItens($arrImagens, $arrVideos);

        if($itemsAvulsosOrdenados) {
            $this->view->touchBlock("Conteudo_Itens");
            $this->view->setCurrentBlock("Itens");
            foreach ($itemsAvulsosOrdenados as $item) {
                $this->view->setVariable("id_imagem", $item['id']);
                $this->view->setVariable("nome", $item['nome']);
                $this->view->setVariable("caminho", $item['caminho']);
                $this->view->setVariable("caminho_thumb", $item['thumb']);
                $this->view->setVariable("ident", $item['ident']);
                $this->view->parseCurrentblock();
            }
        }else {
            $this->view->touchBlock("nenhumaItem");
        }
    }

    public function salvar()
    {
        $sucesso = true;
        $cods = $this->params['ids'];
        if($cods){
            $galeriaItemDao = new Galeria_ItemDAO();
            $galeriaItemModel = new ModelGaleria_Item();
            $galeriaItemModel->set('id_galeria', 1);
            $jaTemGaleria = $galeriaItemDao->consultar($galeriaItemModel);

            if($jaTemGaleria){
                $galeriaItemDao->removerTodosPorGaleria($galeriaItemModel);
            }

            $ordem = 1;
            foreach($cods as $cod){
                $explode = explode('_', $cod);
                $identificador = $explode[0];
                $id = $explode[1];
                $modelGaleriaItem = new ModelGaleria_Item();
                if($identificador == 'v' || $identificador == 'i'){
                    switch ($identificador){
                        case 'i':
                            $modelGaleriaItem->set('id_imagem', $id);
                            break;
                        case 'v':
                            $modelGaleriaItem->set('id_youtube', $id);
                            break;
                    }
                    $modelGaleriaItem->set('ordem', $ordem);
                    $modelGaleriaItem->set('id_galeria', 1);
                    $modelGaleriaItem->set('id_usuario', ControleSessao::$id_usuario);
                    $ordem++;
                    $galeriaItemDao->incluir($modelGaleriaItem);
                }else{
                    $sucesso = false;
                }
            }

        }

        return $sucesso;
    }

    private function ordenarItens($imgs, $videos){
        $arrOrdenado = array();
        if(!$videos){
            $arrOrdenado = $imgs;
        }elseif(!$imgs){
            $arrOrdenado = $videos;
        }else{
            $arrCombinado = $imgs + $videos;
            ksort($arrCombinado);
            $arrDesc = array_reverse($arrCombinado);
            $arrOrdenado = $arrDesc;

        }

        return $arrOrdenado;
    }

    private function listarImagens($metodo)
    {
        $arrImgs = array();
        $imagemDAO = new ImagemDAO();
        $imgModel = new ModelImagem();
        $imgModel->set("ativo", 1);
        $imagens = $imagemDAO->$metodo($imgModel);

        if($imagens){
            foreach($imagens as $img){
                $arrImgs[$img->get('ordem')] = array(
                    'id' => $img->get('id_imagem'),
                    'nome' =>  $img->get('nome'),
                    'thumb' => $img->get('caminho_thumb'),
                    'caminho' => $img->get('caminho'),
                    'data' => $img->get('data_inclusao'),
                    'id_usuario' => $img->get('id_usuario'),
                    'ordem'      => $img->get('ordem'),
                    'ident'      => 'i'
                );
            }
        }
        return $arrImgs;
    }

    private function listarVideos($metodo){
        $arrVideos = array();
        $videoDAO = new Video_YoutubeDAO();
        $videoModel = new ModelVideo_Youtube();
        $videoModel->set("ativo", 1);
        $videos = $videoDAO->$metodo($videoModel);

        if($videos){
            foreach($videos as $video){
                $arrVideos[$video->get('ordem')] = array(
                    'id' => $video->get('id_youtube'),
                    'nome' => $video->get('titulo'),
                    'thumb' => $video->get('thumbnail'),
                    'caminho' => $video->get('url'),
                    'data' => $video->get('data_inclusao'),
                    'id_usuario' => $video->get('id_usuario'),
                    'ordem'      => $video->get('ordem'),
                    'ident'      => 'v'
                );
            }
        }

        return $arrVideos;
    }


}

