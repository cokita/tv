<?php

class ImagemDAO extends DAO {

    public static function incluir(Model $imagem) {
        $sql = "insert into imagem (caminho,
                                    nome)
                            values (:caminho,
                                    :nome)";

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

        $sql .= " order by nome";

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

    public static function consultarImagensPorVeiculo(Model $imagem) {
        $sql = "select i.id_imagem,
                       i.caminho,
                       i.nome as imagem,
                       v.titulo,
                       iv.ativo
                  from imagem i
                   inner join imagem_veiculo iv
                    on i.id_imagem = iv.id_imagem
                   inner join veiculo v
                    on v.id_veiculo = iv.id_veiculo
                where v.id_veiculo = :id_veiculo";
        return self::query($sql, $imagem);
    }

}

?>
