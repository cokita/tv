<?php
/**
 * @package Model
 * @access public
 * @filesource
 */
abstract class Model {
    public static $MESES = array("JAN" => "01", "FEB" => "02", "MAR" => "03",
                                 "APR" => "04", "MAY" => "05", "JUN" => "06",
                                 "JUL" => "07", "AUG" => "08", "SEP" => "09",
                                 "OCT" => "10", "NOV" => "11", "DEC" => "12");
    public static $MESES_EXTENSO = array("JAN" => "JANEIRO",  "FEB" => "FEVEREIRO",
                                         "MAR" => "MARCO",    "APR" => "ABRIL",
                                         "MAY" => "MAIO",     "JUN" => "JUNHO",
                                         "JUL" => "JULHO",    "AGO" => "AGOSTO",
                                         "SEP" => "SETEMBRO", "OCT" => "OUTUBRO",
                                         "NOV" => "NOVEMBRO", "DEC" => "DEZEMBRO");

    public function __construct($params = array()) {
        $obj_vars = $this->get_object_vars();
        $keys     = array_keys($obj_vars);

        foreach ($keys as $key) {
            if (isset($params[$key])) {
                $this->$key = $params[$key];
            }
        }
    }

    /**
     * Método que simula os __set
     *
     * @param object $attr
     * @param $value
     * @return void
     */
    public function set($attr, $value) {
        $this->$attr = mb_convert_encoding($value, "ISO-8859-1", "UTF-8");
    }

    /**
     * Método que simula os __set (sem indução de tipos)
     *
     * @param object $attr
     * @param $value
     * @return void
     */
    public function setDB($attr, $value) {
        $this->$attr = $value;
    }

    /**
     * Método que simula os __set
     *
     * @param object $attr
     * @param $value
     * @return void
     */
    public function setNull($attr) {
        unset($this->$attr);
    }

    /**
     * Método que simula os __get
     *
     * @param object $attr
     * @return atributo
     */
    public function get($attr) {
        if ($attr){
            if (getEncoding($this->$attr) == "UTF-8") {
                $resultado = $this->$attr;
            } else {
                $resultado = mb_convert_encoding($this->$attr, "UTF-8");
            }
        }

        return $resultado;
    }

    public function getDB($attr) {

        return $this->$attr;
    }

    public function hasSet($attr) {

        return isset($this->$attr);
    }

    /**
     * @param Model $attr
     * @return Date
     */
    public function getDate($attr) {
        $formatoCurto    = "(\d{4})[-\/](( \d)|(\d{1,2}))[-\/](\d{1,2})";
        $formato         = "(\d{4})[-\/](( \d)|(\d{1,2}))[-\/](\d{1,2})(\s(\d{2}):(\d{2}):(\d{2})(\.\d*)?)?";
        $formatoLongo    = "(\w{3}) (( \d)|(\d{1,2})) (\d{4})";
        $formato_oci     = "(\d{1,2})[-\/]((\w{3})|(\d{1,2}))[-\/](\d{2,4})(\s(\d{2})\:(\d{2})\:(\d{2})(\.\w*)?)?";
        /*
         * Verificamos a existência de uma data nos formatos:
         * dd-mm-yyyy, dd/mm/yyyy
         */
         
        if (preg_match("/^$formatoCurto$/", $this->$attr, $data)) {
            $data[2] = substr("0" . preg_replace("/ /", "0", $data[2]), -2);
            $data[5] = substr("0" . preg_replace("/ /", "0", $data[5]), -2);
            $value   = sprintf("%s/%s/%s", $data[5], $data[2], $data[1]);
        } else if (preg_match("/^$formato$/", $this->$attr, $data)) {
            $data[5] = substr("0" . preg_replace("/ /", "0", $data[5]), -2);
            $value   = sprintf("%s/%s/%s", $data[5], $data[2], $data[1]);
        } else if (preg_match("/^$formatoLongo/", $this->$attr, $data)) {
            $data[2] = substr("0" . preg_replace("/ /", "0", $data[2]), -2);
            $value   = sprintf("%s/%s/%s", $data[2], self::$MESES[strtoupper($data[1])], $data[5]);
        } else if (preg_match("/^$formato_oci/", $this->$attr, $data)) {
            $pre_ano = (integer) substr(date("Y"), 0, 2);
            if ($data[5] >= 50){
                $pre_ano--;
            }
            $value = sprintf("%s/%s/%s", $data[1], self::$MESES[$data[2]], $pre_ano . $data[5]);
        } else {
            $value = $this->$attr;
        }

        return $value;
    }

    /**
     * Retorna um campo data (no formato dd-mm-yy e, opcionalmente, a hora.
     *
     * @param Model $attr
     * @return Date
     */
    public function getDateTime($attr, $formatoAmigavel = false, $segundos = false) {
        $formatoCurto    = "(\d{4})[-\/]( \d|\d\d)[-\/](\d{2})";
        $formatoLongo    = "$formatoCurto (\d{2}):(\d{2}):(\d{2})(\.\d*)?";
        $formatoLongoMS  = "$formatoCurto (\d{2}):(\d{2}):(\d{2}):(\d*)";
        $formatoCompleto = "(\w{3}) ( \d|\d{2}) (\d{4}) (\d{2}):(\d{2}):(\d{2}):(\d*)(AM|PM)";
        $formato_oci     = "(\d{2})[-\/]((\w{3})|(\d{1,2}))[-\/](\d{2,4})(\s(\d{2})[\.\:](\d{2})[\.\:](\d{2})[\.\:]?(\d*)?\s(AM|PM)?)?";

        if (key_exists($attr, get_object_vars($this))) {
            $valorAtributo = $this->$attr;
        } else {
            $valorAtributo = $attr;
        }

        /*
         * Verificamos a existência de uma data nos formatos:
         * yyyy-mm-dd ou yyyy/mm/dd
         */
        if (preg_match("/^$formatoCompleto/", $valorAtributo, $data)) {
            $data[2] = preg_replace("/ /", "0", $data[2]);
            if ($formatoAmigavel) {
                $value = sprintf($segundos ? "%s de %s de %s às %s:%s:%s" : "%s de %s de %s às %s:%s",
                                 $data[2], self::$MESES_EXTENSO[strtoupper($data[1])], $data[3],
                                 ($data[8] == "PM" ? 12 : 0) + $data[4], $data[5], $segundos ? $data[6] : "");
            } else {
                if ($data[4] == 12) {
                    $hora = ($data[8] == "AM" ? - 12 : 0) + $data[4];
                } else {
                    $hora = ($data[8] == "PM" ? 12 : 0) + $data[4];
                }
                $value = sprintf("%s/%s/%s %s:%s:%s",
                                 $data[2], self::$MESES[strtoupper($data[1])], $data[3],
                                 $hora, $data[5], $data[6]);
            }

        } else if (preg_match("/^$formato_oci/", $valorAtributo, $data)) {
            if ($formatoAmigavel) {
                $value = sprintf($segundos ? "%s de %s de %s às %s:%s:%s" : "%s de %s de %s às %s:%s",
                                 $data[1], self::$MESES_EXTENSO[$data[2]], "20".$data[5],
                                 ($data[11] == "PM" ? 12 : 0) + $data[7], $data[8], $segundos ? $data[9] : "");
            } else {
                $value = sprintf("%s/%s/%s %s:%s:%s",
                                 $data[1], self::$MESES[$data[2]], "20".$data[5],
                                 ($data[11] == "PM" ? 12 : 0) + $data[7], $data[8], $data[9]);
            }
        } else if (preg_match("/^$formatoLongoMS$/", $valorAtributo, $data)) {
            $value = sprintf("%s/%s/%s %s:%s:%s:%s",
                             $data[3], $data[2], $data[1],
                             $data[4], $data[5], $data[6], $data[7]);
        } else if (preg_match("/^$formatoLongo$/", $valorAtributo, $data)) {
            $value = sprintf("%s/%s/%s %s:%s:%s",
                             $data[3], $data[2], $data[1],
                             $data[4], $data[5], $data[6]);
        } else if (preg_match("/^$formatoCurto$/", $valorAtributo, $data)) {
            $value = sprintf("%s/%s/%s", $data[3], $data[2], $data[1]);
        } else {
            $value = $valorAtributo;
        }
        
        return $value;
    }

    /**
     * Retorna um campo hora (no formato hh:mm ou hh:mm:ss.
     *
     * @param Model $attr
     * @return Time
     */
    public function getTime($attr, $segundos = false, $ampm = false) {
        $resultado      = "";
        $formatoCurto   = "(\d\d):(\d\d)";
        $formatoLongo   = "$formatoCurto:(\d\d)";
        $formato24horas = "$formatoLongo:(\d\d\d)(AM|PM)";
        $formato_oci     = "(\d{2})[-\/]((\w{3})|(\d{1,2}))[-\/](\d{2,4})(\s(\d{2})\:(\d{2})\:(\d{2})(\.\w*)?)?";

        /*
         * Verificamos a existência de uma hora nos formatos:
         * hh:mm ou hh:mm:ss
         */
        if (preg_match("/$formato24horas$/", $this->$attr, $data)) {
            $resultado = sprintf("%s:%s:%s%s", $data[1], $data[2], $data[3], $data[5]);
        } else if (preg_match("/^$formatoLongo/", $this->$attr, $data)) {
            $resultado = sprintf("%s:%s:%s", $data[1], $data[2], $data[3]);
        } else if (preg_match("/^$formatoCurto/", $this->$attr, $data)) {
            $resultado = sprintf("%s:%s", $data[1], $data[2]);
        } else if (preg_match("/^$formato_oci/", $this->$attr, $data)) {
            $resultado = sprintf("%s:%s", $data[7], $data[8]);
        } else {
            $resultado = $this->$attr;
        }

        if (!$ampm) {
            if (!$segundos) {
                $resultado = date("H:i", strtotime($resultado));
            } else {
                $resultado = date("H:i:s", strtotime($resultado));
            }
        } else {
            if ($segundos) {
                $resultado = $resultado;
            } else {
                $resultado = sprintf("%s:%s%s", $data[1], $data[2], $data[5]);
            }
        }

        return $resultado;
    }

    public function getDateRelative($attr, $dataFinal = "") {
        if (! $dataFinal) {
            $dataFinal = date("Y/m/d H:i:s:000");
        }

        $final       = self::getDateTime($dataFinal);
        $inicial     = self::getDateTime($attr);
        $dataInicial = substr($inicial, 0, 10);
        $dataFinal   = substr($final,   0, 10);
        $horaInicial = substr($inicial, 11);
        $horaFinal   = substr($final,   11);

        if ($dataInicial == $dataFinal) {
            /*
             * As datas são iguais.
             * A diferença deve estar nas horas.
             */
            $segundosInicial = (substr($horaInicial, 0, 2) * 60 * 60) +
                               (substr($horaInicial, 3, 2) * 60) +
                               (substr($horaInicial, 6, 2));
            $segundosFinal   = (substr($horaFinal, 0, 2) * 60 * 60) +
                               (substr($horaFinal, 3, 2) * 60) +
                               (substr($horaFinal, 6, 2));
            $diferenca       = $segundosFinal - $segundosInicial;

            if ($diferenca == 0 && ! $grandeza) {
                $diferenca = "agora!";
            } else if ($diferenca < 60) {
                $grandeza = "segundo";
            } else if ($diferenca < 3600) {
                $diferenca = intval($diferenca / 60);
                $grandeza  = "minuto";
            } else {
                $diferenca = intval($diferenca / 60 / 60);
                $grandeza = "hora";
            }

            $tempo = array("atrás", "no futuro");
        } else if (substr($dataInicial, 3, 7) == substr($dataFinal, 3, 7)) {
            /*
             * Os meses e anos das datas são iguais.
             * A diferença deve estar entre dias ou horas.
             */
            $diferenca = substr($dataFinal, 0, 2) - substr($dataInicial, 0, 2);
            if ($diferenca < 7) {
                $grandeza  = "dia";
                $tempo     = array("atrás", "no futuro");
            } else {
                $diferenca = $inicial;
            }
        } else {
            $diferenca = $inicial;
        }

        if ($diferenca > 1 || $diferenca < 1) {
            $grandeza .= "s";
        }

        if ($diferenca <> "agora!" && $tempo) {
            if ($diferenca < 0) {
                $diferenca = (Integer) ($diferenca * -1) . " " . $grandeza . " " . $tempo[1];
            } else {
                $diferenca = (Integer) $diferenca . " " . $grandeza . " " . $tempo[0];
            }
        }

        return $diferenca;
    }

    public function getTimeStamp($attr) {
        /*$mascara = "/(... \d{1,2} \d{4} \d{2}:\d{2}:\d{2}):\d{3}([AP]M)/";

        if (preg_match($mascara, $this->$attr, $ocorrencias)) {
            $resultado = strtotime($ocorrencias[1] . $ocorrencias[2]);
        }
        */

        $data = $this->getDate($attr);
        $hora = $this->getTime($attr);
        $timestamp = retornaTimeUNIXBR($data, $hora);

        return $timestamp;
    }

    public function get_object_vars() {

        return get_object_vars($this);
    }

    public function getKey() {

        return key(get_object_vars($this));
    }

    public function getPrimeiroNome() {
        $nomes = explode(' ', mb_convert_encoding($this->nome, "UTF-8"));

        return $nomes[0];
    }

    public function show() {

        _debug($this, false, true);
    }
}
?>