<?php
if (! require_once("app/config/config.php")) {
    echo "Falha ao incluir o arquivo de configuração 'app/config/config.php'.";
} else {
    ControleSessao::obterDadosSessao();
    $interface = "admin.html";
    if (Conexao::autenticarAdmin() == true) {
        if(!ControleSessao::$control || ControleSessao::$control == "admin.php"){
            $controle = "AdminHome";
        }else{
            $controle = "Admin". ControleSessao::$control;
        }
        if(class_exists($controle)) {
            $componentePrincipal = new $controle(ControleSessao::$params);
            $view                = Control::carregarInterface($interface);
            $conteudoComponente  = $componentePrincipal->index();
            $params              = ControleSessao::$params;
            Componente::inclusaoDependencias($params, $view, $componentePrincipal);
            Componente::inclusaoComponentes($params, $view);
            Componente::inclusaoComponentes($params, $conteudoComponente, $view);
            $view->setVariable("aplicacao", $conteudoComponente->get());
            $conteudo = $conteudoComponente->get();
            verificarControleMenu($controle, $view);
            $view->touchBlock("Logout");
            $view->setVariable("usuario_logado", $_SESSION["nome"]);
            $view->setVariable("url", URL);
            $view->setVariable("aplicacao", $conteudo);
        }else{
            echo "Classe Inexistente";
        }
    }else {
        //$interface = "login.html";
        $componentePrincipal = new ControleLogin(null);
        $view               = Control::carregarInterface($interface);
        $conteudoComponente = $componentePrincipal->index();
        Componente::inclusaoDependencias(null, $view, $componentePrincipal);
        $conteudo = $conteudoComponente->get();
        $view->touchBlock("Login");
        $view->setVariable("aplicacao_login", $conteudo);
    }
    ControleSessao::apresentarConteudo($view);
}


function verificarControleMenu($control, $view){
    $controle = substr($control, 5);
    $view->setVariable("active_home", "");
    $view->setVariable("active_estoque", "");
    $view->setVariable("active_credito", "");
    $view->setVariable("active_parceiros", "");
    $view->setVariable("active_cliente", "");
    switch ($controle){
        case "Home":
            $view->setVariable("active_home", "ativo");
            break;
        case "Usuario_Administrador":
            $view->setVariable("active_admin", "ativo");
            break;
        case "Usuario":
            $view->setVariable("active_cadastro", "ativo");
            break;
        case "Usuario_Visualizar":
            $view->setVariable("active_visualizar", "ativo");
            break;
    }
    

}
?>