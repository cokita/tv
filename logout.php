<?php
    if (require_once("app/config/config.php")) {
        session_destroy();
        setcookie('id_usuario',     null);
        header("Location: " . SITE);
    }else{
        echo "erro ao incluir config.php";
    }
?>