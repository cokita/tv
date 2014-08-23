<?php

if (require_once("app/config/config.php")) {
    ControleSessao::obterDadosSessao();

    /*
     * @todo Garantir que o arquivo seja carregado apenas da pasta
     *       js, que seja sempre um arquivo com a extensão .js
     */

    $javascript = $_REQUEST["src"];
    if (strpos($javascript, "_") && substr($javascript, -3) != ".js") {
        $arquivo = "Componentes/" . ucfirst(substr($javascript, 0, strpos($javascript, "_"))) . "/view/js/" . $javascript . ".js";
    } else {
        $componente = ucfirst($javascript);
        if (file_exists("Componentes/$componente/view/js/$javascript.js")) {
            $arquivo = "Componentes/$componente/view/js/$javascript.js";
        } else {
            $arquivo = "js/" . $javascript . ".js";
        }
    }

    if (file_exists($arquivo)) {
        echo file_get_contents($arquivo);
    }
} else {
    echo "Falha ao incluir o arquivo de configuração 'app/config/config.php'.";
}
?>