<?php
if (require_once("app/config/config.php")) {
    ControleSessao::obterDadosSessao();

    if (isset($_REQUEST["admin"])) {
        $classeControle = "Admin" . ControleSessao::$control;
    } else {
        $classeControle = "Controle" . ControleSessao::$control;
    }
    $metodo = ControleSessao::$action;
    if (chamadaValida($classeControle, $metodo)) {

        $control = new $classeControle(ControleSessao::$params);
        $result  = $control->$metodo();

        switch (ControleSessao::$params["returnType"]) {
            case "XML":
                // @todo Todo XML deveria ser, por padrão, codificado em UTF-8.
                header("Content-type: text/xml; charset=ISO-8859-1");

                $resultXML = "<result>";

                foreach($result as $attrName => $item) {

                    if(is_array($item)) {
                        $resultXML .= "<".$attrName.">";

                        foreach($item as $obj) {
                            $resultXML .= "<item>";
                            $attrs = $obj->get_object_vars();

                            foreach($attrs as $attr=>$value) {
                                $resultXML .= "<".$attr.">".htmlspecialchars($value)."</".$attr.">";
                            }

                            $resultXML .= "</item>";
                        }

                        $resultXML .= "</".$attrName.">";
                    } else {
                        $resultXML .= "<item>";
                        $attrs = $item->get_object_vars();

                        foreach($attrs as $attr=>$value) {
                            $resultXML .= "<".$attr.">".htmlspecialchars($value)."</".$attr.">";
                        }

                        $resultXML .= "</item>";
                    }
                }

                $resultXML .= "</result>";

                $result = $resultXML;

                break;
            case "json":
            case "JSON":
                /*
                 * Convertemos um objeto em um vetor genérico.
                 */
                $vetor = Array();
                if (is_object($result)) {
                    foreach ($result->get_object_vars() as $chave => $valor) {
                        $vetor[$chave] = utf8_encode($valor);
                    }

                    $result = $vetor;
                } else if (is_array($result)) {
                    foreach ($result as $objeto) {
                        foreach ($objeto->get_object_vars() as $chave => $valor) {
                            $registro[$chave] = utf8_encode($valor);
                        }

                        $vetor[] = $registro;
                    }

                    $result = $vetor;
                } else if ($result instanceof HTML_Template_IT) {
                    $result = Array("conteudo" => $result->get(),
                                    "sucesso"  => true);
                }

                $result = json_encode($result);

                break;
            case "autocomplete":
                $items        = array();
                $autocomplete = "";

                // @todo Definir dinamicamente quem vai ser o valor e quem vai ser a chave!
                foreach($result as $obj) {
                    $items[utf8_encode($obj->get($_REQUEST["attr1"]))] = $obj->get($_REQUEST["attr2"]);
                }

                foreach ($items as $key=>$value) {
                    $autocomplete .= "$key|$value\n";
                }

                $result = $autocomplete;

                break;
            case "tpl":
                if ($result instanceof HTML_Template_IT) {
                    $result = ControleSessao::apresentarConteudo($result, false);
                }
                break;
            case "bin":
                header("Content-Length: " . strlen($result));
                header("Content-Transfer-Encoding: binary");

                if ($_REQUEST["tipo"]) {
                    header("Content-Type: " . $_REQUEST["tipo"]);
                }

                break;
            default: // Plain text
                 $result = $result;
        }

        echo $result;
    }
} else {
    echo "Falha ao incluir o arquivo de configuração 'app/config/config.php'.";
}

function chamadaValida($classe, $metodo) {

    $sucesso = false;

    if (ControleSessao::$control && ControleSessao::$action) {
        if (class_exists($classe)) {
            $objeto  = new $classe(null);

            if (method_exists($objeto, $metodo)) {
                $sucesso         = true;
            } else {
                echo "Método inexistente: $metodo";
            }
        } else {
            echo "Classe inexistente: $classe";
        }
    } else {
        echo "O controle ou o método não foram informados!";
    }

    return $sucesso;
}
?>