<?php

class ImagemDAO extends DAO {

    /**
     * @param Model $imagem
     * @return int|Model|resource
     */
    public static function incluir(Model $imagem) {
        $sql = "insert into imagem (nome,
                                    extensao,
                                    caminho,
                                    caminho_thumb,
                                    id_usuario)
                            values (:nome,
                                    :extensao,
                                    :caminho,
                                    :caminho_thumb,
                                    :id_usuario)";

        $sucesso = self::exec($sql, $imagem);
        return $sucesso;
    }

    public static function consultar(Model $imagem) {
        $sql = "select *
                  from imagem";

        $criterio = self::criterio($imagem);

        if ($criterio) {
            $sql .= " where " . implode(" and ", $criterio);
        }

        $sql .= " order by data_inclusao desc";

        return self::query($sql, $imagem);
    }

    public static function alterar(Model $imagem) {
        $sql = "";

        $criterio = self::criterio($imagem, array("id_imagem"));

        if ($criterio && $imagem->get("id_imagem")) {
            $sql .= "update imagem
                        set " . implode(", ", $criterio);


            $sql .= " where id_imagem = :id_imagem";

            return self::exec($sql, $imagem);
        }
    }

    public static function remover(Model $imagem) {
        if ($imagem->get("id_imagem")) {
            $sql = "delete from imagem where id_imagem = :id_imagem";
        }

        return self::exec($sql, $imagem);
    }

    public static function consultarUltimaOrdem(Model $imagem)
    {
        $sql = "select max(ordem) as ordem from imagem;";

        return self::query($sql, $imagem);

    }

    public static function consultarImagensSemGaleria(Model $imagem){
        $sql = "select *
                  from imagem i
                 where id_imagem not in
                                  (select id_imagem
                                     from galeria_item
                                    where id_imagem is not null)
                   and ativo = :ativo";

        return self::query($sql, $imagem);
    }

    public static function consultarImagensNaGaleria(Model $imagem){
        $sql = "select *
                  from imagem i
                 where id_imagem in
                                  (select id_imagem
                                     from galeria_item
                                    where id_imagem is not null)
                   and ativo = :ativo";

        return self::query($sql, $imagem);
    }


}

?>
