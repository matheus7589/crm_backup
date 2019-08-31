<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 14/12/2017
 * Time: 16:38
 */
defined('BASEPATH') or exit('No direct script access allowed');
class Technician_evaluation_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function featured_support($where = "1=1"){
        $not = array(0);
        if(PAINEL == INORTE)
            $not = array(0, 54, 4);
        $atendimentos = $this->db
            ->select('count(tbltickets.assigned), assigned, firstname')
            ->join('tblstaff', 'tblstaff.staffid = tbltickets.assigned')
            ->select_avg('nota_atendimento')
            ->where('nota_atendimento !=', null)
            ->where($where)
            ->where_not_in('assigned', $not)
            ->group_by('tbltickets.assigned')
            ->get('tbltickets')->result_array();

        if($atendimentos != false) {
            $max = $atendimentos[0];
            foreach ($atendimentos as $atendimento) {
                if ($atendimento['nota_atendimento'] > $max['nota_atendimento']) {
                    $max = $atendimento;
                }
            }

            return $max;
        }
        return false;
    }


}