<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 25/10/2017
 * Time: 12:38
 */

class Number_attends extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("attendance_report_model");
        $this->load->model("tickets_model");

    }
    public function get_ticket_data()
    {
        $id = $this->input->post('ids');
        $atendido = $this->input->post('atendido');
        $add_atendido = '';
        if($atendido == 'true'){
            $add_atendido = ' and tbltickets.status = 5 ';
        }
        $date_from = $_SESSION['number_attends_filtre']["date_from"];
        $date_to = $_SESSION['number_attends_filtre']["date_to"];

        $query = $this->db->query("SELECT tblcontacts.lastname, tbltickets.subject, tbltickets.lastreply, tblticketstatus.ticketstatusid,
                                    tblticketstatus.statuscolor, tbltickets.ticketid, tblstaff.staffid, tblstaff.firstname
                                    FROM tbltickets 
                                    LEFT JOIN tblticketstatus ON tbltickets.status = tblticketstatus.ticketstatusid 
                                    LEFT JOIN tblcontacts ON tbltickets.contactid = tblcontacts.id 
                                    LEFT JOIN tblstaff ON tbltickets.assigned = tblstaff.staffid
                                    WHERE tbltickets.userid = ".$id." AND tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "'
                                     " . $add_atendido . " 
                                    GROUP BY tbltickets.ticketid " );

        $this->db->where('userid', $id);
        $client = $this->db->get('tblclients')->row();

        $this->db->where('userid', $id);
        $contact = $this->db->get('tblcontacts')->row();

        $ultimo_atendimento = $this->tickets_model->get_last_atendimento($id);

        if(isset($ultimo_atendimento))
        {
            /** treta que eu to testanto */
            if(isset($ultimo_atendimento->lastreply)){
                $last = $ultimo_atendimento->lastreply;
            }else{
                $last = $ultimo_atendimento->date;
            }

            $hora_Ticket = Carbon\Carbon::parse($last);
            $hora_Atual = Carbon\Carbon::now();

            $diff = $hora_Ticket->diffInMinutes($hora_Atual);
            $diff = $diff * 60;

            $ultimo_atendimento = segundosParaLeituraHumana($diff);
            /** treta que eu to testanto */

//            $hora_Ticket = new DateTime($last);
//            $hora_Atual = new DateTime("now");
//            $intervalo = $hora_Ticket->diff($hora_Atual);
//            $ultimo_atendimento = $intervalo->format("%d Dias");
        }
        else
        {
            $ultimo_atendimento = "Sem atendimentos anteriores.";
        }

        $to = $client->company;
        $email = $contact->email;
        $phone = $client->phonenumber;
        $telEmpr = $contact->phonenumber;
        $celEmpr = $client->cellphone;
        $end = $client->address;
        $city = $client->city;

        echo '<table class="table table-striped table-attend info dataTable no-footer dtr-inline" id="whatever">
                <tr style="font-weight: bold"><th>Para</th><th>Endereço</th><th>Cidade</th></tr>
                <tr><th>'.$to.'</th><th>'.$end.'</th><th>'.$city.'</th></tr>
            </table>
            <table class="table table-striped table-attend info dataTable no-footer dtr-inline" id="teste">
                <tr style="font-weight: bold"><th>Telefone</th><th>Telefone Empresa</th><th>Celular Empresa</th></tr>
                <tr><th>'.$phone.'</th><th>'.$telEmpr.'</th><th>'.$celEmpr.'</th></tr>
            </table>
            <table class="table table-striped table-attend info dataTable no-footer dtr-inline" id="inova">
                <tr style="font-weight: bold"><th>Email</th><th>Último atendimento</th></tr>
                <tr><th>'.$email.'</th><td>'.$ultimo_atendimento.'</td></tr>
            </table>
            <hr>';

        echo '<table class="table table-striped" id="tb1">
                <thead>
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
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Opções
                    </th>
                </thead>';
        echo '<tbody>';
        foreach ($query->result() as $row)
        {
            $resposta = isset($row->lastreply) ? _d($row->lastreply) : 'Sem resposta';
            $tecnico = isset($row->staffid) ? '<a target="_blank" href="'. admin_url('staff/member/' . $row->staffid) .'">' . $row->firstname . '</a>' : 'Não atribuído';
            echo '<tr id="tr_' . $row->ticketid . '">
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
                         <button class="btn btn-success" onclick="openresp(\''.$row->ticketid.'\')" style="vertical-align: middle">+</button>
                         '.icon_btn(APP_BASE_URL.'/admin/tickets/ticket/'. $row->ticketid, 'sign-in').'
                    </td></tr>';

        }
        echo '</tbody>';
        echo "</table>";
    }

    public function sub_table(){
        $ticketid = $this->input->get('ticketid');

        $table =  "<table class='table table-striped' id='tb2'>
                            <thead>
                                <th>
                                    Infomações
                                </th>
                                <th>
                                    Resposta
                                </th>
                            </thead>";
        $table .= "<tbody>";
        foreach ($this->tickets_model->get_ticket_replies($ticketid) as $resp)
        {
            $status = $this->tickets_model->get_ticket_status($resp["reply_status"]);
            $table .= '<tr>
                            <td>
                                <div style="font-size: 13px;">
                                    <div>
                                        <p>
                                            <a href="'.APP_BASE_URL.'admin/profile/'.$resp["admin"].'">'.explode(" ", $resp["submitter"])[0].'</a>
                                        </p>
                                        <p class="text-muted">
                                            Colaborador
                                            <br>
                                            <p><span class="label inline-block" style="border:1px solid '.$status->statuscolor.'; color:'.$status->statuscolor.'">'.ticket_status_translate($resp["reply_status"]).'</span></p>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 13px;">
                                    <div class="clearfix"></div>
                                        '.$resp["message"].'
                                    <br>
                                    <p>-----------------------------</p>
                                    <p>IP: '.$resp["ip"].'</p>
                                </div>
                                <hr>
                                <div class="col-md-12">
                                    <span class="pull-left" style="font-size: 12px;">Postado '.date("d/m/Y H:i:s",strtotime($resp["date"])).'</span>
                                </div>
                            </td>
                        </tr>';
        }
        $table .= '</tbody>';
        $table .= '</table>';
        echo $table;
    }


    public function show()
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        $sWhere = array();
        array_push($sWhere, "WHERE tbltickets.partner_id = ".get_staff_partner_id()." ");
        $sOrderBy = " ORDER BY tickets DESC ";
        $plusJoin = "";
        if (($this->input->post('date_from') && $this->input->post('date_to')) || $this->input->post('attend')) {
            $date_from = $this->input->post('date_from') ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('date_from'))
                ->format('Y-m-d 00:00:00.000000') : '';

            $date_to = $this->input->post('date_to') ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('date_to'))
                ->format('Y-m-d 23:59:59.000000') : '';

            $attend = $this->input->post('attend') ? $this->filter_by_userid($this->input->post('attend')) : '';

            if ($this->input->post('attend')) {
                $plusJoin = " and tbltickets.userid=" . $attend->userid . " ";
            }else{
               $sOrderBy = " ORDER BY tickets DESC ";
            }
        } else {
            $date_from = \Carbon\Carbon::yesterday();
            $date_to = \Carbon\Carbon::tomorrow();
        }

        $_SESSION['number_attends_filtre'] = array(
            "date_from" => $date_from,
            "date_to" => $date_to,
        );
        $atendido = $this->input->post('atendido');
        $atendido = isset($atendido) ? $this->input->post('atendido') : false;
        $additional = '';
        if($atendido == 'on'){
            array_push($sWhere, " AND date BETWEEN '" . $date_from . "' AND '" . $date_to . "' AND tbltickets.status = 5 ");
            $additional = ' and tbltickets.status = 5 ';
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'company',
                'phonenumber',
                '1',
                'tblclients.userid',
            );
            $sIndexColumn = "userid";
            $sTable = 'tbltickets';
            $sJoin = array(
                " join tblclients on tblclients.userid = tbltickets.userid " . $plusJoin . " and tbltickets.date BETWEEN '" . $date_from . "' AND '" . $date_to . "' ",
            );
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $sJoin, $sWhere, array(
                "(SELECT count(*) FROM tbltickets WHERE date BETWEEN '" . $date_from . "' AND '" . $date_to . "' AND  tblclients.userid=tbltickets.userid " . $additional . " ) as tickets",
            ), " GROUP BY tbltickets.userid " . $sOrderBy , 1);
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++)
                {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'company') {
                        //$_data = '<div class="col-md-12"><b>Nome/Razão Social: </b><a href="' . admin_url('clients/client/' . $aRow['tbltickets.userid']) . '" data-name="' . $aRow['name'] . '">' . $aRow['social_reason']  . chr(10) . '</a>';
                        $_data = '<div class="row">';
                        $_data .= '<div class=" col-md-12">';
                        $_data .= $aRow['company'];
                        $_data .= '</div>';
                        $_data .= '</div>';
                    }
                    if ($aColumns[$i] == 'tblclients.userid') {
                        //$aRow['tblclients.userid'];
                        $_data = '<button class="btn btn-success" onclick="surelato('.$aRow['tblclients.userid'].')" style="margin: auto; vertical-align: middle">+</button>';
                        //$_data .= ';
                    }
                    if ($aColumns[$i] == "1") {
                        $_data = '<div class="row">';
                        $_data .= '<div class=" col-md-12">';
                        $_data .= $aRow['tickets'];
                        $_data .= '</div>';
                        $_data .= '</div>';
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }
            //dd($output);
            echo json_encode($output);
            die();
        }
        $data['title'] = 'Relatório de Atendimentos';
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $this->load->view('admin/utilities/number_attends', $data);
    }

    public function filter_by_userid($contact_id){

        return $this->attendance_report_model
            ->get_client_from_contact($contact_id);
    }

}