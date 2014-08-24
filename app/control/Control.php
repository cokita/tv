<?php
class Control {
    protected $view;
    protected $params;
    protected $js              = array();
    protected $css             = array();

    public function __construct($params, $interface = "", $objeto = null) {
        $this->params = $params;
        $this->view   = self::carregarInterface($interface);
        $this->interface = $interface;
        if ($objeto) {
            $this->carregarObjeto($objeto);
        }
    }

    protected function carregarObjeto($objeto) {
        if (is_object($objeto)) {
            $obj_vars = $objeto->get_object_vars();
            $keys     = array_keys($obj_vars);

            foreach ($keys as $key) {
                if (isset($this->params[$key]) && $this->params[$key]) {
                    $objeto->set($key, $this->params[$key]);
                }
            }
        }
    }

    protected function get_object_vars() {

        return get_object_vars($this);
    }

    public static function carregarInterface($interface) {

        $base = APP . "view";
        if (strpos($interface, "_")) {
            $componente = substr($interface, 9, strpos($interface, "_") - 9);
            if (! file_exists("$base/$interface")) {
                $base = "Componentes/$componente/view";
            }

         }else if ($interface) {
             $componente = substr($interface, 9, strlen($interface) - 9 - 5);
             if ($componente) {
                 $base = "Componentes/$componente/view";
             }
         }
         if (! file_exists("$base/$interface")) {
            $base = APP . "view";
        }

        $view = new HTML_Template_ITX($base);
        $view->loadTemplateFile($interface);
        return $view;


    }

    public function paginado(Model $objeto, $numeroRegistros = 0) {

        if (! $this->params["pg_pagina"]) {
            $objeto->set("pg_pagina", 1);
        } else {
            $objeto->set("pg_pagina", (Integer) $this->params["pg_pagina"]);
        }

        if ($numeroRegistros) {
            $objeto->set("pg_registros", $numeroRegistros);
        }
    }

    /**
     * Retorna um dos atributos protegidos da classe.
     *
     * @param String $atributo
     * @return Mixed
     */
    public function get($atributo) {
        return $this->$atributo;
    }

     public function obterInterface() {

        return $this->interface;
    }

    public function dependenciasJS() {

        return $this->js;
    }

    public function dependenciasCSS() {

        return $this->css;
    }

    public function redirect($url){
        header('Location: '. $url, true, 302);
        exit;
    }
}
