<?php
function __autoLoad($classe) {
    $caminho = "";
    $estiloPEAR = str_ireplace("_", "/", $classe) . ".php";
    if (file_exists(CONFIG . "$classe.php")) {
        $caminho = CONFIG . "$classe.php";
    } else if (file_exists(CONTROL . "$classe.php")) {
        $caminho = CONTROL . "$classe.php";
    } else if (file_exists(PROJETO . "$classe.php")) {
        $caminho = PROJETO . "$classe.php";
    } else if (file_exists(DAO . "$classe.php")) {
        $caminho = DAO . "$classe.php";
    } else if (file_exists(MODEL . "$classe.php")) {
        $caminho = MODEL . "$classe.php";
    }else if(file_exists($classe.".php")){
        $caminho = $classe.".php";
    }else if (strpos($estiloPEAR, "/") and file_exists(LIBS . "PEAR/$estiloPEAR")) {
        $caminho = LIBS . "PEAR/$estiloPEAR";
    } else if (file_exists(LIBS . "phpmailer/$classe.php")) {
        $caminho = LIBS . "phpmailer/$classe.php";
    } else if (file_exists(LIBS . "thumb/$classe.class.php")) {
        $caminho = LIBS . "thumb/$classe.class.php";
    }else{
        if (strpos($classe, "_")) {
            if (substr($classe, 0, 5) == "Model") {
                $tipo       = "model";
                $componente = substr($classe, 5, strpos($classe, "_") - 5);
            } else if (substr($classe, 0, 4) == "View") {
                $tipo       = "view";
                $componente = substr($classe, 4, strpos($classe, "_") - 3);
            } else if (substr($classe, 0, 8) == "Controle") {
                $tipo       = "control";
                $componente = substr($classe, 8, strpos($classe, "_") - 8);
            } else if (substr($classe, 0, 5) == "Admin") {
                $tipo       = "control";
                $componente = substr($classe, 5, strpos($classe, "_") - 5);
            } else if (substr($classe, -3, 3) == "DAO") {
                $tipo       = "dao";
                $componente = substr($classe, 0, strpos($classe, "_"));
            }

            if ($componente) {
                $componente = ucfirst($componente);
                if (file_exists("Componentes/$componente/$tipo/$classe.php")) {
                    $caminho = "Componentes/$componente/$tipo/$classe.php";
                }
            }
        } else {
            if (preg_match("/(Admin|Controle)(.*)/", $classe, $ocorrencias)) {
                $pacote = $ocorrencias[2];
                if (file_exists("Componentes/$pacote/control/$classe.php")) {
                    $caminho = "Componentes/$pacote/control/$classe.php";
                } else if (file_exists("Componentes/$pacote/control/$pacote.php")) {
                    $caminho = "Componentes/$pacote/control/$pacote.php";
                }
            } else {
                if (substr($classe, -3, 3) == "DAO") {
                    $tipo   = "dao";
                    $pacote = substr($classe, 0, strlen($classe) - 3);

                    if (file_exists("Componentes/$pacote/$tipo/$classe.php")) {
                        $caminho = "Componentes/$pacote/$tipo/$classe.php";
                    }
                }else{
                   if (substr($classe, 0, 5) == "Model") {
                    $tipo   = "model";
                    $pacote = substr($classe, 5, strlen($classe));

                    if (file_exists("Componentes/$pacote/$tipo/$classe.php")) {
                        $caminho = "Componentes/$pacote/$tipo/$classe.php";
                    }
                }
                }
            }
        }
    }
    
    if ($caminho) {
        require_once "$caminho";
    }

    require_once(CONFIG . "Functions.php");
}
?>
