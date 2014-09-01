<?php
class AdminImagem extends Control {
    protected $js  = array("imagem");
    protected $css = array("imagem.css");
    protected $nome_imagem;
    protected $extensao_imagem;
    protected $tamanho_imagem;
    protected $nome_imagem_tmp;
    protected $pasta;
    protected $pasta_thumb;


    public function __construct($params, $interface = "InterfaceImagem.html")
    {
        $this->usuario = new ModelUsuario_Administrador();
        $this->pasta = "upload_files/";
        $this->pasta_thumb = "upload_files/thumb/";
        parent::__construct($params, $interface);
    }

    public function index()
    {
        //session_destroy();
        $this->view->setVariable("id_usuario_logado", ControleSessao::$id_usuario);
        $this->view->setVariable("tamanho_file", ini_get('upload_max_filesize'));
        $this->view->setVariable("tamanho_post", ini_get('post_max_size'));
        $this->view->setVariable("arquivos_permitidos", "gif, jpeg, jpg, png");
        $this->view->setVariable("resolucao_min", "1280x720");

        $imagens = $this->listarImagens();


        $this->view->touchBlock("Conteudo");



        return $this->view;
    }

    public  function listarImagens(){
        $imagemDAO = new ImagemDAO();
        $imgModel = new ModelImagem();
        $imgModel->set("ativo", 1);
        $imagens = $imagemDAO->consultar($imgModel);
        if($imagens) {
            $this->view->touchBlock("Conteudo_Imagens");
            $this->view->setCurrentBlock("Imagens");
            foreach ($imagens as $imagem) {
                $this->view->setVariable("id_imagem", $imagem->get("id_imagem"));
                $this->view->setVariable("nome", $imagem->get("nome"));
                $this->view->setVariable("caminho", $imagem->get("caminho"));
                $this->view->setVariable("caminho_thumb", $imagem->get("caminho_thumb"));
                $this->view->setVariable("ativo", $imagem->get("ativo") == 1 ? "sim" : "não");
                $this->view->setVariable("id_usuario", ControleSessao::$id_usuario);
                $this->view->parseCurrentblock();
            }
        }else {
            $this->view->touchBlock("nenhumaImagem");
        }


        return $imagens;
    }

    public function fazerUpload()
    {
        $erro = 0;
        $msg = "";
        //VEr arquivo instrucoes na raiz para adicionar outros.
        $arrRetornoImageTypeValid = array(1, 2, 3);
        if(isset($_FILES['foto']) && isset($_FILES['foto']['name'])){
            $this->nome_imagem = $_FILES['foto']['name'];
            $this->tamanho_imagem = $_FILES['foto']['size'];
            $this->extensao_imagem = $this->getExtensao();
            $this->tipo_imagem = $_FILES['foto']['type'];
            $this->nome_imagem_tmp = $_FILES['foto']['tmp_name'];

            if(!$this->ehImagem($this->extensao_imagem)){
                $erro++;
                $msg = "O arquivo enviado não tem uma extensão válida, favor enviar um arquivo com as extensões: gif, jpeg, jpg ou png";
            }elseif(!in_array($this->tipo_imagem, $this->getMimeTypes())){
                $erro++;
                $msg = "O arquivo enviado, não é um arquivo do tipo imagem.";
            }elseif(!in_array(exif_imagetype($this->nome_imagem_tmp), $arrRetornoImageTypeValid )){
                $erro++;
                $msg = "Esse arquivo não é uma imagem, não adianta alterar a extensão.";
            }elseif($this->sizeFilter($this->tamanho_imagem) > substr(ini_get('upload_max_filesize'),0,-1)){
                $erro++;
                $msg = "O tamanho da imagem é maior que o máximo definido para upload nas suas configurações do PHP.";
            }elseif ($this->sizeFilter($this->tamanho_imagem) > substr(ini_get('post_max_size'),0,-1)){
                $erro++;
                $msg = "O tamanho da imagem é maior que o máximo definido para posts nas suas configurações do PHP.";
            }
        }else{
            $erro++;
            $msg = "Houve um problema no upload da imagem, tente novamente.";
        }

        if($erro > 0){
            return  json_encode(array('error' => 1, 'msg' => $msg));
        }else{
            return json_encode($this->salvar());
        }
    }

    private function getExtensao()
    {
        //retorna a extensao da imagem
        return $extensao = strtolower(end(explode('.', $this->nome_imagem)));
    }

    private function ehImagem($extensao)
    {
        $extensoes = array('gif', 'jpeg', 'jpg', 'png');
        // extensoes permitidas
        if (in_array($extensao, $extensoes))
            return true;
    }

    private function getMimeTypes()
    {
        $arrayMime = array(
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif'
        );

        return $arrayMime;

    }

    public function sizeFilter( $bytes )
    {
        $kbs = $bytes/1024;
        $mbs = $kbs/1024;
        return round($mbs, 2);
    }


    public function salvar()
    {
        $erro = 0;
        $msg = "";

        $novo_nome = time() . '.' . $this->extensao_imagem ;
        $destino = $this->pasta . $novo_nome;
        if (! move_uploaded_file($this->nome_imagem_tmp, $destino)){
            if ($this->arquivo['error'] == 1){
                $erro++;
                $msg = "Tamanho excede o permitido";
            }else{
                $erro++;
                $msg ="Erro " . $this->arquivo['error'];
            }
        }

        list($largura, $altura, $tipo, $atributo) = getimagesize($destino);

        if($largura < LARGURA && $altura < ALTURA){
            $erro++;
            $msg = "A imagem deve ter uma resolução mínima de 1280x720";
        }elseif(($largura < LARGURA || $altura < ALTURA) || ($largura > LARGURA || $altura > ALTURA)){
            $this->redimensionar($largura, $altura, $tipo, $destino);
        }

        if($erro > 0){
            unlink($destino);
            $retorno = array('erro' => 1, 'msg' => $msg);
        }else{
            $this->createThumb($destino);
            $retorno = $this->saveDB($novo_nome);
        }

        return $retorno;
    }


    private function saveDB($nome)
    {
        $retorno = array('erro' => 0, 'msg' => 'Arquivo salvo com sucesso.');

        $modelImagem = new ModelImagem();
        $imagemDao = new ImagemDAO();

        $modelImagem->set("nome", $this->nome_imagem);
        $modelImagem->set("caminho", $this->pasta.$nome);
        $modelImagem->set("caminho_thumb", $this->pasta_thumb.$nome);
        $modelImagem->set("extensao", $this->extensao_imagem);
        $modelImagem->set("id_usuario", ControleSessao::$id_usuario);

        $imagemDao->incluir($modelImagem);

        return $retorno;

    }

    private function createThumb($file){
        if(!file_exists($this->pasta_thumb)){
            mkdir($this->pasta_thumb, 0777);
        }

        $thumb = new easyphpthumbnail();
        $thumb->Thumbheight = 100;
        $thumb->Thumblocation = $this->pasta_thumb;
        $thumb->Createthumb($file, "file");
    }

    private function redimensionar($imgLarg, $imgAlt, $tipo, $img_localizacao){
        $novaLarg = LARGURA;
        $novaAlt = ALTURA;

        $novaimagem = imagecreatetruecolor($novaLarg, $novaAlt);
        switch ($tipo){
            case 1:
            // gif
                $origem = imagecreatefromgif($img_localizacao);
                imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $novaLarg, $novaAlt, $imgLarg, $imgAlt);
                imagegif($novaimagem, $img_localizacao);
                break;
            case 2:
            // jpg
                $origem = imagecreatefromjpeg($img_localizacao);
                imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $novaLarg, $novaAlt, $imgLarg, $imgAlt);
                imagejpeg($novaimagem, $img_localizacao);
                break;
            case 3:
            // png
                $origem = imagecreatefrompng($img_localizacao);
                imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $novaLarg, $novaAlt, $imgLarg, $imgAlt);
                imagepng($novaimagem, $img_localizacao);
                break;
        }
        //destroi as imagens criadas
        imagedestroy($novaimagem);
        imagedestroy($origem);
    }

    public function inativar()
    {
        $ids[] = $this->params['idsImagens'];
        if(strpos($this->params['idsImagens'], ",") !== false){
            $ids = explode(",", $this->params['idsImagens']);
        }

        if(is_array($ids) && count($ids) > 0){
            $imagemDAO = new ImagemDAO();
            foreach($ids as $id){
                $modelImagem = new ModelImagem();
                $modelImagem->set("id_imagem", $id);
                $modelImagem->set("ativo", 0);
                $imagemDAO->alterar($modelImagem);
            }
        }
    }
}

