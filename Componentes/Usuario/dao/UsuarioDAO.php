<?php

class UsuarioDAO extends DAO{

    public static function incluir(Model $usuario) {
        $sql = "insert into usuario (nome,
                                     cpf,
                                     cod_pais,
                                     cod_regiao,
                                     telefone,
                                     senha,
                                     agencia,
                                     conta,
                                     id_banco)
                            values (:nome,
                                    :cpf,
                                    :cod_pais,
                                    :cod_regiao,
                                    :telefone,
                                    :senha,
                                    :agencia,
                                    :conta,
                                    :id_banco)";

        $sucesso = self::exec($sql, $usuario);

        return $sucesso;
    }

    public static function consultar(Model $usuario) {
        $sql = "select  id_usuario,
                        nome,
                        cpf,
                        cod_pais,
                        cod_regiao,
                        telefone,
                        status,
                        ligando,
                        senha,
                        agencia,
                        conta,
                        id_banco
                  from usuario
                  where removido = 0";

        $criterio = self::criterio($usuario);

        if ($criterio) {
            $sql .= " and " . implode(" and ", $criterio);
        }

        $sql .= " order by nome";
        return self::query($sql, $usuario);
    }

    public static function alterar(Model $usuario) {
        $sql = "";

        $criterio = self::criterio($usuario, array("id_usuario"));

        if ($criterio && $usuario->get("id_usuario")) {
            $sql .= "update usuario
                        set " . implode(", ", $criterio);
                    

            $sql .= " where id_usuario = :id_usuario";
            
            return self::exec($sql, $usuario);
        }
    }

    public static function remover(Model $usuario) {
        if($usuario->get("id_usuario")){
            $sql = "delete from usuario where id_usuario = :id_usuario";
        }

        return self::exec($sql, $usuario);
    }


    public static function consultarCPFUsuario(Model $usuario){
        $sql = "select * from usuario where cpf = ':cpf'";

        if($usuario->get("id_usuario")){
            $sql .= " and id_usuario <> :id_usuario";
        }
        return self::query($sql, $usuario);
    }
    
    public static function ultimoId(){
        $sql = "select max(id_usuario) as id_usuario from usuario";
        return self::query($sql, new ModelUsuario());
    }
    
    public static function removerLogico(Model $usuario) {

        if ($usuario->get("id_usuario")) {
            $sql .= "update usuario
                        set removido = 1
                        where id_usuario = :id_usuario";
                    

            return self::exec($sql, $usuario);
        }
    }
    
    public static function consultarDadosUsuario(Model $usuario){
        $sql = "select  u.id_usuario,
                        u.nome,
                        u.cpf,
                        u.cod_pais,
                        u.cod_regiao,
                        u.telefone,
                        u.agencia,
                        u.conta,
                        b.id_banco,
                        b.codigo,
                        b.descricao as banco
                    from usuario u
                        inner join banco b
                            on u.id_banco = b.id_banco ";
        
        if($usuario->get("id_usuario")){
            $sql .= " where u.id_usuario = :id_usuario";
        }
        
        return self::query($sql, $usuario);
        
    }
}
?>
