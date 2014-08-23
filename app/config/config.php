<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set("display_errors",  "on");
ini_set("default_charset", "UTF-8");
session_cache_expire(3600); // Uma hora.
session_start();
/*
 * URL onde esta hospedado o sistema
 * ex: http://www.meusiste.com.br
 */
define("SITE", "http://local.tvcorporativa.com.br/");
define("URL",            preg_replace("((admin|control|index).php)", "", $_SERVER["SCRIPT_NAME"]));
define("URL_LINK",       preg_replace("(/(admin|control|index).php)", "", $_SERVER["SCRIPT_NAME"]));
//Essa constante serve para armazenar o nome da pasta que foi chamado o projeto. Se houver.
define("NOME_PASTA", "");
define("PASTA_UPLOAD", "upload_files");
define("DBDRIVE", "dblib");
define("DBPORTA", "3306");
define("DBHOST", "127.0.0.1");
define("DBUSUARIO", "root");
define("DBSENHA", "chiclete");
define("DBNOME", "tv");

define("APP",            "app/");
define("JS",             "js/");
define("CONFIG",         APP . "config/");
define("CONTROL",        APP . "control/");
define("DAO",            APP . "dao/");
define("LIBS",           APP . "libs/");
define("MODEL",          APP . "model/");
define("PROJETO",        APP . "projeto/");
define("HTML_COMPACTO",  false);

require_once(CONFIG . "autoLoad.php");

?>
