<?php

class Componente {
    /*
     * Contém os roteiros JavaSript e folhas de estilo previamente
     * incluídos. Útil para evitar a duplicação de requisições.
     */

    private static $js_inclusos = array();
    private static $css_inclusos = array();

    static function inclusaoDependencias($params, HTML_Template_IT $view, Control $componente) {
        $nomeComponente = null;
        //$classe = substr(get_class($componente), 8);
        $classe = get_class($componente);
        if (preg_match('/^Controle/', $classe)) {
            $classe = substr(get_class($componente), 8);
        } else if (preg_match('/^Admin/', $classe)) {
            $classe = substr(get_class($componente), 5);
        }
        if (strpos($classe, "_")) {
            $nomeComponente = substr($classe, 0, strpos($classe, "_"));
        }
        if (in_array("JavaScript", array_keys($view->blocklist))) {
            $view->setCurrentBlock("JavaScript");
            $dependenciasJS = $componente->dependenciasJS();
            if ($dependenciasJS) {
                foreach ($dependenciasJS as $javascript) {
                    /*
                     * Verificamos se existe um JavaScript associado ao
                     * componente e se este ainda não foi incluído.
                     */
                    if ($javascript && (!in_array($javascript, self::$js_inclusos))) {
                        self::$js_inclusos[] = $javascript;

                        /*
                         * Caso a classe esteja no novo formato, verificamos
                         * a possibilidade do roteiro JavaScript residir na
                         * pasta apropriada, dentro da estrutura do componente.
                         */
                        if ((strpos($javascript, "http://") !== false)) {
                            $javascript = $javascript;
                        } else if ((!(strpos($classe, "_") === true) &&
                                file_exists("Componentes/" . $nomeComponente . "/view/js/" . $javascript . ".js")
                                || file_exists("Componentes/" . $classe . "/view/js/" . $javascript . ".js")
                                || file_exists("Componentes/" . ucfirst($javascript) . "/view/js/" . $javascript . ".js")
                                || file_exists("js/" . $javascript . ".js"))) {
                            $javascript = URL . "js.php?src=" . $javascript;
                        } else if (strpbrk("_", $javascript)) {
                            $pfx = explode("_", $javascript);
                            $prefixo = ucfirst($pfx[0]);
                            if (file_exists("Componentes/" . $prefixo . "/view/js/" . $javascript . ".js")) {
                                $javascript = URL . "js.php?src=" . $javascript;
                            } else {
                                $javascript = "js/" . $javascript;
                            }
                        } else {
                            $javascript = "js/" . $javascript;
                        }

                        $view->setVariable("javascript", $javascript);

                        $view->parseCurrentBlock();
                    }
                }
            }
        }
        if (in_array("CSS", array_keys($view->blocklist))) {
            $view->setCurrentBlock("CSS");
            $dependenciasCSS = $componente->dependenciasCSS();

            if (!$dependenciasCSS) {
                //$dependenciasCSS = $menu[$classe]["css"];
            }
            if ($dependenciasCSS) {
                foreach ($dependenciasCSS as $css) {
                    /*
                     * Verificamos se existe uma folha de estilo associada ao
                     * componente e se esta ainda não foi incluído.
                     */
                    if ($css && !in_array($css, self::$css_inclusos)) {

                        if (file_exists("css/" . $css)) {
                            $arquivo = "css/" . $css;
                        } else if (file_exists("Componentes/" . $classe . "/view/css/" . $css)) {
                            $arquivo = "Componentes/" . $classe . "/view/css/" . $css;
                        } else if (file_exists("Componentes/" . $nomeComponente . "/view/css/" . $css)) {
                            $arquivo = "Componentes/" . $nomeComponente . "/view/css/" . $css;
                        } else if (file_exists($css)) {
                            $arquivo = $css;
                        }
                        $view->setVariable("css", $arquivo);
                        $view->parseCurrentBlock();

                        self::$css_inclusos[] = $css;
                    }
                }
            }
        }
    }

    function inclusaoComponentes($params, HTML_Template_IT $view, HTML_Template_IT $viewPrincipal = null) {
        $blocos = array_keys($view->blocklist);

        /*
         * Procuramos por componentes na página gerada pelo componente principal
         * para que a injeção de dependências (JS e CSS) também ocorra.
         */
        if ($blocos) {
            foreach ($blocos as $bloco) {
                if (preg_match_all("/componente_(.*)/", $bloco, $componentes)) {
                    $classe = substr($bloco, 11);
                    //VER UMA FORMA MAIS INTELIGENTE DE TRATAR O CODIGO ABAIXO--> COMO INCLUIR COMPONENTES DE CLASSES ADMIN
                    if(class_exists("Controle" . $classe)){
                        $controle = "Controle" . $classe;
                    }else if(class_exists("Admin" . $classe)){
                        $controle = "Admin" . $classe;
                    }

                    if (class_exists($controle)) {
                        /*
                         * Apenas o componente ControleMenuPerfil utiliza o
                         * terceiro parâmetro. Para os demais, ele é
                         * silenciosamente ignorado.
                         */
                        $componente = new $controle($params);
                        $conteudoComponente = $componente->index();

                        /*
                         * Inserimos as dependências (JS e CSS) dos módulos secundários.
                         */
                        if ($viewPrincipal) {
                            Componente::inclusaoDependencias($params, $viewPrincipal, $componente);
                        } else {
                            Componente::inclusaoDependencias($params, $view, $componente);
                        }

                        if ($conteudoComponente instanceof HTML_Template_IT) {
                            self::inclusaoComponentes($params, $conteudoComponente, $viewPrincipal);
                        }

                        if ($conteudoComponente) {
                            $view->setCurrentBlock($bloco);
                            $view->setVariable("conteudo", $conteudoComponente->get());
                            $view->parseCurrentBlock();
                        }
                    }
                }
            }
        }
    }

}

?>