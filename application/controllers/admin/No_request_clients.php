<?php
/**
 * Created by PhpStorm.
 * User: matheus.machado
 * Date: 26/03/2018
 * Time: 10:56
 */

class No_request_clients extends Admin_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("no_request_clients_model");
        $this->load->model("tickets_model");

    }

    public function show()
    {
        if (!is_admin()) {
            access_denied('Acesso à relatórios');
        }

        $sWhere = array();
        array_push($sWhere, " AND tblclients.partner_id = " . get_staff_partner_id() . " " );
        $sOrderBy = " ORDER BY DATEDIFF(CURRENT_DATE(), (SELECT lastreply from tbltickets WHERE tbltickets.userid = tblclients.userid ORDER BY tbltickets.ticketid DESC LIMIT 1)) DESC ";
        $plusJoin = " ";
        if ($this->input->post('dias') != null) {
            $value = $this->input->post('dias');
            if($value == 0){
                array_push($sWhere, " AND DATEDIFF(CURRENT_DATE(), (SELECT lastreply from tbltickets WHERE tbltickets.userid = tblclients.userid ORDER BY tbltickets.ticketid DESC LIMIT 1)) = 0 AND active = 1 ");
            }else {
                array_push($sWhere, " AND DATEDIFF(CURRENT_DATE(), (SELECT lastreply from tbltickets WHERE tbltickets.userid = tblclients.userid ORDER BY tbltickets.ticketid DESC LIMIT 1)) <= " . $this->input->post('dias') . " AND active = 1 ");
            }
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblclients.userid',
                'company',
                'tblclients.phonenumber',
                '2',
                '3',
            );
            $sIndexColumn = "userid";
            $sTable = 'tblclients';
            $sJoin = array(
                " join tbltickets on tbltickets.userid = tblclients.userid " . $plusJoin . " and tblclients.active=1 ",
            );
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $sJoin, $sWhere, array(
                'DATEDIFF(CURRENT_DATE(), (SELECT lastreply from tbltickets WHERE tbltickets.userid = tblclients.userid ORDER BY tbltickets.ticketid DESC LIMIT 1)) as diff',
                '(SELECT lastreply from tbltickets WHERE tbltickets.userid = tblclients.userid ORDER BY tbltickets.ticketid DESC LIMIT 1) as lastreply',
            ), ' GROUP BY tbltickets.userid ' . $sOrderBy);
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {


                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'tblclients.userid') {
                        $_data = '<div class="row">';
                        $_data .= '<div class=" col-md-12">';
                        $_data .= $aRow['tblclients.userid'];
                        $_data .= '</div>';
                        $_data .= '</div>';
                    }
                    if ($aColumns[$i] == "company") {
                        $_data = '<div class="row">';
                        $_data .= '<div class=" col-md-12">';
                        $_data .= '<a href="' . admin_url('clients/client/' . $aRow['tblclients.userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
//                        $_data .= $aRow['company'];
                        $_data .= '</div>';
                        $_data .= '</div>';
                    }
                    if ($aColumns[$i] == "3") {
                        if ($aRow['diff'] == null) {
                            $_data = _l('ticket_no_reply_yet');
                        }else {
                            $_data = $aRow['diff'] . ' dias';
                        }
                    }
                    if($aColumns[$i] == '2'){
                        if ($aRow['lastreply'] == null) {
                            $_data = _l('ticket_no_reply_yet');
                        } else {
                            $_data = _dt($aRow['lastreply']);
                            //$_data = time_ago_specific($aRow[$aColumns[$i]]);
                        }
                    }

                    if($aColumns[$i] == 'tblclients.phonenumber'){
                        if($aRow[$aColumns[$i]] == null){
                            $_data = 'Número não Informado';
                        }else {
                            $_data = $aRow['tblclients.phonenumber'];
                        }
                    }
                    $row[] = $_data;

                }
                $output['aaData'][] = $row;

            }
            echo json_encode($output);
            die();
        }

        $this->load->model("attend_average_time_model");
        $data['staff'] = $this->attend_average_time_model->get_technicians();
        $data['title'] = "Relatório de Clientes que não solicitaram suporte";
        $this->load->view('admin/utilities/no_request_clients', $data);
    }


}