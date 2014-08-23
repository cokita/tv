<?php

class Imagem_VeiculoDAO extends DAO{

    public static function incluir(Model $imagem_veiculo) {
        $sql = "insert into imagem_veiculo (id_imagem,
                                            id_veiculo)
                                    values (:id_imagem,
                                            :id_veiculo)";

        $sucesso = self::exec($sql, $imagem_veiculo);
        return $sucesso;
    }

    public static function consultar(Model $imagem_veiculo) {
        $sql = "select *
                  from imagem_veiculo";

        $criterio = self::criterio($imagem_veiculo);

        if ($criterio) {
            $sql .= " where " . implode(" and ", $criterio);
        }

        $sql .= " order by ordem";

        return self::query($sql, $imagem_veiculo);
    }

    public static function alterar(Model $imagem_veiculo) {
        $sql = "";

        $criterio = self::criterio($imagem_veiculo, array("id_imagem_veiculo"));

        if ($criterio && $imagem_veiculo->get("id_imagem_veiculo")) {
            $sql .= "update imagem_veiculo
                        set " . implode(", ", $criterio);


            $sql .= " where id_imagem_veiculo = :id_imagem_veiculo";
            return self::exec($sql, $imagem_veiculo);
        }
    }
    
    public static function alterarVeiculosAtivos(Model $imagem_veiculo) {

        if ($imagem_veiculo->get("id_veiculo")) {
            $sql = "update imagem_veiculo
                        set ativo = :ativo
                        where id_veiculo = :id_veiculo";
        }
        if($imagem_veiculo->get("id_imagem")){
            $sql .= " and id_imagem = :id_imagem";

        }
        return self::exec($sql, $imagem_veiculo);
    }

    public static function remover(Model $imagem_veiculo) {
        if($imagem_veiculo->get("id_imagem_veiculo")){
            $sql = "delete from imagem_veiculo where id_imagem_veiculo = :id_imagem_veiculo";
        }

        return self::exec($sql, $imagem_veiculo);
    }

    public static function removerPorImagem(Model $imagem_veiculo) {
        if($imagem_veiculo->get("id_imagem")){
            $sql = "delete from imagem_veiculo where id_imagem = :id_imagem";
        }

        return self::exec($sql, $imagem_veiculo);
    }

    public static function removerPorVeiculo(Model $imagem_veiculo) {
        if($imagem_veiculo->get("id_veiculo")){
            $sql = "delete from imagem_veiculo where id_veiculo = :id_veiculo";
        }

        return self::exec($sql, $imagem_veiculo);
    }


    public static function consultarPorVeiculo(Model $imagem_veiculo) {
        $sql = "select i.id_imagem,
                       i.caminho,
                       i.nome,
                       i.ativo,
                       iv.id_imagem_veiculo,
                       iv.id_veiculo
                  from imagem i
                     inner join imagem_veiculo iv
                        on i.id_imagem = iv.id_imagem
                where id_veiculo = :id_veiculo";
        return self::query($sql, $imagem_veiculo);
    }

    public static function consultarImagensVeiculoDestaque(Model $imagem_veiculo){
        $sql = "select i.id_imagem,
                       i.caminho,
                       i.nome,
                       i.ativo as ativo_imagem,
                       iv.id_imagem_veiculo,
                       iv.id_veiculo,
                       iv.ordem,
                       iv.ativo,
                       iv.destaque,
                       (select count(*) as qtd from imagem_veiculo where id_veiculo = :id_veiculo) as qtd_fotos
                from imagem i
                inner join imagem_veiculo iv
                on i.id_imagem = iv.id_imagem
                where iv.id_veiculo = :id_veiculo
                  and iv.ativo = 1
                ORDER BY RAND()
                limit 1";
        
        return self::query($sql, $imagem_veiculo);
    }
}
?>
