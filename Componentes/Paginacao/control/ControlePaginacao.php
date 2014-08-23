<?php
class ControlePaginacao {

    public static function paginar($objetos, $criterios = array(), $ajax=false, $classForm = null) {
        $view = Control::carregarInterface("InterfacePaginacao.html");
        
        if ($objetos) {
            if ($objetos[0]->get("pg_numero_registros") > $objetos[0]->get("pg_itens_pagina")) {
                $paginacao          = new stdClass();
                $paginacao->paginas = ceil($objetos[0]->get("pg_numero_registros")/$objetos[0]->get("pg_itens_pagina"));
                $paginacao->paginas++;

                if ((!isset($_REQUEST['pg_pagina'])) or ($_REQUEST['pg_pagina'] == 0)) {
                    $paginacao->pagina = 0;
                } else if ($_REQUEST['pg_pagina'] > $paginacao->paginas) {
                    $paginacao->pagina = $paginacao->paginas-2;
                    $paginacao->pagina = $paginacao->pagina;
                } else {
                    $paginacao->pagina = $_REQUEST['pg_pagina'];
                    $paginacao->pagina--;
                }

                if ($paginacao->pagina > 0) {
                    $menos               = $paginacao->pagina;
                    $paginacao->primeira = 1;
                    $paginacao->anterior = $menos;
                }

                if ($paginacao->pagina < $paginacao->paginas - 2) {
                    $mais               = $paginacao->pagina  + 2;
                    $ultima             = $paginacao->paginas - 1;
                    $paginacao->proxima = $mais;
                    $paginacao->ultima  = $ultima;
                }

                $inicio = $paginacao->pagina * $objetos[0]->get("pg_itens_pagina") + 1;
                $fim    = $inicio + count($objetos) - 1;


                if ($ajax) {
                    $view->setVariable("ajax", 'return false');
                }

                if ($classForm) {
                    $view->setVariable('classForms', $classForm);
                }

                $view->setVariable("align",           "left");
                $view->setVariable("row",             $inicio);
                $view->setVariable("itemPagina",      $fim);
                $view->setVariable("numeroRegistros", $objetos[0]->get("pg_numero_registros"));
                $view->setVariable("numPagina",       $objetos[0]->get("pg_pagina"));

                $view->setCurrentBlock("Criterios");
                foreach ($criterios as $campo => $valor) {
                    $view->setVariable("campo", $campo);
                    $view->setVariable("valor", $valor);
                    $view->parseCurrentBlock();
                }

                if ($paginacao->pagina > 0) {
                    $view->setCurrentBlock("sePrimeiraAnterior");
                    if ($classForm) {
                        $view->setVariable('classForm', " ".$classForm);
                    }
                    $view->setVariable("pag_primeira", $paginacao->primeira);
                    $view->setVariable('pag_anterior', $paginacao->anterior);
                    $view->parseCurrentBlock();
                } else {
                    $view->setCurrentBlock("senaoPrimeiraAnterior");
                    $view->setVariable('url', SITE);
                    $view->parseCurrentBlock();
                }

                if ($paginacao->pagina < $paginacao->paginas - 2) {
                    $view->setCurrentBlock('seProximaUltima');
                    $view->setVariable("pag_proxima", $paginacao->proxima);
                    $view->setVariable("pag_ultima",  $paginacao->ultima);
                    if ($classForm) {
                        $view->setVariable('classForm2', " ".$classForm);
                    }
                    $view->parseCurrentBlock();
                } else {
                    $view->setCurrentBlock("senaoProximaUltima");
                    $view->setVariable('url', SITE);
                    $view->parseCurrentBlock();
                }

            }
        }
        return $view->get();
    }
}
?>