<?php
/**
 * Created by PhpStorm.
 * User: matheus.machado
 * Date: 17/05/2018
 * Time: 08:51
 */

use Carbon\Carbon;

class Painel_desenvolvimento_model extends CRM_Model
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
        8 => 8,    //PENDENTE
    ];


    const DEV_ESPERA = 26;
    const DEV_NAO_INICIADO = 1;
    const DEV_TESTE = 24;
    const DEV_ATENDIMENTO = 33;
    const DEV_REANALISE = 34;

    const TASK_STATUS_PROGRESSO = 4;
    const TASK_STATUS_TESTE = 3;
    const TASK_STATUS_REANALISE = 2;
    const TASK_STATUS_NAO_INICIADO = 1;

    const ATEND_EXTERNO = 27;
    const ATEND_INTERNO = 2;
    const SUP_PENDENTE = 18;
    const SUP_IMPLANTACAO = 30;

    const TASK_STATUS = [
        self::DEV_ATENDIMENTO => self::TASK_STATUS_PROGRESSO,
        self::DEV_TESTE => self::TASK_STATUS_TESTE,
        self::DEV_REANALISE => self::TASK_STATUS_REANALISE,
        self::DEV_NAO_INICIADO => self::TASK_STATUS_NAO_INICIADO
    ];

    public function naoIniciado()
    {

        $aux = [];
        $urgentes = [];
        $agendados = [];
        $result = [];
        $now = Carbon::now('America/Sao_Paulo');

        $_where = array(
            'status' => self::DEV_ESPERA,
//            'tbltickets.partner_id' => get_staff_partner_id(),
        );

        $resultDb = $this->db->
        select(['company', "priority", "ticketid", "is_externo", "subject", "date", "status"])
            ->from("tbltickets")
            ->join('tblclients', 'tblclients.userid = tbltickets.userid', "INNER")
            ->where($_where)
            ->order_by("priority DESC")
            ->get()->result();

        $task_waiting_alert_time = intval(get_option("task_waiting_alert_time"));
        $task_waiting_limit_time = intval(get_option("task_waiting_limit_time"));

        $this->load->model("tickets_model");
        foreach ($resultDb as $key => $obj) {

            /** Gambirinha marots */
            $date_auxiliar = $this->tickets_model->get_ticket_timestatus($obj->status, $obj->ticketid);
            if($date_auxiliar != null){
                $obj->date_espera = $date_auxiliar->datetime;
                $espera_parcial = Carbon::parse($obj->date_espera);


                $obj->date_espera = $date_auxiliar->datetime;
                $weekend_espera_parcial = $this->filtra_fim_de_semana($espera_parcial, Carbon::now());
                $weekend_espera_parcial = $weekend_espera_parcial * 24 * 60 * 60;

                $interval_parcial = Carbon::now()->diffInSeconds($espera_parcial);
                $interval_parcial -= $weekend_espera_parcial;
                $interval_parcial = secondsToHumanReadable($interval_parcial);

                $obj->date_espera = $interval_parcial;
            }else
                $obj->date_espera = "Sem Alteração";

            /** Gambirinha marots */

            $dia_in_esp = Carbon::parse($obj->date); //Total em espera

            /** @var  time */

            $dt2 = Carbon::now();

            $weekend_espera_total = $this->filtra_fim_de_semana($dia_in_esp, $dt2); /** retorno em minutos */
            $weekend_espera_total = $weekend_espera_total * 24 * 60 * 60; /** Convertendo para segundos */
            /** @var  time */
            $interval = Carbon::now()->diffInSeconds($dia_in_esp);
            $interval -= $weekend_espera_total;
            $interval = secondsToHumanReadable($interval);
//            $interval = biss_hours($dia_in_esp, Carbon::now());
            $obj->old_date = $obj->date;
            $obj->date = $interval;

            /** Gambirinha marots desenvolvedores*/
            $staff_auxiliar = $this->tasks_model->get_task_assignees_by_ticketid_and_status($obj->ticketid, self::TASK_STATUS[self::TASK_STATUS_NAO_INICIADO]);
            if($staff_auxiliar != null){
                $obj->developers = $staff_auxiliar;
                foreach ($obj->developers as $chave => $value){
                    $obj->developers[$chave]['profile_pic'] = staff_profile_image($value['assigneeid'], array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $value['firstname'],
                    ));
                }
            }
            /** Gambirinha marots desenvolvedores*/

            $minutos_espera = $dia_in_esp->diffInMinutes()-$now->diffInMinutes();
            $obj->priority = self::PRIORITY[$obj->priority]; // Configura as prioridades das solicitacoes
//            $result[$key] = $obj;
            $obj->limit = false;
            $obj->alerta = false;

            if($minutos_espera >= $task_waiting_alert_time) {
                if($minutos_espera >= $task_waiting_limit_time)
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
                if($obj->priority == 1){
//                    $aux[] = $obj;
                    $urgentes[] = $obj;
                    unset($obj);
                } else if($obj->priority == 6){
                    $agendados[] = $obj;
                    unset($obj);
                } else{
                    $result[$key] = $obj;
                }
            }else{
                $result[$key] = $obj;
            }

//            $result[$key] = $obj;

        }

        if(PAINEL != INORTE)
            usort($result, array($this, "compare_priority"));


        if(PAINEL == INORTE){
            // Obtem as colunas em forma de lista simples
            foreach ($result as $key => $row) {
//                $priority[$key]  = $row->priority;
                $date[$key] = $row->old_date;
            }

            // Ordena os dados por priority crescente, data crescente. Isso me retorna os tickets de maior prioridade
            // e de mais tempo de espera simultaneamente. Magicamente essa func ordena o ultimo array $tickets com base nas
            // 2 listas informadas kkkkk
//            array_multisort($priority, SORT_ASC, $date, SORT_ASC, $result);
            array_multisort($date, SORT_ASC, $result);

            usort($urgentes, array($this, "compare_total_espera"));
            usort($agendados, array($this, "compare_total_espera"));
            $result = array_merge($urgentes, $result);
            $result = array_merge($result, $agendados);
        }

//        dd($result);

        return $result;
    }

    public function where_push_query($status){

        $_where = array(
            'tbltickets.status' => $status,
//            'tbltickets.partner_id' => get_staff_partner_id(),
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
        select(['company', 'firstname', "priority", "ticketid", "date_atendimento", "is_externo", "subject"])
            ->from("tbltickets")
            ->join('tblstaff', 'tblstaff.staffid = tbltickets.assigned', "INNER")
            ->join('tblclients', 'tblclients.userid = tbltickets.userid', "INNER")
            ->join("horaatendimento", "horaatendimento.idticket = tbltickets.ticketid", "INNER")
            ->where($_where)
            ->get()->result();

        foreach ($result as $key => $obj) {
            $dia_in_esp = Carbon::parse($obj->date_atendimento);
            $interval = $now->diff(Carbon::parse($obj->date_atendimento));
            $format = [];
            if ($interval->d !== 0) {
                $format = "%d dias %H:%I:%S";
            } else {
                $format = "%H:%I:%S";
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
        usort($result, array($this, "compare_priority"));
        return $result;
    }

    public function pending_sup($status)
    {
        $result = [];
        $now = Carbon::now('America/Sao_Paulo');

        $_where = $this->where_push_query($status)['$_where'];
        $task_waiting_alert_time_attendance = intval(get_option("task_waiting_alert_time_attendance"));
        $task_waiting_limit_time_attendance = intval(get_option("task_waiting_limit_time_attendance"));


        $result = $this->db->
        select(['company', 'firstname', "tbltickets.priority as priority", "ticketid", "date", "is_externo", "subject", "tbltickets.status as status", "MAX(tblstafftasks.id) as taskid", "MAX(tblstafftasks.name) as nome"])
            ->from("tbltickets")
            ->join('tblstaff', 'tblstaff.staffid = tbltickets.assigned', "INNER")
            ->join('tblclients', 'tblclients.userid = tbltickets.userid', "INNER")
            ->join('tblstafftasks', 'tblstafftasks.rel_id = ticketid', 'INNER')
            ->where($_where)
            ->group_by('ticketid')
            ->get()->result();

        //essa gambira de usar o max foi por causa de uma treta sobre o group_by nessa nova versão do mysql.
        //se souberem alguma forma melhor, me avisa please :DDDDD'''

        $this->load->model("tickets_model");
        $this->load->model("tasks_model");

        foreach ($result as $key => $obj) {
            $dia_in_esp = Carbon::parse($obj->date);

            /** Gambirinha marots */
            $date_auxiliar = $this->tickets_model->get_ticket_timestatus($obj->status, $obj->ticketid);
            if($date_auxiliar != null) {
//                $obj->date = $date_auxiliar->datetime;


                $obj->date_espera = $date_auxiliar->datetime;
                $espera_parcial = Carbon::parse($obj->date_espera);


//                $obj->date_espera = $date_auxiliar->datetime;
                $weekend_espera_parcial = $this->filtra_fim_de_semana($espera_parcial, Carbon::now());
                $weekend_espera_parcial = $weekend_espera_parcial * 24 * 60 * 60;

                $interval_parcial = Carbon::now()->diffInSeconds($espera_parcial);
                $interval_parcial -= $weekend_espera_parcial;
                $interval_parcial = secondsToHumanReadable($interval_parcial);

                $obj->date_espera = $interval_parcial;
            }else
                $obj->date_espera = "Sem Alteração";
            /** Gambirinha marots */

            /** Gambirinha marots desenvolvedores*/
            $staff_auxiliar = $this->tasks_model->get_task_assignees_by_ticketid_and_status($obj->ticketid, self::TASK_STATUS[$status]);
            if($staff_auxiliar != null){
                $obj->developers = $staff_auxiliar;
                foreach ($obj->developers as $chave => $value){
                    $obj->developers[$chave]['profile_pic'] = staff_profile_image($value['assigneeid'], array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $value['firstname'],
                    ));
                }
            }
            /** Gambirinha marots desenvolvedores*/


            $dia_in_esp = Carbon::parse($obj->date); //Total em espera

            /** @var  time */

            $dt2 = Carbon::now();

            $weekend_espera_total = $this->filtra_fim_de_semana($dia_in_esp, $dt2);
            $weekend_espera_total = $weekend_espera_total * 24 * 60 * 60;


            /** @var  time */


            $interval = Carbon::now()->diffInSeconds($dia_in_esp);
            $interval -= $weekend_espera_total;
            $interval = secondsToHumanReadable($interval);

            $obj->date = $interval;
            $obj->time = $interval;

            $minutos_espera = $dia_in_esp->diffInMinutes()-$now->diffInMinutes();
            if($minutos_espera >= $task_waiting_alert_time_attendance) {
                if($minutos_espera >= $task_waiting_limit_time_attendance)
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
            unset($obj->date);
            $obj->priority = self::PRIORITY[$obj->priority];
            //$obj->company = substr($obj->company, 0, 30);
            $result[$key] = $obj;
        }
        usort($result, array($this, "compare_priority"));
        return $result;
    }

    public function staffDisponiveis()
    {
        $status = [
            0 => self::TASK_STATUS_PROGRESSO,
        ];

//        $staffs = $this->db->select(["assigned"])
//            ->from('tbltickets')
//            ->where_in('status', $status)
//            ->order_by('admin ASC')
//            ->get()->result_array();
//        $staff_ids = [];

        $staffs = $this->db->select(["staffid"])
            ->from('tblstafftaskassignees')
            ->join('tblstafftasks', 'tblstafftasks.id = tblstafftaskassignees.taskid', 'INNER')
            ->where_in('status', $status)
            ->order_by('staffid ASC')
            ->get()->result_array();
        $staff_ids = [];

//        $this->db->where("tblstaff.partner_id", get_staff_partner_id());

        foreach ($staffs as $id)
            $staff_ids[] = $id["staffid"]; //staffid para tarefas


        $this->db->select(["firstname", "tblstaff.staffid"])
            ->from("tblstaff")
            ->distinct("tblstaff.staffid")
            ->join('tblstaffonline', 'tblstaffonline.staffid = tblstaff.staffid', "INNER");

        if ($staff_ids)
            $this->db->where_not_in("tblstaff.staffid", $staff_ids);

        return $this->db->where("active", 1)
            ->where("role", 5) // 5 desenvolvedor, 4 suporte
            ->where("is_online", 1)
            ->get()->result();


    }

    function compare_ticketid($a, $b)
    {
        return $a->ticketid - $b->ticketid;
    }

    function compare_taskid($a, $b)
    {
        return $a->id - $b->id;
    }

    function compare_priority($a, $b)
    {
        return $a->priority - $b->priority;
    }

    function compare_total_espera($a, $b)
    {
        return $a->old_date > $b->old_date;
    }

    function filtra_fim_de_semana($d1, $d2){

        return $d1->diffFiltered(\Carbon\CarbonInterval::days(), function (Carbon $date) {
            return $date->isWeekend();
        }, $d2);

    }


}