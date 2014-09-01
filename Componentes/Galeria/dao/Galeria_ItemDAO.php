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

    public function recuperarItems(Model $galeria_item){
        $sql = "select
                    id,
                    caminho,
                    duracao,
                    id_galeria,
                    ordem,
                    tipo
                from (
                        select
                            i.id_imagem   as id,
                            i.caminho     as caminho,
                            null          as duracao,
                            gi.id_galeria as id_galeria,
                            gi.ordem      as ordem,
                            'i'           as tipo
                        from galeria_item gi
                        inner join imagem i
                            on gi.id_imagem = i.id_imagem
                        where i.ativo = 1
                        union
                        select
                            y.id_youtube  as id,
                            y.url         as caminho,
                            y.duracao     as duracao,
                            gi.id_galeria as id_galeria,
                            gi.ordem      as ordem,
                            'v'           as tipo
                        from galeria_item gi
                        inner join youtube y
                            on gi.id_youtube = y.id_youtube
                        where y.ativo = 1
                ) as galeria
                where id_galeria = 1
                order by ordem";

        return self::query($sql, $galeria_item);
    }

}

?>
