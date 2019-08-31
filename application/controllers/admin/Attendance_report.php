<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 18/10/2017
 * Time: 08:39
 */

class Attendance_report extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("attendance_report_model");
        $this->load->model("tickets_model");

    }

    public function show_formated(){

        $this->get_Replies_Tickets_Formated();
    }

    public function get_Replies_Tickets_Formated()
    {
        $tid = $this->input->post('ids');
        $tabela = '';
        $tabela .= "<div class='row'><div class='panel-body'><table class='table' id='table-resps'><thead>
                        <tr><th><b>Infomações</b></th><th><b>Resposta</b></th></tr></thead><tbody>";

        foreach ($this->tickets_model->get_ticket_replies($tid) as $resp)
        {
            $status = $this->tickets_model->get_ticket_status($resp["reply_status"]);
            $tabela .= '<tr>
                    <td>
                        <div align="left" style="font-size: 13px;">
                            <div class="col-md-3 border-right">
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
                        <div class="col-md-9" align="left" style="font-size: 13px;">
                            <div class="clearfix"></div>
                            <div class="tc-content">
                                '.$resp["message"].'
                            </div>
                            <br>
                            <p>-----------------------------</p>
                            <p>IP: '.$resp["ip"].'</p>
                        </div>
                        <hr>
                        <span class="col-md-12" style="font-size: 12px;">Postado '.date("d/m/Y H:i:s",strtotime($resp["date"])).'</span>
                    </td>
                </tr>';
//            $tabela .= '<tr><td colspan="2"><span class="pull-left" style="font-size: 12px;">Postado '.date("d/m/Y H:i:s",strtotime($resp["date"])).'</span></td></tr>';
//            $tabela .= '<tr class="hide"></tr><td colspan="3"><span class="pull-left" style="font-size: 12px;">Postado '.date("d/m/Y H:i:s",strtotime($resp["date"])).'</span></td><tr><tr class="hide"></tr></tr>';
        }
        $tabela .= "</tbody></table></div></div>";
        echo $tabela;
    }

    public function show()
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        $sWhere = array();
        array_push($sWhere, "WHERE tbltickets.partner_id = ".get_staff_partner_id()." ");
        if((($this->input->post('date_from') || $this->input->post('date_to')) || $this->input->post('attend') || ($this->input->post('date_from_status') || $this->input->post('date_to_status'))) && PAINEL == INORTE) {
            $date_from = $this->input->post('date_from') ?  \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('date_from'))
                ->format('Y-m-d 00:00:00.000000') : Carbon\Carbon::today();

            $date_to = $this->input->post('date_to') ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('date_to'))
                ->format('Y-m-d 23:59:59.000000') : \Carbon\Carbon::now();

            /**
             * date status
             */
            $date_from_status = $this->input->post('date_from_status') != '' ?  \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('date_from_status'))
                ->format('Y-m-d 00:00:00.000000') : $date_from;
            $date_to_status = $this->input->post('date_to_status') != '' ?  \Carbon\Carbon::createFromFormat('d/m/Y', $this->input->post('date_to_status'))
                ->format('Y-m-d 23:59:59.000000') : $date_to;
            if($this->input->post('status') != '') {
                $Where_status = " AND tblticketstimestatus.statusid = " . $this->input->post('status');
                $status = $this->input->post('status');
            }else {
                $Where_status = '';
                $status = '';
            }

            /**
             * date status
             */

            $attend = $this->input->post('attend') ?  $this->filter_by_userid($this->input->post('attend')) : '';

            if(!$this->input->post('attend'))
                array_push($sWhere, " AND date BETWEEN '" . $date_from . "' AND '" . $date_to . "' AND tblticketstimestatus.datetime BETWEEN '" .$date_from_status . "' AND '" . $date_to_status . "' " . $Where_status );
            else if(!($this->input->post('date_from') && $this->input->post('date_to')))
                array_push($sWhere, " AND tbltickets.userid= " . $attend->userid. " ");
            else
                array_push($sWhere, " AND date BETWEEN '" . $date_from . "' AND '" . $date_to . "' AND tbltickets.userid=" . $attend->clientid. " AND tblticketstimestatus.datetime BETWEEN '" .$date_from_status . "' AND '" . $date_to_status . "' " . $Where_status);

        }
        /** To colocando essa parada separada pq essa desse jeito que foi feita não funcionou */
        else if(($this->input->post('date_from') || $this->input->post('date_to') || $this->input->post('attend')) && PAINEL == QUANTUM)
        {
            if (!$this->input->post('date_to'))
                $date_to = Carbon\Carbon::now()->addDay()->toDateString();
            else
                $date_to = to_sql_date($this->input->post('date_to')." 23:59:00",true);
            if (!$this->input->post('date_from'))
                $date_from = '0000-01-01';
            else
                $date_from = to_sql_date($this->input->post('date_from'));
            if($this->input->post('attend'))
                array_push($sWhere, " AND tbltickets.userid = " . $this->filter_by_userid($this->input->post('attend'))->clientid);

            array_push($sWhere, " AND date BETWEEN '" . $date_from . "' AND '" . $date_to . "' ");
//            echo json_encode($sWhere);
//            die();
        }
        /** Final */
        else
        {
            $today = \Carbon\Carbon::now();
            $yesterday = \Carbon\Carbon::today();
            $date_from_status = $yesterday;
            $date_to_status = $today;
            $status = 1;
            array_push($sWhere, " AND date BETWEEN '" . $yesterday . "' AND '" . $today . "' ");
        }


        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                '2',
                '1',
//                'tbltickets.userid',
                'tbltickets.ticketid',
            );
            $sIndexColumn = "userid";
            $sTable = 'tbltickets';
            $sJoin = array(
                ' join tblclients on tblclients.userid = tbltickets.userid ',
                ' join tblticketstimestatus on tblticketstimestatus.ticketid = tbltickets.ticketid '
            );
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $sJoin, $sWhere, array(
                'tbltickets.ticketid',
                'company',
                'date',
                'tbltickets.name',
                'lastreply',
                //'(SELECT count(*) FROM tbltickets WHERE tblclients.userid=tbltickets.userid) as tickets',
                'social_reason',
                'address',
                'city',
                'subject',
                'phonenumber',
                'cellphone',
                '(SELECT email FROM tblcontacts WHERE tblcontacts.userid=tblclients.userid and is_primary=1) as email',
                '(SELECT firstname FROM tblcontacts WHERE tblcontacts.id=tbltickets.contactid) as contato',
                '(SELECT firstname FROM tblstaff WHERE tblstaff.staffid=tbltickets.assigned) as staff',
                'tbltickets.userid',
                '(SELECT message from tblticketreplies WHERE tblticketreplies.ticketid = tbltickets.ticketid ORDER BY tblticketreplies.date DESC LIMIT 0,1) as reply',
                'message',
                'name_soli',
                'status'

            ), ' GROUP BY tbltickets.ticketid ORDER BY tbltickets.ticketid DESC', 1);
            //dd($result);
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == '2') {
                        $_data = '<table class="table"><tr><td class="col-md-4" style="white-space: nowrap;"><b>Nome/Razão Social: </b></td><td><a href="' . admin_url('clients/client/' . $aRow['userid']) . '" data-name="' . $aRow['name'] . '">' . $aRow['social_reason']  . chr(10) . '</a>'. "</td></tr>";
//                        $_data .= '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Nome Fantasia: </b></td><td>' . $aRow['company'] . "\n" . "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
//                        $_data .= '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Endereço: </b></td><td>' . $aRow['address'] . "\n". "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Cidade: </b></td><td>' . $aRow['city'] . chr(10). "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
//                        $_data .= '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Telefone: </b></td><td>' . $aRow['phonenumber'] . "\t". "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Celular: </b></td><td>' . $aRow['cellphone'] . "\n". "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>E-mail: </b></td><td>' . $aRow['email'] . chr(10). "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
//                        $_data .= '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Assunto: </b></td><td>' . $aRow['subject']. "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
//                        $_data .= '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Laudo: </b></td><td>' . $aRow['reply']. "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
//                        $_data .= '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        $status = $this->tickets_model->get_ticket_status($aRow['status']);
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Status: </b></td><td><span class="label inline-block" style="border:1px solid '.$status->statuscolor.'; color:'.$status->statuscolor.'">'.ticket_status_translate($aRow['status']).'</span>'. "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
//                        $_data .= '</div>';
                        $_data .= '</table>';
                    }
                    if ($aColumns[$i] == '1') {
//                        $_data = '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        $_data = '<table class="table"><tr><td class="col-md-4" style="white-space: nowrap;"><b>Código: </b></td><td><a target="_blank" href="' . admin_url('tickets/ticket/' . $aRow['tbltickets.ticketid']) . '"> ' . $aRow['tbltickets.ticketid'] . '</a>' . "\t". "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Nome do Consultor: </b></td><td>' . $aRow['staff'] . "\n". "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
//                        $_data .= '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Data de Abertura: </b></td><td>' . Carbon\Carbon::parse($aRow['date'])->format("d/m/Y H:i") . "\n". "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Data de Fechamento: </b></td><td>' . \Carbon\Carbon::parse($aRow['lastreply'])->format("d/m/Y H:i") . chr(10). "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
//                        $_data .= '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        if(PAINEL == QUANTUM)
                            $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Solicitante: </b></td><td>' . $aRow['name_soli'] . "\n". "</td></tr>";
                        else
                            $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Solicitante: </b></td><td>' . $aRow['contato'] . "\n". "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '<div class=" col-md-12">';
                        $datetime1 = new DateTime($aRow['date']);
                        $datetime2 = new DateTime($aRow['lastreply']);
                        $interval = $datetime1->diff($datetime2);
                        $format = '';
                        if($interval->h !== 0){
                            if($interval->h > 1) {
                                $format .= "%H horas ";
                            }else{
                                $format .= "%H hora ";
                            }
                        }
                        if($interval->i !== 0){
                            $format .= "%I minutos ";
                        }
                        if($interval->s !== 0 && PAINEL == INORTE){
                            $format .= "%S segundos";
                        }

                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Tempo de Atendimento: </b></td><td>' . $interval->format($format) . chr(10). "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
//                        $_data .= '<div class="row">';
//                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<tr><td class="col-md-4" style="white-space: nowrap;"><b>Informação Cliente: </b></td><td>' . $aRow['message']. "</td></tr>";
//                        $_data .= '</div>';
//                        $_data .= '</div>';
                        $_data .= '</table>';
                    }
                    if ($aColumns[$i] == 'tbltickets.ticketid') {
//                        $_data = '<table class="table"><tr><td><button class="btn btn-success" name="ticket_'.$aRow['tbltickets.ticketid'].'" value="'.$aRow['tbltickets.ticketid'].'" onclick="surelato('.$aRow['tbltickets.ticketid'].')" style="margin: auto; vertical-align: middle">+</button></td></tr></table>';
                        $_data = '<button class="btn btn-success" name="ticket_'.$aRow['tbltickets.ticketid'].'" value="'.$aRow['tbltickets.ticketid'].'" onclick="surelato('.$aRow['tbltickets.ticketid'].')" style="margin: auto; vertical-align: middle">+</button>';
                    }
                    $row[] = $_data;
                }
//                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array(
//                    'data-name' => $aRow['name'],
//                    'onclick' => 'edit_service(this,' . $aRow['tbltickets.userid'] . '); return false;'
//                ));
//                $row[] = $options .= icon_btn('tickets/delete_service/' . $aRow['tbltickets.userid'], 'remove', 'btn-danger _delete');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = 'Relatório de Atendimentos';
        $data['begin_open'] = substr($yesterday, 0, 10);
        $data['end_open'] = substr($today, 0, 10);
        $data['begin_status'] = substr($date_from_status, 0, 10);
        $data['end_status'] = substr($date_to_status, 0, 10);
        $data['statuses'] = $this->tickets_model->get_ticket_status();
        $data['statuses']['callback_translate'] = 'ticket_status_translate';
        $data['status'] = $status;
        $this->load->view('admin/utilities/attendance_report', $data);
    }



    public function filter_by_userid($contact_id){

        return $this->attendance_report_model
            ->get_client_from_contact($contact_id);
    }

}