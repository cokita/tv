<?php
if (! require_once("app/config/config.php")) {
    echo "Falha ao incluir o arquivo de configuração 'app/config/config.php'.";
} else {
    ControleSessao::obterDadosSessao();
    $interface = "index.html";
        if(!ControleSessao::$control){
            $controle = "ControleHome";
        }else{
            $controle = "Controle". ControleSessao::$control;
        }
        if(class_exists($controle)) {
            $componentePrincipal = new $controle(ControleSessao::$params);
            $view               = Control::carregarInterface($interface);
            $conteudoComponente = $componentePrincipal->index();
            Componente::inclusaoDependencias(ControleSessao::$params, $view, $componentePrincipal);
            Componente::inclusaoComponentes(ControleSessao::$params, $view);
            Componente::inclusaoComponentes(ControleSessao::$params, $conteudoComponente, $view);
            $view->setVariable("aplicacao", $conteudoComponente->get());
            $conteudo = $conteudoComponente->get();
            $view->setVariable("aplicacao", $conteudo);
            $view->setVariable("site", URL);
        }else{
            echo "Classe Inexistente";
        }
    ControleSessao::apresentarConteudo($view);
}
?>