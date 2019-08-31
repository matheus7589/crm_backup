<?php

use App\Enums\TicketStatusEnum;

/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 08/11/2017
 * Time: 08:44
 */

class Attend_average_time extends Admin_controller
{

    const DEV_ATENDIDO = 32;
    const SUP_CONCLUIDO = 5;

    public function __construct()
    {
        parent::__construct();
        $this->load->model("attend_average_time_model");
        $this->load->model("tickets_model");
        $this->load->model("staff_model");
    }


    public function show(){

        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        $sWhere = array();
        array_push($sWhere, "WHERE tblstaff.partner_id = ".get_staff_partner_id()." and role not in (1, 6) ");
        $sOrderBy = " ";
        $plusJoin = " ";
        if (($this->input->post('date_from') && $this->input->post('date_to')) || $this->input->post('attend')) {
            $date_from = $this->input->post('date_from') ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('date_from'))
                ->format('Y-m-d 00:00:00.000000') : '';

            $date_to = $this->input->post('date_to') ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('date_to'))
                ->format('Y-m-d 23:59:59.000000') : '';

            $attend = $this->input->post('attend') ? $this->input->post('attend') : '';

            if ($this->input->post('attend')) {
                if(!$this->input->post('date_from') || !$this->input->post('date_to')){
                    $date_from = new \Carbon\Carbon('first day of last month');
                    $date_to = \Carbon\Carbon::tomorrow();
                }
                $plusJoin .= " and tblticketstimestatus.staffid= " . $attend . " ";
            }
        } else {
            $date_from = \Carbon\Carbon::today()->subDays(30)->format('Y-m-d');
            $date_to = \Carbon\Carbon::today()->format('Y-m-d');
        }
//        array_push($sWhere, " and tblstaff.role!=5 and tblstaff.active=1 and admin=0 ");
        if ($this->input->is_ajax_request()) {
            $timers = $this->staff_times($date_from, $date_to);
            $aColumns = array(
                'firstname',
                '1',
                '2'
            );
            $sIndexColumn = "staffid";
//            $sTable = 'tblticketstimestatus';
            $sTable = 'tblstaff';
            $sJoin = array(
                " join tblticketstimestatus on tblticketstimestatus.staffid = tblstaff.staffid " . $plusJoin . " and tblstaff.role!=5 and tblstaff.active=1 and admin=0 and tblticketstimestatus.datetime BETWEEN '" . $date_from . "' AND '" . $date_to . "' ",
            );
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $sJoin, $sWhere, array(
//                "(SELECT count(ticketid) FROM tblticketstimestatus WHERE datetime BETWEEN '" . $date_from . "' AND '" . $date_to . "' AND  tblticketstimestatus.staffid = tblstaff.staffid and tblstaff.active=1 and tblstaff.role!=5) as tickets",
                'tblstaff.staffid as staffid'
            ), " GROUP BY tblstaff.staffid" . $sOrderBy , 1);
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'firstname') {
                        $_data = '<div class="row">';
                        $_data .= '<div class=" col-md-12">';
                        $_data .= $aRow['firstname'];
                        $_data .= '</div>';
                        $_data .= '</div>';
                    }
                    if ($aColumns[$i] == "1") {
                        foreach ($timers as $timer){
                            if($timer['staffid'] == $aRow['staffid']){
                                $_data = $timer['mediahora'] . ' Hora(s) e ' . $timer['minutos'] . ' minuto(s)';
                            }
                        }
                    }
                    if ($aColumns[$i] == "2") {
                        $_data = '<div class="center">';
//                        $_data .= $aRow['2'];
                        $_data .= '<button class="btn btn-success" name="staff'.$aRow['staffid'].'" value="'.$aRow['staffid'].'" onclick="list('.$aRow['staffid'].')" style="margin: auto; vertical-align: middle">+</button>';
                        $_data .= '</div>';
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }
//            print_r($output['aaData']);
            usort($output['aaData'], array($this, "compare_attend"));
            echo json_encode($output);
            die();
        }

        $data['from'] = $date_from;
        $data['to'] = $date_to;
        $data['title'] = "Tempo Médio de Atendimento por Técnico";
        $data['staff'] = $this->attend_average_time_model->get_technicians();
        $data['await_average'] = $this->attend_average_time_model->get_average_await_time('', '', TicketStatusEnum::OPEN);
        $data['attend_average'] = $this->attend_average_time_model->get_average_await_time('', '', TicketStatusEnum::INPROGRESS);
        $this->load->view('admin/utilities/attend_average_time', $data);
    }

    public function staff_times($from, $to, $staffid = false, $_ticketid = false, $is_media = true)
    {
        if($staffid)
            $staff = $this->attend_average_time_model->get_technicians($staffid);
        else
            $staff = $this->attend_average_time_model->get_technicians();

        $dates = array();
//        $teste = array();

//        var_dump($staff);
        $tickets = [];
        foreach ($staff as $key_staff => $value_staff){

            if($_ticketid) {
                $tickets = $this->attend_average_time_model->get_tickets('', $from, $to, $_ticketid);
            }else {
                $where = array(
                    "datetime >=" => to_sql_date($from, true),
                    "datetime <=" => to_sql_date($to, true),
                    "statusid =" => $this::SUP_CONCLUIDO
                );
                $list_tickets = $this->attend_average_time_model->get_list_tickets($where);
                foreach ($list_tickets as $key => $list){

                    foreach ($this->attend_average_time_model->get_tickets('', $from, $to, $list['ticketid'], array()) as $inner_ticket){
                        $tickets[] = $inner_ticket;
                    }
                }
            }
//            var_dump($tickets);
            $sum_date = 0;
            $had_attend = 0;
            $ticketid = 0;
            $date_from = '';

            $cont = 0;

            foreach ($tickets as $key => $value){

                if ($value['statusid'] == 2 && $value_staff['staffid'] == $value['staffid']) {

                    if($had_attend == 0) {
//                    if($value['ticketid'] != $ticketid) {
                        $date_from = \Carbon\Carbon::parse($value['datetime']);
                        $had_attend = 1;
                        $ticketid = $value['ticketid'];
//                    }
                    }
                }

                if($had_attend == 1) {
//                    if($value['staffid'] == $value_staff['staffid']){
                        if ($value['statusid'] != 2) {
                            if ($value['ticketid'] == $ticketid) {
                                $date_to = \Carbon\Carbon::parse($value['datetime']);
                                $diff = Carbon\Carbon::parse($date_to)->diffFiltered(Carbon\CarbonInterval::minutes(), function (\Carbon\Carbon $date) {
                                    return !$date->isSunday();
                                }, Carbon\Carbon::parse($date_from));
//                                var_dump(array('current_staff: ' . $value_staff['staffid'], 'diff: ' . $diff,'ticketid: ' . $ticketid, 'staffid: '.$value['staffid'], 'current: ' . $ticketid, 'had_attend: ' . $had_attend, 'status_current: ' . $value['statusid']));
                                $sum_date += $diff;
                                $had_attend = 0;
                                $cont++;
//                                var_dump(array('aqui', $value['id'], $diff, $ticketid));
                            }else{
                                $had_attend = 0;
                            }
                        } else {
                            if ($key == count($tickets) - 1) {
                                /** Pega o ultimo status em atendimento do ticket */
                                $next = $this->attend_average_time_model->get_next_timestatus($ticketid, $value['datetime']);
                                /** retorna o proximo ticket depois do status */
                                if ($next != null) {
                                    $diff_parc = Carbon\Carbon::parse($next->datetime)->diffFiltered(Carbon\CarbonInterval::minutes(), function (\Carbon\Carbon $date) {
                                        return !$date->isSunday();
                                    }, Carbon\Carbon::parse($value['datetime']));
//                                var_dump($diff_parc);
                                    $sum_date += $diff_parc;
                                    $had_attend = 0;
                                }
                                var_dump(array($next, 'aqui'));
                            }

                            if($value_staff['staffid'] != $value['staffid']){
                                $date_to = \Carbon\Carbon::parse($value['datetime']);
                                $diff = Carbon\Carbon::parse($date_to)->diffFiltered(Carbon\CarbonInterval::minutes(), function (\Carbon\Carbon $date) {
                                    return !$date->isSunday();
                                }, Carbon\Carbon::parse($date_from));
//                                var_dump(array('current_staff: ' . $value_staff['staffid'], 'diff: ' . $diff,'ticketid: ' . $ticketid, 'staffid: '.$value['staffid'], 'current: ' . $ticketid, 'had_attend: ' . $had_attend, 'status_current: ' . $value['statusid']));
                                $sum_date += $diff;
                                $had_attend = 0;
                                $cont++;
                            }
                        }

                }
            }
//            var_dump(array($staffid, $tickets));
            if($is_media){
                $media = ($cont > 0) ? $sum_date/$cont : $sum_date;
            }else{
                $media = $sum_date;
            }

//            var_dump($media);
            $media = $media/60;
            $hora = explode('.', number_format($media, 2))[0];
            $minute = explode('.', number_format($media, 2))[1];
//            $minute = explode(".", (string)$media);
//            $teste = (count($minute) > 1) ? intval(($minute[1]/(10**(strlen((string)$minute[1]))))*60) : 0;
//            var_dump(array($cont, $value_staff['staffid'], $sum_date));
//            print_r($tickets);
//            var_dump($sum_date);
            array_push($dates, array(
                'staffid' => $value_staff['staffid'],
                'mediahora' => $hora,
                'minutos' => explode('.', number_format(((intval($minute)/100)*60), 2))[0], //formata os minutos
                'total_minutos' => $sum_date
            ));
        }
//        dd($teste);
//        var_dump($dates);
        return $dates;
    }

    function compare_attend($a, $b)
    {
//        (int)preg_replace('#[^0-9]+#', '', $b) --> Remove todos os caracteres nao numericos
        $explode_b = explode(" ", $b['1']);
        $explode_a = explode(" ", $a['1']);
        return ((intval($explode_b[0]) * 60) + intval($explode_b[3])) - ((intval($explode_a[0]) * 60) + intval($explode_a[3]));
//        return (int)preg_replace('#[^0-9]+#', '', $b[1]) - (int)preg_replace('#[^0-9]+#', '', $a[1]);
    }

    function list_bad_tickets(){


            $from = $this->input->get('from');
            $to = $this->input->get('to');
            $status = $this->input->get('status');
            if (!empty($status)) {
                $average = $this->attend_average_time_model->get_average_await_time(to_sql_date($from), to_sql_date($to), $status);
            } else {
                $average = $this->attend_average_time_model->get_average_await_time(to_sql_date($from), to_sql_date($to));
            }
            $tickets = $average['bad_tickets'];
            $times = $average['bad_times'];
//            $times = '38, 232, 225, 44, 82, 68, 34, 867, 59, 1060, 66, 71, 44, 32, 37, 1769, 132, 47, 58, 44, 84, 71, 60, 46, 70, 32, 66, 58, 39, 54, 63, 57, 47, 80, 44, 40, 1007, 47, 50, 33, 902, 810, 88, 75, 44, 47, 132, 58, 32, 97, 216, 46, 65, 44, 54, 67, 36, 32, 1094, 66, 154, 34, 35, 745, ';
//            $tickets = '11190, 11202, 11208, 11209, 11217, 11218, 11220, 11228, 11241, 11243, 11248, 11250, 11253, 11254, 11255, 11266, 11271, 11272, 11279, 11284, 11295, 11296, 11297, 11299, 11300, 11301, 11302, 11312, 11317, 11325, 11330, 11332, 11336, 11337, 11340, 11349, 11358, 11360, 11361, 11362, 11368, 11370, 11376, 11377, 11381, 11382, 11389, 11394, 11415, 11417, 11418, 11422, 11423, 11426, 11427, 11428, 11432, 11433, 11450, 11459, 11465, 11466, 11479, 11482, ';
            $tickets = explode(', ', $tickets);
            $times = explode(', ', $times);
//            var_dump($tickets, $times);


            $bad_tickets = array();
            foreach ($tickets as $key => $ticket){
                if(is_numeric($ticket)){
                    $aux = $this->tickets_model->get_few_ticket_info_by_id($ticket);
                    $aux->time = $times[$key];
                    array_push($bad_tickets, $aux);
                }
            }


            echo '<div class="table-responsive">
                                        <table class="table dt-table">
                                            <thead>
                                                <th>Ticket</th>
                                                <th>Assunto</th>
                                                <th>Atendente</th>
                                                <th>Espera</th>
                                            </thead>
                                            <tbody>';
                                            foreach ($bad_tickets as $key => $bad_ticket){
                                                echo '<tr><td>';
                                                echo '<a target="blank" href="';
                                                echo APP_BASE_URL."admin/tickets/ticket/".$bad_ticket->ticketid . '"';
                                                echo 'data-nome="' . $bad_ticket->ticketid .'" style="margin: auto; vertical-align: middle">'. '#' . $bad_ticket->ticketid .'</a>';
                                                echo '</td>';
                                                echo '<td style="max-width: 150px;">';
                                                echo '<a target="blank" href="';
                                                echo APP_BASE_URL."admin/tickets/ticket/".$bad_ticket->ticketid . '"';
                                                echo 'data-nome="' . $bad_ticket->ticketid .'" style="margin: auto; vertical-align: middle">'. $bad_ticket->subject .'</a>';
                                                echo '</td>';
                                                echo '<td>';
//                                                echo '<a ';
                                                $atribuido = $bad_ticket->assigned;
                                                if(!empty($atribuido)){
                                                    echo '<a target="blank" href="' . APP_BASE_URL."admin/staff/member/".$bad_ticket->assigned . '"';
                                                }else{
                                                    echo '<p ';
                                                }
                                                $nome = '';
                                                if(empty($atribuido)){
                                                    $nome = 'Não atribuído';
                                                    echo ' style="margin: auto; vertical-align: middle">'. $nome .'</p>';
                                                }else{
                                                    $nome = $this->staff_model->get($bad_ticket->assigned)->firstname;
                                                    echo 'data-nome="' . $nome .'" style="margin: auto; vertical-align: middle">'. $nome .'</a>';
                                                }
                                                echo '<td>';
                                                $format = "";
                                                if($bad_ticket->time/60 > 24){
                                                    $format = "d:H\h:i\m";
                                                }else{
                                                    $format = "H\h:i\m";
                                                }
                                                $total = ($bad_ticket->time);
//                                                $horas = floor($total / 60);
//                                                $minutos = floor(($total - ($horas * 60)));
//                                                if($horas > 24)
//                                                    echo floor($horas / 24) . ' dias - ';
//                                                echo (($horas < 10) ? '0'.$horas : $horas) . "h:" . (($minutos < 10) ? '0'.$minutos : $minutos) . "m";
                                                echo segundosParaLeituraHumana($total * 60);
//                                                echo gmdate("H:i:s", ($bad_ticket->time*60));
                                                //echo $data;//date($format, mktime($bad_ticket->time));
                                                echo '</td>';
                                                echo '</tr>';
                                            }

                                            echo '</tbody>
                                        </table>
                                    </div>';

    }

    function compare_time($a, $b)
    {
        return $a->time < $b->time;
    }

    public function average_time(){
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $average = $this->attend_average_time_model->get_average_await_time(to_sql_date($from), to_sql_date($to), TicketStatusEnum::OPEN);
        if($average['diff_data'] <= 0){
            echo json_encode(array(
                'resultado' => 'Sem atendimentos no período',
            ));
        }else {
            $result = date("H\h:i\m", mktime(0, $average['diff_data'] / (count($average['cont_tickets'])) ?? 1 ));
            echo json_encode(array(
                'resultado' => $result,
            ));
        }
    }

    public function average_time_attend(){
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $average = $this->attend_average_time_model->get_average_await_time(to_sql_date($from), to_sql_date($to), TicketStatusEnum::INPROGRESS);
        if($average['diff_data'] <= 0){
            echo json_encode(array(
                'resultado' => 'Sem atendimentos no período',
            ));
        }else {
            $result = date("H\h:i\m", mktime(0, $average['diff_data'] / (count($average['cont_tickets']) ?? 1 )));
            echo json_encode(array(
                'resultado' => $result,
            ));
        }
    }

    public function show_formated(){

        $this->get_Replies_Times_Formated();
    }


    public function get_Replies_Times_Formated(){
        $tid = $this->input->post('ids');
        $from = !empty($this->input->post('from')) ? to_sql_date($this->input->post('from')) : \Carbon\Carbon::today()->subDays(30);
        $to = !empty($this->input->post('to')) ? to_sql_date($this->input->post('to')) : \Carbon\Carbon::today();
        $where = array(
            "tblticketstimestatus.datetime >=" => to_sql_date($from, true),
            "tblticketstimestatus.datetime <=" => to_sql_date($to, true),
            "tblticketstimestatus.statusid" => $this::SUP_CONCLUIDO
            );
        $tabela = '';
        $tabela .= "<div class='row'><div class='panel-body'><table class='table' id='table-resps'><thead>
                        <tr><th><b>#Ticket</b></th><th><b>Atendente</b></th><th><b>Tempo em Atendimento</b></th></tr></thead><tbody>";
        $tickets = $this->tickets_model->get_tickets_by_staffid('', $where);

        foreach ( $tickets as $resp)
        {
            $tempo = $this->staff_times($from, $to, $tid, $resp['ticketid'], false);
            if(empty($tempo))
                continue;
            if(isset($tempo[0]))
                $tempo = $tempo[0];
            if($tempo['total_minutos'] == 0)
                continue;
//            if($resp['staffid'] != $tid)
//                continue;
            $nome = explode(' ', get_staff_full_name($tid));
            $nome = $nome[0] . ' ' . $nome[1];

            $tabela .= '<tr>
                    <td>
                        <div class="col-md-3 ">
                            <a target="_blank" href="'.APP_BASE_URL.'admin/tickets/ticket/'.$resp["ticketid"].'"> #' . $resp['ticketid'] . '</a>
                        </div>    
                    </td>
                    <td>
                        <div align="left" style="font-size: 13px;">
                            <div class="col-md-3">
                                <p>
                                    <a target="_blank" href="'.APP_BASE_URL.'admin/profile/'.$tid.'">'. $nome .'</a>
                                </p>
                                <p class="text-muted">
                                    Colaborador
                                    <br>
                                    
                                </p>
                            </div>
                        </div>
                    </td>
                    <td data-order="' . $tempo['total_minutos'] . '">
                        <div class="col-md-9" align="left" style="font-size: 13px;">
                            <div class="clearfix"></div>
                            <div class="tc-content">
                                
                            </div>
                            <br>
                            <p>'.
//                                $tempo['mediahora'] . ' Hora(s) e ' . $tempo['minutos'] . ' minuto(s)'
                                    segundosParaLeituraHumana($tempo['total_minutos'] * 60)
//                                  $tempo['mediahora']
                            .'</p>
                            
                        </div>
                        
                    </td>
                </tr>';
        }
        $tabela .= "</tbody></table></div></div>";
        echo $tabela;
    }

}