<?php

class Video_YoutubeDAO extends DAO {

    /**
     *
     * @param Model $video_youtube
     * @return int|Model|resource
     */
    public static function incluir(Model $video_youtube) {
        $sql = "insert into youtube (titulo,
                                    descricao,
                                    thumbnail,
                                    duracao,
                                    url,
                                    id_usuario)
                            values (:titulo,
                                    :descricao,
                                    :thumbnail,
                                    :duracao,
                                    :url,
                                    :id_usuario)";

        $sucesso = self::exec($sql, $video_youtube);
        return $sucesso;
    }

    public static function consultar(Model $video_youtube) {
        $sql = "select *
                  from youtube";

        $criterio = self::criterio($video_youtube);

        if ($criterio) {
            $sql .= " where " . implode(" and ", $criterio);
        }

        $sql .= " order by data_inclusao desc";

        return self::query($sql, $video_youtube);
    }

    public static function alterar(Model $video_youtube) {
        $sql = "";

        $criterio = self::criterio($video_youtube, array("id_youtube"));

        if ($criterio && $video_youtube->get("id_youtube")) {
            $sql .= "update youtube
                        set " . implode(", ", $criterio);


            $sql .= " where id_youtube = :id_youtube";

            return self::exec($sql, $video_youtube);
        }
    }

    public static function remover(Model $video_youtube) {
        if ($video_youtube->get("id_youtube")) {
            $sql = "delete from youtube where id_youtube = :id_youtube";
        }

        return self::exec($sql, $video_youtube);
    }

    public static function consultarVideosSemGaleria(Model $youtube){
        $sql = "select *
                  from youtube
                 where id_youtube not in
                                  (select id_youtube
                                     from galeria_item
                                    where id_youtube is not null)
                   and ativo = :ativo";

        return self::query($sql, $youtube);
    }

    public static function consultarVideosNaGaleria(Model $youtube){
        $sql = "select y.*, gi.ordem
                  from youtube y
                  right join galeria_item gi
                  on gi.id_youtube = gi.id_youtube
                 where y.ativo = :ativo";

        return self::query($sql, $youtube);
    }

}

?>
