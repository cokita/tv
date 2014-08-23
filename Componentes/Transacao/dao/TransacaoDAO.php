<?php

class TransacaoDAO extends DAO{

    public static function incluir(Model $transacao) {
        $sql = "insert into transacao (nome,
                                     email,
                                     senha,
                                     login,
                                     ativo)
                            values (:nome,
                                    :email,
                                    :senha,
                                    :login,
                                    :ativo)";
        
        $sucesso = self::exec($sql, $transacao);

        return $sucesso;
    }

    public static function consultar(Model $transacao) {
        $sql = "select *
                  from transacao";

        $criterio = self::criterio($transacao);

        if ($criterio) {
            $sql .= " where " . implode(" and ", $criterio);
        }

        $sql .= " order by nome";

        return self::query($sql, $transacao);
    }

    public static function alterar(Model $transacao) {
        $sql = "";

        $criterio = self::criterio($transacao, array("id_transacao"));

        if ($criterio && $transacao->get("id_transacao")) {
            $sql .= "update transacao
                        set " . implode(", ", $criterio);
                    
            $sql .= " where id_transacao = :id_transacao";
            
            return self::exec($sql, $transacao);
        }
    }

    public static function remover(Model $transacao) {
        if($transacao->get("id_transacao")){
            $sql = "delete from transacao where id_transacao = :id_transacao";
        }

        return self::exec($sql, $transacao);
    }
    
    public static function consultarTransacoesUsuario(Model $transacao){
        
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
                        t.status       as status_transacao,
                        CASE :id_usuario
                        WHEN (select id_usuario_credor from transacao where id_usuario_credor = u1.id_usuario) THEN 'D'
                        WHEN (select id_usuario_devedor from transacao where id_usuario_devedor = u2.id_usuario) THEN 'C'
                        END as tipo_transacao
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
                       where p.status = 'T'
                        and :id_usuario in (id_usuario_credor, id_usuario_devedor);";
        return self::query($sql, $transacao);
    }
    
}
?>
