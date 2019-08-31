<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 22/11/2017
 * Time: 12:37
 */

class Nota_atendimento_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_notas(){
        return $this->db
                ->get('nota_atendimento')->result_array();
    }

    public function get_observacoes($ticketid){
        return $this->db
            ->where('ticketid', $ticketid)
            ->get('tbldescricaoavaliacao')->result();
    }


}