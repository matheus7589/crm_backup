<?php
/**
 * Created by PhpStorm.
 * User: dsisconeto
 * Date: 23/09/17
 * Time: 09:21
 */

use Carbon\Carbon;

class Painel_atedimento_model extends CRM_Model
{
    /**
     * Os status que  estão entre parênteses são os status correntes no banco de dados.
     * Os valores atribuídos à eles são referentes à ordenação por nós aferida.
     *
     */
    const PRIORITY = [
        4 => 1,    //URGENTE(4) = 1
        3 => 2,    //ALTO(3) = 2
        2 => 3,    //MÉDIO(1) = 4
        1 => 4,    //BAIXO(2) = 3
        5 => 5,    //ATUALIZAÇÃO(5) = 5
        6 => 6,    //AGENDADO(6) = 6
        8 => 7,    //PENDENTE(8)
    ];

    const ATEND_EXTERNO = 27;
    const ATEND_INTERNO = 2;
    const SUP_PENDENTE = 18;
    const SUP_IMPLANTACAO = 30;

    public function ticketsEspera()
    {

        $aux = [];
        $result = [];
        $now = Carbon::now('America/Sao_Paulo');

        $_where = array(
            'status' => 1,
            'tbltickets.partner_id' => get_staff_partner_id(),
        );

        $resultDb = $this->db->
        select(['company', "priority", "ticketid", "date_espera", "is_externo", "subject", "date", "assigned", "lastreply"])
            ->from("tbltickets")
            ->join('tblclients', 'tblclients.userid = tbltickets.userid', "INNER")
            ->join("horaatendimento", "horaatendimento.idticket = tbltickets.ticketid", "INNER")
            ->where($_where)
            ->order_by("date_espera ASC")
            ->get()->result();

        $ticket_waiting_alert_time = intval(get_option("ticket_waiting_alert_time"));
        $ticket_waiting_limit_time = intval(get_option("ticket_waiting_limit_time"));

        foreach ($resultDb as $key => $obj) {

            if (!empty($obj->lastreply)) {
                $obj->lastreply = Carbon::createFromFormat('Y-m-d H:i:s', $obj->lastreply)->format('d/m/Y H:i:s');
            } else {
                $obj->lastreply = "Não possui respostas";
            }

            if(!empty($obj->assigned)){
                $obj->assigned = $this->db->select('firstname')->where('staffid', $obj->assigned)->get('tblstaff')->row();
            }

            if(strlen($obj->company) > 23){
                $obj->company = substr($obj->company, 0, 23);
            }

            $aux_total_date = $now->diff(Carbon::parse($obj->date));
            $obj->old_date = $obj->date;


            $dia_in_esp = Carbon::parse($obj->date_espera);
            $interval = $now->diff($dia_in_esp);
            $format = "%H:%I:%S";
            if ($interval->d !== 0) {
                $format = "%d dia(s) " . $format;
            }
            if($interval->m !==0){
                $format = ' %m mes(es) ' . $format;
            }
            if($interval->y !==0){
                $format = ' %y ano(s) ' . $format;
            }

            $obj->date_espera = $interval->format($format);
            $obj->date = $aux_total_date->format($this->get_date_format($aux_total_date));
            $minutos_espera = $dia_in_esp->diffInMinutes()-$now->diffInMinutes();
            $obj->priority = self::PRIORITY[$obj->priority];
//            $result[$key] = $obj;
            $obj->limit = false;
            $obj->alerta = false;

            if($minutos_espera >= $ticket_waiting_alert_time) {
                if($minutos_espera >= $ticket_waiting_limit_time)
                    $obj->limit = true;
                else
                    $obj->alerta = true;
            }

            if(PAINEL == INORTE){
                if($obj->priority > 4){
                    $obj->limit = false;
                    $obj->alerta = false;
                }
            }

            if(PAINEL == INORTE){
                if($obj->priority < 5){
                    $aux[] = $obj;
                    unset($obj);
                }else{
                    $result[$key] = $obj;
                }
            }else{
                $result[$key] = $obj;
            }
        
        }

//        usort($result, array($this, "compare_priority"));

        if(PAINEL == INORTE){
            $priority = array();
            $date = array();
            // Obtem as colunas em forma de lista simples
            foreach ($result as $key => $row) {
                $priority[$key]  = $row->priority;
                $date[$key] = $row->old_date;
            }

            // Ordena os dados por priority crescente, data crescente. Isso me retorna os tickets de maior prioridade
            // e de mais tempo de espera simultaneamente. Magicamente essa func ordena o ultimo array $tickets com base nas
            // 2 listas informadas kkkkk
            array_multisort($priority, SORT_ASC, $date, SORT_ASC, $result);
//            array_multisort($date, SORT_ASC, $result);

        }

        if(PAINEL == INORTE){
            usort($aux, array($this, "compare_ticketid"));

            $result = array_merge($aux, $result);
        }


        return $result;
    }

    public function where_push_query($status){

        $_where = array(
            'status' => $status,
            'tbltickets.partner_id' => get_staff_partner_id(),
        );
        return array(
            '$_where' => $_where
        );
    }


    public function tikectsAtedimento($status)
    {

        $result = [];
        $now = Carbon::now('America/Sao_Paulo');

        $_where = $this->where_push_query($status)['$_where'];
        $ticket_waiting_alert_time_attendance = intval(get_option("ticket_waiting_alert_time_attendance"));
        $ticket_waiting_limit_time_attendance = intval(get_option("ticket_waiting_limit_time_attendance"));

        $result = $this->db->
        select(['company', 'firstname', "priority", "ticketid", "date_atendimento", "is_externo", "subject", "status", "lastreply"])
            ->from("tbltickets")
            ->join('tblstaff', 'tblstaff.staffid = tbltickets.assigned', "INNER")
            ->join('tblclients', 'tblclients.userid = tbltickets.userid', "INNER")
            ->join("horaatendimento", "horaatendimento.idticket = tbltickets.ticketid", "INNER")
            ->where_in('status', $_where['status'])
            ->where('tbltickets.partner_id', $_where['tbltickets.partner_id'])
            ->get()->result();

        foreach ($result as $key => $obj) {

            if (!empty($obj->lastreply)) {
                $obj->lastreply = Carbon::createFromFormat('Y-m-d H:i:s', $obj->lastreply)->format('d/m/Y H:i:s');
            } else {
                $obj->lastreply = "Não possui respostas";
            }

            $obj->old_date = $obj->date_atendimento;

            $dia_in_esp = Carbon::parse($obj->date_atendimento);
            $interval = $now->diff(Carbon::parse($obj->date_atendimento));
            $format = [];
            $format = "%H:%I:%S";
            if ($interval->d !== 0) {
                $format = "%d dia(s) " . $format;
            }
            if($interval->m !==0){
                $format = ' %m mes(es) ' . $format;
            }
            if($interval->y !==0){
                $format = ' %y ano(s) ' . $format;
            }
            $obj->time = $interval->format($format);
            $minutos_espera = $dia_in_esp->diffInMinutes()-$now->diffInMinutes();
            if($minutos_espera >= $ticket_waiting_alert_time_attendance) {
                if($minutos_espera >= $ticket_waiting_limit_time_attendance)
                    $obj->limit_att = true;
                else
                    $obj->alerta_att = true;
            }

            if(PAINEL == INORTE){
                if($status == 27){
                    $obj->limit_att = false;
                    $obj->alerta_att = false;
                }
            }

            //$obj->time = $now->diff(Carbon::parse($obj->date_atendimento))->format("%d dias %H:%I:%S");
            unset($obj->date_atendimento);
            $obj->priority = self::PRIORITY[$obj->priority];
            //$obj->company = substr($obj->company, 0, 30);
            $result[$key] = $obj;
        }
//        usort($result, array($this, "compare_priority"));

        if(PAINEL == INORTE){
            $priority = array();
            $date = array();
            // Obtem as colunas em forma de lista simples
            foreach ($result as $key => $row) {
                $priority[$key]  = $row->priority;
                $date[$key] = $row->old_date;
            }
//            print_r($priority);

            // Ordena os dados por priority crescente, data crescente. Isso me retorna os tickets de maior prioridade
            // e de mais tempo de espera simultaneamente. Magicamente essa func ordena o ultimo array $tickets com base nas
            // 2 listas informadas kkkkk
            if(is_array($priority) && is_array($date)) {
                array_multisort($priority, SORT_ASC, $date, SORT_ASC, $result);
//            array_multisort($date, SORT_ASC, $result);
            }

        }

        return $result;
    }

    public function pending_sup($status)
    {
        $result = [];
        $now = Carbon::now('America/Sao_Paulo');

        $_where = $this->where_push_query($status)['$_where'];


        $result = $this->db->
        select(['company', 'firstname', "priority", "ticketid", "lastreply", "is_externo", "subject"])
            ->from("tbltickets")
            ->join('tblstaff', 'tblstaff.staffid = tbltickets.assigned', "INNER")
            ->join('tblclients', 'tblclients.userid = tbltickets.userid', "INNER")
            ->where($_where)
            ->get()->result();

        foreach ($result as $key => $obj) {

            if (!empty($obj->lastreply)) {
                $obj->lastreplyAux = Carbon::createFromFormat('Y-m-d H:i:s', $obj->lastreply)->format('d/m/Y H:i:s');
            } else {
                $obj->lastreplyAux = "Não possui respostas";
            }

            $obj->old_date = $obj->lastreply;

            $interval = $now->diff(Carbon::parse($obj->lastreply));
            $format = "%H:%I:%S";
            if ($interval->d !== 0) {
                $format = "%d dia(s) " . $format;
            }
            if($interval->m !==0){
                $format = ' %m mes(es) ' . $format;
            }
            if($interval->y !==0){
                $format = ' %y ano(s) ' . $format;
            }
            $obj->time = $interval->format($format);
            unset($obj->lastreply);
            $obj->priority = self::PRIORITY[$obj->priority];
            //$obj->company = substr($obj->company, 0, 30);
            $result[$key] = $obj;
        }

        if(PAINEL == INORTE){
            // Obtem as colunas em forma de lista simples
            foreach ($result as $key => $row) {
                $priority[$key]  = $row->priority;
                $date[$key] = $row->old_date;
            }

            // Ordena os dados por priority crescente, data crescente. Isso me retorna os tickets de maior prioridade
            // e de mais tempo de espera simultaneamente. Magicamente essa func ordena o ultimo array $tickets com base nas
            // 2 listas informadas kkkkk
            array_multisort($priority, SORT_ASC, $date, SORT_ASC, $result);
//            array_multisort($date, SORT_ASC, $result);

        }

//        usort($result, array($this, "compare_priority"));
        return $result;
    }

    public function staffDisponiveis()
    {
        $status = [
            0 => self::ATEND_INTERNO,
            1 => self::ATEND_EXTERNO
        ];

        $staffs = $this->db->select(["assigned"])
            ->from('tbltickets')
            ->where_in('status', $status)
            ->order_by('admin ASC')
            ->get()->result_array();
        $staff_ids = [];

        $this->db->where("tblstaff.partner_id", get_staff_partner_id());

        foreach ($staffs as $id)
            $staff_ids[] = $id["assigned"];


        $this->db->select(["firstname", "tblstaff.staffid"])
            ->from("tblstaff")
            ->distinct("tblstaff.staffid")
            ->join('tblstaffonline', 'tblstaffonline.staffid = tblstaff.staffid', "INNER");

        if ($staff_ids)
            $this->db->where_not_in("tblstaff.staffid", $staff_ids);

        return $this->db->where("active", 1)
            ->where("role", 4)
            ->where("is_online", 1)
            ->get()->result();


    }

    function compare_ticketid($a, $b)
    {
        return $a->ticketid - $b->ticketid;
    }

    function compare_priority($a, $b)
    {
        return $a->priority - $b->priority;
    }

    function get_date_format($interval){
        $format = "%H:%I:%S";
        if ($interval->d !== 0) {
            $format = "%d dia(s) " . $format;
        }
        if($interval->m !==0){
            $format = ' %m mes(es) ' . $format;
        }
        if($interval->y !==0){
            $format = ' %y ano(s) ' . $format;
        }

        return $format;
    }

}