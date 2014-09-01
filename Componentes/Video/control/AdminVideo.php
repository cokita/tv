<?php
class AdminVideo extends Control {
    protected $js  = array("video");
    protected $css = array("video.css");
    private $group;


    public function __construct($params, $interface = "InterfaceVideo.html")
    {
        $this->usuario = new ModelUsuario_Administrador();
        parent::__construct($params, $interface);
    }

    public function index()
    {
        $this->view->setVariable("id_usuario_logado", ControleSessao::$id_usuario);

        $this->view->touchBlock("Conteudo");

        $this->listarVideos();

        return $this->view;
    }

    private function listarVideos(){
        $videoDAO = new Video_YoutubeDAO();
        $videoModel = new ModelVideo_Youtube();
        $videoModel->set("ativo", 1);
        $videos = $videoDAO->consultar($videoModel);
        if($videos) {
            $this->view->touchBlock("Conteudo_Videos");
            $this->view->setCurrentBlock("Videos");
            foreach ($videos as $youtube) {
                $this->view->setVariable("id_youtube", $youtube->get("id_youtube"));
                $this->view->setVariable("titulo", $youtube->get("titulo"));
                $this->view->setVariable("descricao", $youtube->get("descricao"));
                $this->view->setVariable("caminho_thumb", $youtube->get("thumbnail"));
                $this->view->setVariable("url", $youtube->get("url"));
                $this->view->setVariable("ativo", $youtube->get("ativo") == 1 ? "sim" : "não");
                $this->view->parseCurrentblock();
            }
        }else {
            $this->view->touchBlock("nenhumVideo");
        }
    }


    public function salvarVideo()
    {
        $erro = 0;
        $msg = "";
        $url = $this->params['youtube_url'];
        if(isset($url) && $url != ""){
            $urlCode = $this->getYoutubeCode($url);
            if($urlCode){
                $youtubeInfo = $this->getFullInfoYoutube($urlCode);
                $youtubeModel = new ModelVideo_Youtube();
                $youtubeModel->set("titulo", $youtubeInfo['title']);
                $youtubeModel->set("descricao", $youtubeInfo['description']);
                $youtubeModel->set("thumbnail", $youtubeInfo['thumbnail']);
                $youtubeModel->set("duracao", $youtubeInfo['duration']);
                $youtubeModel->set("url", $url);
                $youtubeModel->set("id_usuario", ControleSessao::$id_usuario);
                $youtubeDAO = new Video_YoutubeDAO();
                $youtubeDAO->incluir($youtubeModel);
                $msg = "Video inserido com sucesso";
            }else{
                $erro++;
                $msg = "A url informada não é uma url válida do Youtube.";
            }
        }else{
            $erro++;
            $msg = "Por favor, informe uma url do Youtube.";
        }

        return json_encode(array('error'=> $erro, 'msg' => $msg));
    }


    public function inativar()
    {
        $ids[] = $this->params['idsVideos'];
        if(strpos($this->params['idsVideos'], ",") !== false){
            $ids = explode(",", $this->params['idsVideos']);
        }

        if(is_array($ids) && count($ids) > 0){
            $videoDAO = new Video_YoutubeDAO();
            foreach($ids as $id){
                $modelVideo = new ModelVideo_Youtube();
                $modelVideo->set("id_youtube", $id);
                $modelVideo->set("ativo", 0);

                $videoDAO->alterar($modelVideo);
            }
        }
    }

    public function getYoutubeCode ($string)
    {
        if (preg_match('!(?<=v=)[a-zA-Z0-9_-]+(?=&)|(?<=v\/)[^&\?\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\?\n]+!', $string, $matches)) {
            return $matches[0];
        }
        return NULL;
    }

    public function getFullInfoYoutube($id)
    {
        // id do youtube3.
        $video_id = "$id";

        //Using cURL php extension to make the request to youtube API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, YT_API_URL . $video_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //$feed holds a rss feed xml returned by youtube API
        $feed = curl_exec($ch);
        curl_close($ch);

        //Using SimpleXML to parse youtube"s feed
        $xml = simplexml_load_string($feed);
        $entry = $xml->entry[0];
        $media = $entry->children("media", true);
        $this->group = $media[0];

        $content_attributes = $this->group->content->attributes();
        $vid_duration = $content_attributes["duration"];
        //formata a duração do video em mm:ss
        $duration_formatted = str_pad(floor($vid_duration/60), 2, "0", STR_PAD_LEFT) . ":" . str_pad($vid_duration%60, 2, "0", STR_PAD_LEFT);


        $youtube = array(
            "title"=>(string)$this->group->title,
            "description"=>(string)$this->group->description,
            "tags"=>(string)$this->group->keywords,
            "thumbnail"=>(string)$this->group->thumbnail[0]->attributes(),
            "duration"=>(string)$duration_formatted
        );

        return $youtube;

    }


}

