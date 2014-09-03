<?php

function _debug($var, $die = false,$mode2 = false, $pilha = false) {
    echo "<div style=\"border: 3px solid #FF0000; background: #FFEE99; margin: 10px; padding: 10px;\"><pre>";

    if ($mode2) {
        var_dump($var);
    } else {
        print_r($var);
    }

    if ($pilha) {
        echo "<hr>";
        $e = new Exception();
        echo $e->getTraceAsString();
    }

    echo "</pre></div>";
    if($die){
        die();
    }
}

function show_404($return_page = '') {

    $view = Control::carregarInterface("show_404.html");

    if ($return_page != '') {
        $view->setVariable('page', $return_page);
    } else {
        $view->setVariable('page', './');
    }
    $view->setVariable('mensagem', 'Página não encontrada.');

    return $view;
}

function getEncoding($str) {
    $charsets = array("UTF-8", "ISO-8859-1", "ASCII", "UTF-16", "Windows-1252");
    return mb_detect_encoding($str . "z", $charsets);
}

function RemoveAcentos($str, $enc = 'UTF-8') {

    $acentos = array(
        'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
        'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
        'C' => '/&Ccedil;/',
        'c' => '/&ccedil;/',
        'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
        'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
        'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
        'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
        'N' => '/&Ntilde;/',
        'n' => '/&ntilde;/',
        'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
        'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
        'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
        'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
        'Y' => '/&Yacute;/',
        'y' => '/&yacute;|&yuml;/',
        'a.' => '/&ordf;/',
        'o.' => '/&ordm;/'
    );

    return preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_NOQUOTES, $enc));
}

function formatDataDB($data) {
    $arrData = explode("/", $data);
    return $arrData[2] . "-" . $arrData[1] . "-" . $arrData[0];
}

function formatDataWEB($data) {
    $arrData = explode(" ", $data);
    $data = explode("-", $arrData[0]);
    return $data[2] . "/" . $data[1] . "/" . $data[0];
}

function _debugToFile($value, $print_r = false, $novoArquivo = false) {
    $caminho = $_SERVER["SCRIPT_FILENAME"];
    _debug($caminho);
    $pos = strrpos($caminho, "/");
    $arquivo = substr($caminho, 0, $pos + 1) . "err.log";

    if ($print_r == true) {
        $e = new Exception();
        $value = print_r($value, true) . "\n" . $e->getTraceAsString();
    }

    $f = fopen($arquivo, ($novoArquivo ? "w+" : "a+"));
    $linha = date("d/m/Y H:i:s") . " : " . $value . "\n";
    fwrite($f, $linha);
    fclose($f);
}

function my_ucwords($string){

        $invalid_characters = array('"',
                                    '\(',
                                    '\[',
                                    '\/',
                                    '<.*?>',
                                    '<\/.*?>');

        foreach($invalid_characters as $regex){
            $string = preg_replace('/('.$regex.')/','$1 ',$string);
        }

        $string=ucwords($string);

        foreach($invalid_characters as $regex){
            $string = preg_replace('/('.$regex.') /','$1',$string);
        }

        return $string;
    }

function title_case($title) {
    $smallwordsarray = array(
        'da', 'de', 'a', 'e', 'o', 'u', 'não', 'do');

    $words = explode(' ', $title);
    foreach ($words as $key => $word) {
        if ($key == 0 or !in_array($word, $smallwordsarray))
            $words[$key] = my_ucwords(strtolower($word));
    }

    $newtitle = implode(' ', $words);
    return $newtitle;
}

?>
