<?php
class Conexao {
    /**
     * Contém a conexão com o banco de dados.
     * @static
     */
    static private $conexao;
    /**
     * Contém o e-mail do usuário autenticado. O e-mail substitui o nome do
     * usuário.
     * @static
     */
    static private $login;
    /**
     * Contém a senha do usuário autenticado. A senha é gravada através do
     * método md5, disponível no banco de dados.
     * @static
     */
    static private $senha;
    static private $novoLogin;

    static function obterConexao() {
        $dsn     = DBHOST;
        $usuario = DBUSUARIO;
        $senha   = DBSENHA;
        $esquema = DBNOME;

        self::$conexao = mysql_connect($dsn, $usuario, $senha) or die(mysql_error());
        $conn = mysql_select_db($esquema,self::$conexao) or die(mysql_error());

        return self::$conexao;
    }

    public static function autenticarAdmin() {
        $sql = "select u.id_usuario_administrador,
                       u.nome,
                       u.email,
                       u.senha
                  from usuario_administrador u
                 where (:email in (u.email) or :login in (u.login))
                   and u.senha = :senha
                   and u.ativo = 1";
        return self::verificarCredenciais($sql);
    }

    private static function verificarCredenciais($sql) {
        $sucesso         = false;
        self::$novoLogin = false;

        if (isset($_POST["user_login"])) {
            self::$login = (String) $_POST["user_login"];
        } else if (isset($_SESSION["user_login"])) {
            self::$login = (String) $_SESSION["user_login"];
        } else {
            self::$login = "";
        }
        
        if (isset($_POST["senha_login"])) {
            self::$senha = (String) $_POST["senha_login"];
        } else if (isset($_SESSION["senha_login"])) {
            self::$senha = (String) $_SESSION["senha_login"];
        } else {
            self::$senha = "";
        }

        if (self::$login and self::$senha) {
            self::$conexao = self::obterConexao();

            if (self::$conexao) {
                $parametros    = array("email" => self::$login,
                                       "login" => self::$login,
                                       "senha" => md5(self::$senha));
                $model_usuario_administrador = new ModelUsuario_Administrador($parametros);
                $usuario_administrador       = DAO::query($sql, $model_usuario_administrador);
                if ($usuario_administrador) {
                    $usuario_administrador = $usuario_administrador[0];
                    $sucesso                 = true;
                    $_SESSION["nome"]        = $usuario_administrador->get("nome");
                    $_SESSION["user_login"] = $usuario_administrador->get("email");
                    $_SESSION["senha_login"] = self::$senha;
                    $_SESSION["id_usuario_administrador"]  = $usuario_administrador->get("id_usuario_administrador");
                    self::$novoLogin = (  ((String) isset($_REQUEST["user_login"]) || (String) isset($_REQUEST["url"]))
                                        && ((String) isset($_REQUEST["senha_login"]))) ? "1" : "0";

                } else {
                    $_SESSION["erro_login"] = "Usuário e senha inválidos!";
                }
            } else {
                $_SESSION["erro_login"] = DB::errorMessage(self::$conexao);
            }
        }
        return $sucesso;
    }
}
?>
