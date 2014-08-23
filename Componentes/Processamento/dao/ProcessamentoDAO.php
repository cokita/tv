<?php

class ProcessamentoDAO extends DAO{

    public static function incluir(Model $processamento) {
        $sql = "insert into processamento (id_transacao,
                                     status,
                                     aceite,
                                     dt_aceite)
                            values (:id_transacao,
                                    :status,
                                    :aceite,
                                    :dt_aceite)";
        
        $sucesso = self::exec($sql, $processamento);

        return $sucesso;
    }

    public static function consultar(Model $processamento) {
        $sql = "select *
                  from processamento";

        $criterio = self::criterio($processamento);

        if ($criterio) {
            $sql .= " where " . implode(" and ", $criterio);
        }

        $sql .= " order by nome";

        return self::query($sql, $processamento);
    }

    public static function alterar(Model $processamento) {
        $sql = "";

        $criterio = self::criterio($processamento, array("id_processamento"));

        if ($criterio && $processamento->get("id_processamento")) {
            $sql .= "update processamento
                        set " . implode(", ", $criterio);
                    
            $sql .= " where id_processamento = :id_processamento";
            
            return self::exec($sql, $processamento);
        }
    }

    public static function remover(Model $processamento) {
        if($processamento->get("id_processamento")){
            $sql = "delete from processamento where id_processamento = :id_processamento";
        }

        return self::exec($sql, $processamento);
    }
    
    
    public static function consultarProcessamentos(Model $processamento){
        $sql = "SELECT  u1.nome        as nome_credor,
                        u1.cpf         as cpf_credor,
                        u1.cod_pais    as cod_pais_credor,
                        u1.cod_regiao  as cod_regiao_credor,
                        u1.telefone    as telefone_credor,
                        u1.agencia     as agencia_credor,
                        u1.conta       as conta_credor,
                        u1.id_banco    as id_banco_credor,
                        b1.descricao   as banco_credor,
                        u2.nome        as nome_devedor,
                        u2.cpf         as cpf_devedor,
                        u2.cod_pais    as cod_pais_devedor,
                        u2.cod_regiao  as cod_regiao_devedor,
                        u2.telefone    as telefone_devedor,
                        u2.agencia     as agencia_devedor,
                        u2.conta       as conta_devedor,
                        u2.id_banco    as id_banco_devedor,
                        b2.descricao   as banco_devedor,
                        p.id_processamento,
                        p.status       as status_processamento,
                        p.aceite       as aceite_processamento,
                        p.dt_aceite    as dt_aceite_processamento,
                        t.id_transacao,
                        t.valor,
                        t.dt_transacao,
                        t.status       as status_transacao
                    FROM transacao t
                        inner join usuario u1
                            on u1.id_usuario = t.id_usuario_credor
                        inner join usuario u2
                            on u2.id_usuario = t.id_usuario_devedor
                        inner join processamento p
                            on p.id_transacao = t.id_transacao
                        inner join banco b1
                            on b1.id_banco = u1.id_banco
                        inner join banco b2
                            on b2.id_banco = u2.id_banco
                       where not exists (select status from processamento where id_transacao = t.id_transacao and status = 'T')
                         and p.status = 'C';";
        return self::query($sql, $processamento);
    }
    
}
?>
