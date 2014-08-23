<?php

class BancoDAO extends DAO{

    public static function incluir(Model $banco) {
        $sql = "insert into banco (codigo,
                                   descricao)
                            values (:codigo,
                                    :descricao)";
        
        $sucesso = self::exec($sql, $banco);

        return $sucesso;
    }

    public static function consultar(Model $banco) {
        $sql = "select *
                  from banco";

        $criterio = self::criterio($banco);

        if ($criterio) {
            $sql .= " where " . implode(" and ", $criterio);
        }

        $sql .= " order by destaque desc, codigo";

        return self::query($sql, $banco);
    }

    public static function alterar(Model $banco) {
        $sql = "";

        $criterio = self::criterio($banco, array("id_banco"));

        if ($criterio && $banco->get("id_banco")) {
            $sql .= "update banco
                        set " . implode(", ", $criterio);
                    
            $sql .= " where id_banco = :id_banco";
            
            return self::exec($sql, $banco);
        }
    }

    public static function remover(Model $banco) {
        if($banco->get("id_banco")){
            $sql = "delete from banco where id_banco = :id_banco";
        }

        return self::exec($sql, $banco);
    }
}
?>
