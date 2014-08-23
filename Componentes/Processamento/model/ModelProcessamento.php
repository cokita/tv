<?php
class ModelProcessamento extends Model{
    protected $id_processamento;
    protected $id_transacao;
    protected $status;
    protected $aceite;
    protected $dt_aceite;
    
//  P:pendente
//  C:cobrada aceite
//  N:cobrada negada
//  A:aguardando nova chamada
//  T : transferido
    const STATUS_PENDENTE        = "P";
    const STATUS_COBRADA_ACEITE  = "C";
    const STATUS_COBRADA_NEGADA  = "N";
    const STATUS_AGUARDANDO      = "A";
    const STATUS_TRANSFERIDO     = "T";
  
    CONST ACEITE_SIM = "S";
    CONST ACEITE_NAO = "N";
}
?>
