<?php
/**
 * Created by PhpStorm.
 * User: matheus
 * Date: 13/07/2018
 * Time: 15:15
 */

class Dev_report_model extends CRM_Model
{
    const DEV_ATENDIDO = 32;

    public function __construct()
    {
        parent::__construct();
    }

    public function media_espera($from, $to, $status_from, $status_to, $_ticketid = false, $_media = true){
        $this->load->model('attend_average_time_model');
//        $this->load->model('tickets_model');
//        $tickets = $this->attend_average_time_model->get_tickets('', $from, $to);



        $tickets = [];
        if($_ticketid) {
//            $tickets = $this->attend_average_time_model->get_tickets('', $from, $to, $_ticketid);
            $tickets = $this->get_tickets_dev_report($_ticketid);
        }else {
            $where = array(
                "tblticketstimestatus.datetime >=" => $from,
                "tblticketstimestatus.datetime <=" => $to,
                "tblticketstimestatus.statusid =" => $this::DEV_ATENDIDO
            );
            $this->load->model('tickets_model');
            $list_tickets = $this->get_list_tickets($where);
//            print_r(($list_tickets));
            foreach ($list_tickets as $key => $list){

                foreach ($this->get_tickets_dev_report($list['ticketid']) as $inner_ticket){
                    $tickets[] = $inner_ticket;
                }
            }
        }

//        print_r(($tickets));

        $ticketid = 0;
        $had_espera = 0;
        $sum_diff = 0;
        $cont = 0;
        $ids_selecionados = [];
        $ids = '';
        $ids_cont = '';

        /** auxiliares */
        $date_from = '';
        $date_to = '';

        if($status_from == 'primeiro_dev'){ //Para verificação de primeiro DEV
            $ticketid = $tickets[0]['ticketid'];
        }

        foreach ($tickets as $key => $value){

            if($status_from == 'primeiro_dev'){
                $status_from = $this->get_ticket_first_dev($ticketid)['statusid']; /** Gambirinha pra pegar o primeiro status DEV */
            }


            if($value['statusid'] == $status_from){
//                var_dump(array($value['statusid'], $status_from));
                if($had_espera == 0){
//                    var_dump(array($value));
                    $ids .= $value['ticketid'] . ' - ';
                    $ticketid = $value['ticketid'];
                    $date_from = \Carbon\Carbon::parse($value['datetime']);
                    $had_espera = 1;
                }
            }

//            var_dump(array($had_espera, $value, 'salved: ' . $ticketid, 'current ' . $value['ticketid'], 'status from: ' . $status_from, 'status_current: ' . $value['statusid'], 'status to: ' . $status_to));
            if($had_espera == 1){
//                if($value['statusid'] == $status_to){
                if(in_array($value['statusid'], $status_to)){
                    if ($value['ticketid'] == $ticketid) {
                        $date_to = \Carbon\Carbon::parse($value['datetime']);
                        $diff = $date_to->diffInMinutes(\Carbon\Carbon::parse($date_from)); // essa bagaça é mais rapido mas n ignora fim de semana

                        $period = new DatePeriod($date_from, new DateInterval('P1D'), $date_to);
                        $days = 0;
//                         Armazenando como array, ai posso add mais feriados
//                        $holidays = array('2012-09-07');
                        $holidays = array();

                        foreach($period as $dt) {
                            $curr = $dt->format('D');

                            // subtrai se for domingo
//                            if ($curr == 'Sat' || $curr == 'Sun') {
                            if ($curr == 'Sun') {
                                $days++;
                            }

                            // tira o feriado
                            elseif (in_array($dt->format('Y-m-d'), $holidays)) {
                                $days++;
                            }
                        }

                        if($days > 0){
                            $days = $days * 24 * 60;
                            $diff -= $days;
                        }

//                        $diff = Carbon\Carbon::parse($date_to)->diffFiltered(Carbon\CarbonInterval::minutes(), function (\Carbon\Carbon $date) { // essa bagaça ignora fim de semana, mas é bem mais lento
//                            return !$date->isSunday();
//                        }, Carbon\Carbon::parse($date_from));
//                        var_dump($diff, $value['ticketid']);
                        $sum_diff += $diff;
                        $had_espera = 0;
                        if($diff > 0) { // so adiciona a contagem se for maior que 0
                            $cont++;
                            $ids_cont .= $value['ticketid'] . ' - ';
//                            array_push($ids_selecionados, $value['ticketid']);
                            $ids_selecionados[] = $value['ticketid'];
                        }
                    }else{
                        $had_espera = 0;
                    }
                } else {
                    // TODO altas satanagens
                    if ($value['ticketid'] != $ticketid) {
                        $had_espera = 0;
                    }
                }
            }

        }

        $media = ($cont > 0) ? $sum_diff/$cont : $sum_diff;
        $ids_selecionados = array_unique($ids_selecionados);
//        print_r(array('media: ' . $media, 'soma total: ' . $sum_diff, 'contagem: ' . $cont, 'ticketid: ' . $_ticketid,
//            'status_from: ' . $status_from, 'tickets: ' . $ids, 'tickets_cont: ' . $ids_cont, 'ids_unique: ' => $ids_selecionados));
        $media = $media/60;
        $hora = explode('.', number_format($media, 2))[0];
        $minute = explode('.', number_format($media, 2))[1];

        if($_ticketid == false && $_media == true){
            if(count($ids_selecionados) > 0)
                $total_minutos = $sum_diff/count($ids_selecionados);
            else
                $total_minutos = $sum_diff;
        }else if($_ticketid != false && $cont > 0 && $_media == true){
            $total_minutos = $sum_diff/$cont;
        }else{
            $total_minutos = $sum_diff;
        }

        return array(
            'mediahora' => $hora,
            'minutos' => explode('.', number_format(((intval($minute)/100)*60), 2))[0], //formata os minutos
            'total_minutos' => $total_minutos
        );
    }

    public function get_tickets_dev_report($ticketid = '', $where = array())
    {
        if(!empty($ticketid)) {
            $this->db->where('ticketid', $ticketid);
        }

        return $this->db
//            ->group_by('ticketid, id')
            ->order_by('datetime ASC')
            ->get('tblticketstimestatus')->result_array();
    }

    public function get_list_tickets($where = array()){
        $this->db->select('DISTINCT(ticketid)');
        $this->db->where($where);
        $this->db->group_by('ticketid, id');
        return $this->db->get('tblticketstimestatus')->result_array();
    }

    public function get_ticket_first_dev($ticketid){
        return $this->db
            ->like('tblticketstatus.name', 'DEV')
            ->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tblticketstimestatus.statusid')
            ->where('ticketid', $ticketid)
            ->order_by('datetime ASC')
            ->get('tblticketstimestatus')->first_row('array');
    }




}