<?php

/**
 * @package DAO
 * @access  public
 * @filesource
 */

/**
 * Interface de onde todas as classes DAO devem implementar, garantindo que, ao
 * menos os quatro métodos estejam disponíveis para as classes de acesso aos
 * dados. Outros métodos podem ser criados, desde que não interfiram no
 * funcionamento básico dos pré existentes.
 */
abstract class DAO {

    public abstract static function incluir(Model $registro);

    public abstract static function consultar(Model $registro);

    public abstract static function alterar(Model $registro);

    public abstract static function remover(Model $registro);

    public static function prepare($sql, $params) {
        $conn = Conexao::obterConexao();
        $formatoCurto = "(\d\d)[-\/](\d\d)[-\/](\d\d\d\d)";
        $formatoLongo = "$formatoCurto (\d\d):(\d\d):(\d\d)";
        $formatoParcial = "$formatoCurto (\d\d):(\d\d)";
        $formatoCompleto = "$formatoLongo(AM|PM)";

        if (is_object($params)) {
            $obj_vars = $params->get_object_vars();
            $keys = array_keys($obj_vars);

            foreach ($keys as $key) {
                /*
                 * @TODO: Verificar a maneira correta de remover as tags html, sem atrapalhar o base64 das
                 * imagens sem que seja gerado um warning.
                 */
                //$value = @htmlentities($params->get($key));
                $value = $params->getDB($key);
                $param = ":$key";

                if (!is_array($value) && $params->hasSet($key) && ($value <> "null")) {

                    /*
                     * Verificamos a existência de aspas anteriores.
                     */

                    if (!(eregi("/'[^']*'/", $sql) || is_numeric($value))) {

                        if (preg_match("/^$formatoCompleto$/", $value, $data)) {
                            /*
                             * Verificamos a existência de uma data nos formatos:
                             * dd-mm-yyyy hh:mm:ssAM ou dd/mm/yyyy hh:mm:ssAM
                             */
                            $value = sprintf("%s-%s-%s %s:%s:%s%s", $data[3], $data[2], $data[1], $data[4], $data[5], $data[6], $data[7]);
                        } else if (preg_match("/^$formatoLongo$/", $value, $data)) {
                            /*
                             * Verificamos a existência de uma data nos formatos:
                             * dd-mm-yyyy hh:mm:ss ou dd/mm/yyyy hh:mm:ss
                             */
                            $value = sprintf("%s-%s-%s %s:%s:%s", $data[3], $data[2], $data[1], $data[4], $data[5], $data[6]);
                        } else if (preg_match("/^$formatoParcial$/", $value, $data)) {
                            /*
                             * Verificamos a existência de uma data nos formatos:
                             * dd-mm-yyyy hh:mm:ss ou dd/mm/yyyy hh:mm
                             */
                            $value = sprintf("%s-%s-%s %s:%s:%s", $data[3], $data[2], $data[1], $data[4], $data[5], "00");
                        } else if (preg_match("/^$formatoCurto$/", $value, $data)) {
                            /*
                             * Verificamos a existência de uma data nos formatos:
                             * dd-mm-yyyy ou dd/mm/yyyy
                             */
                            $value = sprintf("%s-%s-%s", $data[3], $data[2], $data[1]);
                        }

                        if ($value) {
                            $value = "'" . $value . "'"; //$conn->quote($value);
                        } else {
                            $value = "null";
                        }
                    }
                } else {
                    $value = "null";
                }

                $sql = preg_replace("/($param\b)/i", $value, $sql);
                
                //CASO necessario retiro aspas duplicadas.
                if (eregi("''[^']*''", $sql)) {
                    $sql = str_replace("''", "'", $sql);
                }
            }
        }

        /*
         * Parâmetros não utilizados são silenciosamente eliminados.
         */
        $sql = preg_replace("/\s+:[a-zA-Z_]*/", " null", $sql);

        return $sql;
    }

    /**
     * Atribui os parâmetros à consulta e a executa, retornando uma coleção de objetos do tipo indicado (parâmetro $class).
     *
     * @param String $sql
     * @param Model|Array $class Objeto descendente de Model ou vetor (utilizado pelo método prepare()) que contém os parâmetros que serão usados na consulta.
     * @return <type>
     */
    public static function query($sql, $class) {
        $conn = Conexao::obterConexao();
        $sql = self::prepare($sql, $class);
        if (property_exists($class, 'pg_pagina')) {
            if ($class->get("pg_pagina")) {
                $posicao = strpos(strtolower($sql), " order by ");
                if ($posicao) {
                    $ordem = preg_replace("/(\w*\.)/", "", substr($sql, $posicao + 10));
                    $sql = substr($sql, 0, $posicao);
                } else {
                    $ordem = $class->get("pg_ordem");
                }

                if ($class->get("pg_registros")) {
                    $registros = ", " . $class->get("pg_registros");
                }

                $sql = self::paginarQuery($sql, $ordem, $class->get("pg_pagina"), $class->get("pg_registros"));
            }
        }

        $stmt = mysql_query($sql) or die(mysql_error());
        if ($stmt) {
            while ($x = mysql_fetch_object($stmt, get_class($class))) {
                $resultado[] = $x;
            }
        }
        return $resultado;
    }

    /**
     * Executa uma consulta determinada.
     *
     * @param String $sql
     * @param Model|Array $class Objeto descendente de Model ou vetor (utilizado pelo método prepare()) que contém os parâmetros que serão usados na consulta.
     * @return <type>
     */
    public static function exec($sql, Model $modelo) {
        $conn = Conexao::obterConexao();
        $sql = self::prepare($sql, $modelo);
        $sql = html_entity_decode($sql);
        $chave = $modelo->getKey();
        $id = 0;

        if (preg_match("/^insert into/i", $sql)) {
            if ($_SESSION["banco"] == "pgsql") {
                $sql .= " returning " . $chave;

                $resultado = self::query($sql, $modelo);
                $registrosAfetados = count($resultado);

                if ($registrosAfetados) {
                    $id = $resultado[0]->get($chave);
                }
            } else {
                $registrosAfetados = mysql_query($sql) or die(mysql_error());
                if ($registrosAfetados) {
                    $id = self::lastInsertedId($sql, $modelo);
                }
            }

            if ($id) {
                $modelo->set($chave, $id);
                return $modelo;
            }
        } else {
            $registrosAfetados = mysql_query($sql) or die(mysql_error());
            return $registrosAfetados;
        }
    }

    private static function lastInsertedId($sql = "", $class = null) {
        $resultado = 0;
        /**
         * NOTA: Veja o motivo da construção peculiar em:
         *       http://br.php.net/manual/en/pdo.lastinsertid.php
         */
        $conn = Conexao::obterConexao();

        if (preg_match_all("/^insert into (\w*)\s*/i", $sql, $insert_itens)) {
            $sqlUltimoId = "SELECT LAST_INSERT_ID() as last_id";

            $stmt = mysql_query($sqlUltimoId) or die(mysql_error());
            if ($stmt) {
                while ($x = mysql_fetch_object($stmt, get_class($class))) {
                    $resultado = $x->get("last_id");
                }
            }
        }
        return $resultado;
    }

    public static function criterio(Model $registro, $exclusao = array()) {

        $obj_vars = $registro->get_object_vars();
        $keys = array_keys($obj_vars);
        $criterio = array();

        foreach ($obj_vars as $obj => $valor) {
            if (!in_array($obj, $exclusao)) {
                if ($registro->hasSet($obj)) {
                    $criterio[] = "$obj = :$obj";
                }
            }
        }

        return $criterio;
    }

    public static function obterTitulos($classeBase) {

        $titulos = array();

        foreach (get_declared_classes() as $classe) {
            if (substr($classe, 0, 8) == "Controle") {
                if (!is_subclass_of($classeBase, $classe)) {
                    $controle = new $classe($classeBase->get("perfil"),
                                    $classeBase->get("params"));
                    if (is_subclass_of($controle, get_class($classeBase))) {
                        $DAO = substr($classe, 8, strlen($classe) - 16) . "DAO";
                        $titulos[substr($classe, 8)]["ordem"] = $controle->get("ordem");
                        $titulos[substr($classe, 8)]["titulo"] = $controle->get("titulo");
                    }
                }
            }
        }

        return $titulos;
    }

    public static function consultarGeral($classeBase, $criterios, $metodo, $ordem) {

        $resultado = "";

        foreach (get_declared_classes() as $classe) {
            if (substr($classe, 0, 21) == "ControlePesquisaGeral") {
                if (!is_subclass_of($classeBase, $classe)) {
                    $controle = new $classe($classeBase->get("perfil"),
                                    $classeBase->get("params"));
                    if (is_subclass_of($controle, get_class($classeBase))) {
                        $DAO = substr($classe, 22, strlen($classe) - 4) . "DAO";
                        $pesquisas[$classe] = @call_user_func(array($DAO, $metodo), $criterios);
                    }
                }
            }
        }

        if ($pesquisas) {
            $sql = implode(" union ", $pesquisas) . " order by id_imagem, $ordem desc";
            $resultado = self::query($sql, $criterios);
        }

        return $resultado;
    }

    private static function paginarQuery($sql, $ordem_colunas, $pagina, $itens_pagina) {
        if (!$itens_pagina) {
            $itens_pagina = 25;
        }

        if ($pagina == 1) {
            $inicio = 0;
        } else {
            $inicio = ($itens_pagina * $pagina) - $itens_pagina;
        }

        $fim = $inicio + $itens_pagina;
        $resultado = substr($sql, 9, strlen($sql) - 1);

        $resultado = "select *, " . $pagina . " as pg_pagina,
                                 " . $itens_pagina . " as pg_itens_pagina,
                                 (select count(*) from (" . $sql . ") as x) as pg_numero_registros
                                 " . $resultado . "
                        order by " . $ordem_colunas . "
                         limit " . $inicio . "," . $itens_pagina;
        return $resultado;
    }

}

?>
