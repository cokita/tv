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
define("SITE", "http://cokita.com.br/");
define("URL",            preg_replace("((admin|control|index).php)", "", $_SERVER["SCRIPT_NAME"]));
define("URL_LINK",       preg_replace("(/(admin|control|index).php)", "", $_SERVER["SCRIPT_NAME"]));
//Essa constante serve para armazenar o nome da pasta que foi chamado o projeto. Se houver.
define("NOME_PASTA", "tvcorporativa");
define("PASTA_UPLOAD", "upload_files");
define("DBDRIVE", "dblib");
define("DBPORTA", "3306");
define("DBHOST", "pdb9.awardspace.net");
define("DBUSUARIO", "1445347_tv");
define("DBSENHA", "@tvcaixa2014");
define("DBNOME", "1445347_tv");

define("APP",            "app/");
define("JS",             "js/");
define("CONFIG",         APP . "config/");
define("CONTROL",        APP . "control/");
define("DAO",            APP . "dao/");
define("LIBS",           APP . "libs/");
define("MODEL",          APP . "model/");
define("PROJETO",        APP . "projeto/");
define("HTML_COMPACTO",  false);

define('LARGURA', 1280);
define('ALTURA', 720);

//Api do youtube
define("YT_API_URL", "http://gdata.youtube.com/feeds/api/videos?q=");

require_once(CONFIG . "autoLoad.php");

?>
