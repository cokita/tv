<?php

class Usuario_AdministradorDAO extends DAO{

    public static function incluir(Model $usuario_administrador) {
        $sql = "insert into usuario_administrador (nome,
                                     email,
                                     senha,
                                     login,
                                     ativo)
                            values (:nome,
                                    :email,
                                    :senha,
                                    :login,
                                    :ativo)";
        
        $sucesso = self::exec($sql, $usuario_administrador);

        return $sucesso;
    }

    public static function consultar(Model $usuario_administrador) {
        $sql = "select *
                  from usuario_administrador";

        $criterio = self::criterio($usuario_administrador);

        if ($criterio) {
            $sql .= " where " . implode(" and ", $criterio);
        }

        $sql .= " order by nome";

        return self::query($sql, $usuario_administrador);
    }

    public static function alterar(Model $usuario_administrador) {
        $sql = "";

        $criterio = self::criterio($usuario_administrador, array("id_usuario_administrador", "senha", "alterar_senha"));

        if ($criterio && $usuario_administrador->get("id_usuario_administrador")) {
            $sql .= "update usuario_administrador
                        set " . implode(", ", $criterio);
                    

            if($usuario_administrador->get("alterar_senha") == true){
                $sql .= " , senha = :senha";
            }
            
            $sql .= " where id_usuario_administrador = :id_usuario_administrador";
            
            return self::exec($sql, $usuario_administrador);
        }
    }

    public static function remover(Model $usuario_administrador) {
        if($usuario_administrador->get("id_usuario_administrador")){
            $sql = "delete from usuario_administrador where id_usuario_administrador = :id_usuario_administrador";
        }

        return self::exec($sql, $usuario_administrador);
    }

    static function consultarLoginSenha(Model $usuario_administrador) {
        $sql = "select u.id_usuario_administrador
                  from usuario_administrador u
                 where (:email in (u.email) or :login in (u.login))
                   and u.senha = :senha
                   and ativo   = 1";

        $usuario_administrador->set("email", $usuario_administrador->get("email"));
        $usuario_administrador->set("senha", md5($usuario_administrador->get("senha")));
        return self::query($sql, $usuario_administrador);
    }

    public static function consultarEmailUsuario(Model $usuario_administrador){
        $sql = "select * from usuario_administrador where email = :email";

        if($usuario_administrador->get("id_usuario_administrador")){
            $sql .= " and id_usuario_administrador <> :id_usuario_administrador";
        }
        return self::query($sql, $usuario_administrador);
    }
    
    public static function consultarLoginUsuario(Model $usuario_administrador){
        $sql = "select * from usuario_administrador where login = :login";

        if($usuario_administrador->get("id_usuario_administrador")){
            $sql .= " and id_usuario_administrador <> :id_usuario_administrador";
        }

        return self::query($sql, $usuario_administrador);
    }

    public static function ultimoId(){
        $sql = "select max(id_usuario_administrador) as id_usuario_administrador from usuario_administrador";
        return self::query($sql, new ModelUsuario());
    }
}
?>
