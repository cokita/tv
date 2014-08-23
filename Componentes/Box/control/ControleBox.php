<?php

class ControleBox {

    public static function input($conteudo, $width = "80%", $possuiConteudo = true, $interface = "InterfaceBox.html") {

        $view = Control::carregarInterface($interface);

        if ($possuiConteudo) {

            $view->setCurrentBlock("Desdobravel");
            $view->setVariable("width", $width);
            $view->setVariable("conteudo", $conteudo->get());

            $view->parseCurrentBlock();
        }

        return $view;
    }

}

?>