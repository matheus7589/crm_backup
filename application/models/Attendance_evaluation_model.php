<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 14/12/2017
 * Time: 11:04
 */
defined('BASEPATH') or exit('No direct script access allowed');
class Attendance_evaluation_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_media_geral($where = "1=1"){
//        $fim = new \Carbon\Carbon('now');
//        $aux = new \Carbon\Carbon('now');
//        $inicio = $aux->subDays(30);
//
//        if($where == "1=1")
//            $this->db->where("date BETWEEN '" . $inicio . "' and '" . $fim . "' ");
        $atendimentos = $this->db
            ->select('nota_atendimento, ticketid')
            ->where('nota_atendimento !=', null)
            ->where($where)
            ->get('tbltickets')->result_array();
        $media = 0;
        foreach ($atendimentos as $atendimento){
            $media += $atendimento['nota_atendimento'];
        }

        if(count($atendimentos) > 0) {
            if(PAINEL == INORTE)
                return round($media / count($atendimentos),2);
            else{
                $media = $media / count($atendimentos);
                $media = round($media);
                return $this->db->where("nota",$media)->get("nota_atendimento")->row();
            }
        }

        return false;

    }

    public function get_media_atendimentos(){
        return $atendimentos = $this->db
            ->select('tbltickets.userid, company, count(tbltickets.userid) as soma', FALSE)
            ->join('tblclients', 'tblclients.userid = tbltickets.userid')
            ->select_sum('nota_atendimento')
            ->where('nota_atendimento !=', null)
            ->group_by('tbltickets.userid')
            ->get('tbltickets')->result_array();
    }


}