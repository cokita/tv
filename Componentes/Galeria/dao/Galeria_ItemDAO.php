<?php

class Galeria_ItemDAO extends DAO {

    /**
     *
     * @param Model $galeria_item
     * @return int|Model|resource
     */
    public static function incluir(Model $galeria_item) {
        $sql = "insert into galeria_item (
                                    id_imagem,
                                    id_youtube,
                                    ordem,
                                    id_usuario,
                                    id_galeria)
                            values (
                                    :id_imagem,
                                    :id_youtube,
                                    :ordem,
                                    :id_usuario,
                                    :id_galeria)";

        $sucesso = self::exec($sql, $galeria_item);
        return $sucesso;
    }

    public static function consultar(Model $galeria_item) {
        $sql = "select *
                  from galeria_item";

        $criterio = self::criterio($galeria_item);

        if ($criterio) {
            $sql .= " where " . implode(" and ", $criterio);
        }

        $sql .= " order by data_inclusao desc";

        return self::query($sql, $galeria_item);
    }

    public static function alterar(Model $galeria_item) {
        $sql = "";

        $criterio = self::criterio($galeria_item, array("id_galeria_item"));

        if ($criterio && $galeria_item->get("id_galeria_item")) {
            $sql .= "update galeria_item
                        set " . implode(", ", $criterio);


            $sql .= " where id_galeria_item = :id_galeria_item";

            return self::exec($sql, $galeria_item);
        }
    }

    public static function remover(Model $galeria_item) {
        if ($galeria_item->get("id_galeria_item")) {
            $sql = "delete from galeria_item where id_galeria_item = :id_galeria_item";
        }

        return self::exec($sql, $galeria_item);
    }

    public static function removerTodosPorGaleria(Model $galeria_item) {
        if($galeria_item->get('id_galeria')){
            $sql = "delete from galeria_item where id_galeria = :id_galeria";
        }

        return self::exec($sql, $galeria_item);
    }

}

?>
