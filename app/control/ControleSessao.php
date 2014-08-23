<?php
/**
 * Classe responsável pelo controle da sessão do usuário e usada como objeto
 * de baixo acoplamento entre as classes de controle e modelos.
 */
class ControleSessao {
    public static $email;
    public static $control;
    public static $action;
    public static $id_usuario;
    public static $perfil;
    public static $params;
    public static $id;

    static function obterDadosSessao() {
        if(NOME_PASTA){
            $nome = substr($_SERVER["REQUEST_URI"], strlen("/".NOME_PASTA."/"));
            $caminho = explode("/", $nome);
            self::$control = $caminho[0];
            self::$id = $caminho[1];
        }else{
            $caminho = explode("/", $_SERVER["REQUEST_URI"]);
            if(isset($caminho[1])){
                self::$control = $caminho[1];
                if(isset($caminho[2])){
                    self::$action = $caminho[2];
                }else{
                    self::$action = 'index';

                }
                if(isset($caminho[3])){
                    self::$id = $caminho[3];
                }else{
                    self::$id = null;

                }
            }
        }

        if($_POST){
            self::$params = $_POST;
        } else if (isset($_SERVER["REDIRECT_QUERY_STRING"]) && (strpos("&", $_SERVER["REDIRECT_QUERY_STRING"]) === true)) {
            $parametros = explode("&", $_SERVER["REDIRECT_QUERY_STRING"]);
            foreach($parametros as $parametro) {
                $parametro = explode("=", $parametro);
                self::$params[$parametro[0]] = $parametro[1];
            }
        } else if (isset($_SERVER["QUERY_STRING"]) && (strripos($_SERVER["QUERY_STRING"], '&') !== false)){
            $parametros = explode("&", $_SERVER["QUERY_STRING"]);

            if($parametros){
            foreach ($parametros as $parametro) {
                    if(strripos($parametro, '=') !== false){
                        $parametro = explode("=", $parametro);
                        self::$params[$parametro[0]] = $parametro[1];
                    }
                }
            }

        } else{
            $parametros = explode("/", $_SERVER["QUERY_STRING"]);
            self::$action    = isset($parametros[1]) ?: null;
            self::$id    = isset($parametros[2]) ?: null;;
        }

        if(isset($_REQUEST["control"])){
            ControleSessao::$control = $_REQUEST["control"];
        }
        if(isset($_REQUEST["action"])){
            ControleSessao::$action = $_REQUEST["action"];
        }

//        if($_GET){
//            self::$params = $_GET;
//        }
        if(isset($_SESSION["email"])){
            self::$email = $_SESSION["email"];
        }
        if(isset($_SESSION["id_usuario"])){
            self::$id_usuario = $_SESSION["id_usuario"];
        }

    }

    public static function apresentarConteudo($view, $mostrarConteudo = true) {
        /**
         * @todo Corrigir a injeção (provisoria) do caminho da hospedagem do portal.
         * Dirty and ugly method.
         */
        if ($view instanceof HTML_Template_IT) {

            $conteudo = $view->get();
        } else {
            $conteudo = $view;
        }

        $conteudo = preg_replace('/((href|src)=[\'"])((mailto|ftp|ftps|http|https))/', "$1_$3_", $conteudo);
        $conteudo = preg_replace('/((href|src)=[\'"])([[:alnum:]])/', "$1" . URL . "$3",         $conteudo);
        $conteudo = preg_replace('/((href|src)=[\'"])_((mailto|ftp|ftps|http|https))_/', "$1$3", $conteudo);
        if ((! isset($_REQUEST["_debug_"])) && HTML_COMPACTO) {
            $conteudo = preg_replace("/>  *</", "><", preg_replace("/\r|\n|  */", " ", $conteudo));
        }

        if ($mostrarConteudo) {

            echo $conteudo;
        }
        return $conteudo;
    }
}
?>
