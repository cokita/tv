<?php

class ControleImagem extends Control {

    public function __construct($params, $interface = "", $objeto = null) {
        parent::__construct($params, $interface, $objeto);
    }

    public function index() {
        return $this->view;
    }

    public function inserirImagem() {
        $qtdRetirar = 1;
        if(NOME_PASTA != ""){
            $qtdRetirar = strlen(NOME_PASTA) + 2;
        }
        //_debug(substr(substr($this->params["caminho"], 0, -(strlen($this->params["nomeAntigo"]))), $qtdRetirar));die();
        $caminho = substr(substr($this->params["caminho"], 0, -(strlen($this->params["nomeAntigo"]))), $qtdRetirar);
        $id_veiculo = $this->params["id_veiculo"];
        $imagem = ImagemDAO::incluir(new ModelImagem(array("nome" => strtolower($this->params["novoNome"]), "caminho" => $caminho)));
        if ($imagem) {
            Imagem_VeiculoDAO::incluir(new ModelImagem_Veiculo(array("id_veiculo" => $id_veiculo, "id_imagem" => $imagem->get("id_imagem"))));
            return true;
        } else {
            return false;
        }
    }

    public function excluirImagem() {
        $sucesso = false;
        $id_imagem = $this->params["id_imagem"];
        if ($id_imagem) {
            $modelImagem = new ModelImagem(array("id_imagem" => $id_imagem));
            $imagem = ImagemDAO::consultar($modelImagem);
            if ($imagem) {
                if (file_exists(PASTA_UPLOAD . "/" . $imagem[0]->get("nome"))) {
                    unlink(PASTA_UPLOAD . "/" . $imagem[0]->get("nome"));
                }
                if (file_exists(PASTA_UPLOAD . "/thumbs/" . $imagem[0]->get("nome"))) {
                    unlink(PASTA_UPLOAD . "/thumbs/" . $imagem[0]->get("nome"));
                }
            }
            Imagem_VeiculoDAO::removerPorImagem(new ModelImagem_Veiculo(array("id_imagem" => $id_imagem)));
            ImagemDAO::remover($modelImagem);
            $sucesso = true;
        }
        return $sucesso;
    }

    public function excluirImagens() {
        $id_veiculo = $this->params["id_veiculo"];
        if ($id_veiculo) {
            $modelImagemVeiculo = new ModelImagem_Veiculo(array("id_veiculo" => $id_veiculo));
            $imagens = Imagem_VeiculoDAO::consultarPorVeiculo($modelImagemVeiculo);
            if ($imagens) {
                foreach ($imagens as $imagem) {
                    $id_imagem = $imagem->get("id_imagem");
                    if (file_exists(PASTA_UPLOAD . "/" . $imagem->get("nome"))) {
                        unlink(PASTA_UPLOAD . "/" . $imagem->get("nome"));
                    }
                    if (file_exists(PASTA_UPLOAD . "/thumbs/" . $imagem->get("nome"))) {
                        unlink(PASTA_UPLOAD . "/thumbs/" . $imagem->get("nome"));
                    }
                    Imagem_VeiculoDAO::removerPorImagem(new ModelImagem_Veiculo(array("id_imagem" => $id_imagem)));
                    ImagemDAO::remover(new ModelImagem(array("id_imagem" => $id_imagem)));
                }
            }
        }
        return true;
    }

}

?>
