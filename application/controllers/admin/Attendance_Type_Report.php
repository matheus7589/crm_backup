<?php

use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: desenvolvimento3
 * Date: 17/11/2018
 * Time: 09:00
 */

class Attendance_Type_Report extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("attendance_type_model");
        $this->load->model("tickets_model");
    }

    public function show()
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }

        $date_from = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $date_to = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
        if (($this->input->post('date_from') && $this->input->post('date_to'))) {
            $date_from = $this->input->post('date_from') ? Carbon::createFromFormat('d/m/Y', $this->input->post('date_from'))
                ->format('Y-m-d') : '';

            $date_to = $this->input->post('date_to') ? Carbon::createFromFormat('d/m/Y', $this->input->post('date_to'))
                ->format('Y-m-d') : '';
        }

        if ($this->input->is_ajax_request()) {
            $sWhere = array(" WHERE tbltickets.status in (3, 5) " . ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : ""));
            $aColumns = array(
                'tblcustomfieldsvalues.value as name',
                '2',
                '3',
                '4',
            );
            $sIndexColumn = "ticketid";
            $sTable = 'tbltickets';
            $sJoin = array(
                " left join tblcustomfieldsvalues on tbltickets.ticketid = tblcustomfieldsvalues.relid AND tblcustomfieldsvalues.fieldid = 17 and tblcustomfieldsvalues.value <> '' " ,
            );
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $sJoin, $sWhere, array(
                'tblcustomfieldsvalues.value as name',
                "( SELECT count(*) from tbltickets left join tblcustomfieldsvalues on tbltickets.ticketid = tblcustomfieldsvalues.relid AND tblcustomfieldsvalues.fieldid = 17 and tblcustomfieldsvalues.value <> '' " .
                ((!is_null($date_from) && !is_null($date_to)) ? " WHERE tbltickets.status in (3, 5) AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")  . " ) as total",

            ), " GROUP BY tblcustomfieldsvalues.value ", 1);
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();


                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }


                    if (empty($aRow['name'])) {
                        $count = $this->db->query("SELECT count(*) as attend from tbltickets "
                            . " WHERE tbltickets.ticketid not in (SELECT tblcustomfieldsvalues.relid from tblcustomfieldsvalues WHERE tblcustomfieldsvalues.value <> '' AND tblcustomfieldsvalues.fieldid = 17 )" .
                            ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
                            .  " and tbltickets.status in (3, 5) ;")->row();
                    } else {
                        $count = $this->db->query("SELECT count(*) as attend from tbltickets " .
                            " left join tblcustomfieldsvalues on tbltickets.ticketid = tblcustomfieldsvalues.relid and tblcustomfieldsvalues.value <> '' AND tblcustomfieldsvalues.fieldid = 17 "
                            . " WHERE tblcustomfieldsvalues.value = '" . $aRow['name']  . "' " .
                            ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
                            .  " and tbltickets.status in (3, 5) ;")->row();
                    }

                    if ($aColumns[$i] == 'tblcustomfieldsvalues.value as name') {
                        $_data = '<div class="row">';
                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<a href="#" onclick="detalhesServicos(\''.$aRow['name'].'\', ' . $count->attend . '); return false;">';
                        $_data .= $aRow['name'] ? $aRow['name'] : 'Serviço não selecionado';
                        $_data .= '</div>';
                        $_data .= '</div>';
                    }

                    if ($aColumns[$i] == "2" ) {
                        $_data = number_format($count->attend);
                    }

                    if ($aColumns[$i] == "3") {
                        $percent = ($count->attend / $aRow['total']) * 100;
                        $_data = round($percent, 2) . '%';
                    }

                    if ($aColumns[$i] == "4") {
                        $options = icon_btn('#', 'fa fa-ticket','btn-info', array('onclick' => 'detalhesTickets(\''.$aRow['name'].'\', ' . $count->attend . '); return false;'));
                        $_data = $options;
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }
            usort($output['aaData'], array($this, "compare_percent"));
            echo json_encode($output);
            die();
        }

        $atendimentos = $this->attendance_type_model->getAtendimentos($date_from, $date_to);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['atendimentos'] = $atendimentos;
        $data['services'] = $this->tickets_model->get_service();
        $data['servicenv2'] = $this->tickets_model->get_secondservice();

        $this->load->view('admin/reports/attendance_type', $data);
    }

    function compare_percent($a, $b)
    {
        return floatval($a[2]) < floatval($b[2]);
    }

    public function get_atendimentos()
    {
        $date_from = Carbon::createFromFormat('d/m/Y', $this->input->get('from'))->format('Y-m-d');
        $date_to = Carbon::createFromFormat('d/m/Y', $this->input->get('to'))->format('Y-m-d');

        $atendimentos = $this->attendance_type_model->getAtendimentos($date_from, $date_to);
        $atendimentos = ["atendimentos" => number_format($atendimentos->total)];

        echo json_encode($atendimentos);
    }

    public function get_services_percentage()
    {
        $name = $this->input->get('name');
        $total = intval($this->input->get('total'));
        $date_from = ($this->input->get('from')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('from'))->format('Y-m-d') : null;
        $date_to = ($this->input->get('to')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('to'))->format('Y-m-d') : null;

        $atendimentos = $this->db->query("SELECT tbltickets.service as service, count(tbltickets.service) as total, count(*) as full_total from tblcustomfieldsvalues " .
            " join tbltickets on tbltickets.ticketid = tblcustomfieldsvalues.relid AND tblcustomfieldsvalues.fieldid = 17 and tblcustomfieldsvalues.value = '" . $name . "' AND tbltickets.service <> 0 and tbltickets.status in (3, 5) " .
            ((!is_null($date_from) && !is_null($date_to)) ? " AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' " : "")
            . " WHERE tblcustomfieldsvalues.fieldid = 17 AND tblcustomfieldsvalues.value = '" . $name .
            "' AND tbltickets.service <> 0 GROUP BY tbltickets.service ")->result();

        $services = $this->attendance_type_model->get_services();

        $table = '<div class="col-md-12">';
        $table .= '<div style="float: right; font-size: x-large; margin-bottom: 10px; margin-top: 5px;">Total Atendimentos: <span id="atendimentos" style="padding:3px;" class="label-primary">
                                ' . $total . '</span></div>';
        $table .= '<h3 style="float: left;">Tipo de Serviço: ' . $name . '</h3>';
        $table .= '<table class="table table-striped table-sm" id="services">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th class="text-center">Serviço</th>';
        $table .= '<th class="text-center">Número de Atendimentos</th>';
        $table .= '<th class="text-center">Percentual</th>';
        $table .= '<th class="text-center">Detalhes</th>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        foreach ($services as $service) {
            foreach ($atendimentos as $atendimento) {

                if ($atendimento->service == $service->serviceid) {
                    $table .= '<tr>';
                    $table .= '<td><a href="#" onclick="detalhesSecondServices(\'' . $service->name . '\', \'' . $name . '\',' .
                        $atendimento->total . ', ' . $service->serviceid . '); return false;">
                    ' . $service->name. '</td>';
                    $table .= '<td class="text-center">' . number_format($atendimento->total) . '</td>';
                    $percent = ($atendimento->total / $total) * 100;
                    $table .= '<td class="text-center">' . round($percent, 2) . '%</td>';
                    $table .= '<td class="text-center">' . icon_btn('#', 'fa fa-ticket','btn-info',
                            array('onclick' => 'detalhesTicketsFirstServices(\''.$service->name.'\', '
                            . $service->serviceid . ', ' . $atendimento->total . ', \'' . $name . '\'); return false;')) . '</td>';
                    $table .= '</tr>';
                }
            }
        }
        $table .= '</tbody>';
        $table .= '</table>';
        $table .= '</div>';

        echo $table;
    }

    public function get_second_service_percentage()
    {
        $name = $this->input->get('name');
        $service_name = $this->input->get('service_name');
        $total = intval($this->input->get('total'));
        $serviceid = $this->input->get('serviceid');
        $date_from = ($this->input->get('from')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('from'))->format('Y-m-d') : null;
        $date_to = ($this->input->get('to')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('to'))->format('Y-m-d') : null;

        $atendimentos = $this->attendance_type_model->getSecondServiceAttends($service_name, $date_from, $date_to, $serviceid);

        $services = $this->attendance_type_model->get_second_services($serviceid);

        $table = '<div class="col-md-12">';
        $table .= '<div style="float: right; font-size: x-large; margin-top: 5px;">Total Atendimentos: <span id="atendimentos" style="padding:3px;" class="label-primary">
                                ' . $total . '</span></div>';
        $table .= '<h3 style="">Tipo de Serviço: ' . $service_name . '</h3>';
        $table .= '<h3 style="">Serviço Nivel 1: ' . $name . '</h3> <br>';

        $table .= '<table class="table table-striped table-sm" id="servicesnv2">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th class="text-center">Serviço</th>';
        $table .= '<th class="text-center">Número de Atendimentos</th>';
        $table .= '<th class="text-center">Percentual</th>';
        $table .= '<th class="text-center">Detalhes</th>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        foreach ($services as $service) {
            foreach ($atendimentos as $atendimento) {

                if ($atendimento->servicenv2 == $service->secondServiceid) {
                    $table .= '<tr>';
                    $table .= '<td>' . $service->name. '</td>';
                    $table .= '<td class="text-center">' . number_format($atendimento->total) . '</td>';
                    $percent = ($atendimento->total / $total) * 100;
                    $table .= '<td class="text-center">' . round($percent, 2) . '%</td>';
                    $table .= '<td class="text-center">' . icon_btn('#', 'fa fa-ticket','btn-info',
                            array('onclick' => 'detalhesTicketsSecondServices(\''.$service->name.'\', '
                                . $serviceid . ', ' . $service->secondServiceid . ', ' . $atendimento->total . ', \'' . $service_name . '\'); return false;')) . '</td>';
                    $table .= '</tr>';
                }
            }
        }
        $table .= '</tbody>';
        $table .= '</table>';
        $table .= '</div>';

        echo $table;
    }

    public function get_tickets_from_services()
    {
        $name = $this->input->get('name');
        $total = intval($this->input->get('total'));
        $date_from = ($this->input->get('from')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('from'))->format('Y-m-d') : null;
        $date_to = ($this->input->get('to')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('to'))->format('Y-m-d') : null;

        $tickets = $this->attendance_type_model->getTicketsFromServices($name, $date_from, $date_to);

        echo '<div class="col-md-12">';
        echo '<div style="float: right; font-size: x-large; margin-bottom: 10px; margin-top: 5px;">Total Atendimentos: <span id="atendimentos" style="padding:3px;" class="label-primary">
                                ' . $total . '</span></div>';
        echo '<h3 style="float: left;">Tipo de Serviço: ' . $name . '</h3>';

        echo '<table class="table table-striped table-sm" id="tickets-from-service">
                <thead>
                    <th class="col-md-1" style="font-size: 13px; font-weight: bold">
                        #
                    </th>
                    <th>
                        Cliente
                    </th>
                    <th class="col-md-3" style="font-size: 13px; font-weight: bold">
                        Assunto
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Status
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Última Resposta
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Técnico
                    </th>
                    <th class="col-md-1" style="font-size: 13px; font-weight: bold">
                        Opções
                    </th>
                </thead>';
        echo '<tbody>';
        foreach ($tickets as $row)
        {
            $resposta = isset($row->lastreply) ? _d($row->lastreply) : 'Sem resposta';
            $tecnico = isset($row->staffid) ? '<a target="_blank" href="'. admin_url('staff/member/' . $row->staffid) .'">' . $row->firstname . '</a>' : 'Não atribuído';
            echo '<tr id="tr_' . $row->ticketid . '">
                    <td>
                        <a target="_blank" href="/crm/admin/tickets/ticket/' . $row->ticketid . '">#' . $row->ticketid . '</a>
                    </td>
                    <td>
                        <a target="_blank" href="/crm/admin/clients/client/' . $row->ticketuserid . '">' . get_client_by_id($row->ticketuserid)->company . '</a>
                    </td>
                    <td> 
                       '.$row->subject.' 
                    </td> 
                    <td > 
                        <span class="label" style="border:1px solid '.$row->statuscolor.'; color:'.$row->statuscolor.'">'.ticket_status_translate($row->ticketstatusid).'</span> 
                    </td> 
                    <td > 
                        ' . $resposta . '
                    </td> 
                    <td> 
                        ' . $tecnico . '
                    </td> 
                    <td> 
                         '.icon_btn(APP_BASE_URL.'/admin/tickets/ticket/'. $row->ticketid, 'sign-in').'
                    </td></tr>';

        }
        echo '</tbody>';
        echo "</table>";
        echo '</div>';

    }


    public function get_tickets_from_servicesnv1()
    {
        $name = $this->input->get('name');
        $service_name = $this->input->get('service_name');
        $total = intval($this->input->get('total'));
        $serviceid = $this->input->get('serviceid');
        $date_from = ($this->input->get('from')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('from'))->format('Y-m-d') : null;
        $date_to = ($this->input->get('to')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('to'))->format('Y-m-d') : null;

        $tickets = $this->attendance_type_model->getTicketsFromServicesnv1($service_name, $date_from, $date_to, $serviceid);

        echo '<div class="col-md-12">';
        echo '<div style="float: right; font-size: x-large; margin-bottom: 10px; margin-top: 5px;">Total Atendimentos: <span id="atendimentos" style="padding:3px;" class="label-primary">
                                ' . $total . '</span></div>';
        echo '<h3 style="">Tipo de Serviço: ' . $service_name . '</h3>';
        echo '<h3 style="">Serviço Nivel 1: ' . $name . '</h3> <br>';

        echo '<table class="table table-striped table-sm" id="tickets-from-servicenv2">
                <thead>
                    <th class="col-md-1" style="font-size: 13px; font-weight: bold">
                        #
                    </th>
                    <th>
                        Cliente
                    </th>
                    <th class="col-md-3" style="font-size: 13px; font-weight: bold">
                        Assunto
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Status
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Última Resposta
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Técnico
                    </th>
                    <th class="col-md-1" style="font-size: 13px; font-weight: bold">
                        Opções
                    </th>
                </thead>';
        echo '<tbody>';
        foreach ($tickets as $row)
        {
            $resposta = isset($row->lastreply) ? _d($row->lastreply) : 'Sem resposta';
            $tecnico = isset($row->staffid) ? '<a target="_blank" href="'. admin_url('staff/member/' . $row->staffid) .'">' . $row->firstname . '</a>' : 'Não atribuído';
            echo '<tr id="tr_' . $row->ticketid . '">
                    <td>
                        <a target="_blank" href="/crm/admin/tickets/ticket/' . $row->ticketid . '">#' . $row->ticketid . '</a>
                    </td>
                    <td>
                        <a target="_blank" href="/crm/admin/clients/client/' . $row->ticketuserid . '">' . get_client_by_id($row->ticketuserid)->company . '</a>
                    </td>
                    <td> 
                       '.$row->subject.' 
                    </td> 
                    <td > 
                        <span class="label" style="border:1px solid '.$row->statuscolor.'; color:'.$row->statuscolor.'">'.ticket_status_translate($row->ticketstatusid).'</span> 
                    </td> 
                    <td > 
                        ' . $resposta . '
                    </td> 
                    <td> 
                        ' . $tecnico . '
                    </td> 
                    <td> 
                         '.icon_btn(APP_BASE_URL.'/admin/tickets/ticket/'. $row->ticketid, 'sign-in').'
                    </td></tr>';

        }
        echo '</tbody>';
        echo "</table>";
        echo '</div>';

    }

    public function get_tickets_from_servicesnv2()
    {
        $name = $this->input->get('name');
        $service_name = $this->input->get('service_name');
        $total = intval($this->input->get('total'));
        $serviceid = $this->input->get('serviceid');
        $servicenv2 = $this->input->get('servicenv2');
        $date_from = ($this->input->get('from')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('from'))->format('Y-m-d') : null;
        $date_to = ($this->input->get('to')) ? Carbon::createFromFormat('d/m/Y', $this->input->get('to'))->format('Y-m-d') : null;

        $tickets = $this->attendance_type_model->getTicketsFromServicesnv2($service_name, $date_from, $date_to, $serviceid, $servicenv2);

        echo '<div class="col-md-12">';
        echo '<div style="float: right; font-size: x-large; margin-bottom: 10px; margin-top: 5px;">Total Atendimentos: <span id="atendimentos" style="padding:3px;" class="label-primary">
                                ' . $total . '</span></div>';
        echo '<h3 style="">Tipo de Serviço: ' . $service_name . '</h3>';
        echo '<h3 style="">Serviço Nivel 1: ' . $this->attendance_type_model->get_first_service_name($serviceid) . '</h3>';
        echo '<h3 style="">Serviço Nivel 2: ' . $this->attendance_type_model->get_second_service_name($servicenv2) . '</h3>';

        echo '<table class="table table-striped table-sm" id="tickets-from-servicenv2">
                <thead>
                    <th class="col-md-1" style="font-size: 13px; font-weight: bold">
                        #
                    </th>
                    <th>
                        Cliente
                    </th>
                    <th class="col-md-3" style="font-size: 13px; font-weight: bold">
                        Assunto
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Status
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Última Resposta
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Técnico
                    </th>
                    <th class="col-md-1" style="font-size: 13px; font-weight: bold">
                        Opções
                    </th>
                </thead>';
        echo '<tbody>';
        foreach ($tickets as $row)
        {
            $resposta = isset($row->lastreply) ? _d($row->lastreply) : 'Sem resposta';
            $tecnico = isset($row->staffid) ? '<a target="_blank" href="'. admin_url('staff/member/' . $row->staffid) .'">' . $row->firstname . '</a>' : 'Não atribuído';
            echo '<tr id="tr_' . $row->ticketid . '">
                    <td>
                        <a target="_blank" href="/crm/admin/tickets/ticket/' . $row->ticketid . '">#' . $row->ticketid . '</a>
                    </td>
                    <td>
                        <a target="_blank" href="/crm/admin/clients/client/' . $row->ticketuserid . '">' . get_client_by_id($row->ticketuserid)->company . '</a>
                    </td>
                    <td> 
                       '.$row->subject.' 
                    </td> 
                    <td > 
                        <span class="label" style="border:1px solid '.$row->statuscolor.'; color:'.$row->statuscolor.'">'.ticket_status_translate($row->ticketstatusid).'</span> 
                    </td> 
                    <td > 
                        ' . $resposta . '
                    </td> 
                    <td> 
                        ' . $tecnico . '
                    </td> 
                    <td> 
                         '.icon_btn(APP_BASE_URL.'/admin/tickets/ticket/'. $row->ticketid, 'sign-in').'
                    </td></tr>';

        }
        echo '</tbody>';
        echo "</table>";
        echo '</div>';

    }


}