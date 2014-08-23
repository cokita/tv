<?php
class ModelTransacao extends Model{
    protected $id_transacao;
    protected $id_usuario_credor;
    protected $id_usuario_devedor;
    protected $valor;
    protected $dt_transacao;
    protected $status;
    
    
    //Constantes para valores fixos.
    const STATUS_PENDENTE = "P";
  
}
?>
