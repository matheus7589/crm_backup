<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 14/12/2017
 * Time: 11:05
 */
defined('BASEPATH') or exit('No direct script access allowed');
class Attend_evaluation extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("attendance_evaluation_model");

    }

    public function show()
    {
        $dateW = "tbltickets.partner_id = ".get_staff_partner_id();
        if ($this->input->is_ajax_request()) {

            if ($this->input->get('date')) {
                if ($this->input->get('date_to') == "false")
                    $date_to = Carbon\Carbon::now()->toDateString();
                else
                    $date_to = to_sql_date($this->input->get('date_to') . ' 23:59:59', true);
                if ($this->input->get('date_from') == "false")
                    $date_from = '0000-01-01';
                else
                    $date_from = to_sql_date($this->input->get('date_from') . ' 00:00:01', true);

                $dateW .= ' AND date BETWEEN "' . $date_from . '" AND "' . $date_to . '"';
                $periodo = "Exibindo de ".Carbon\Carbon::parse($date_from)->format("d/m/Y")." até ".Carbon\Carbon::parse($date_to)->format("d/m/Y");
            }
            else
                $periodo = "Exibindo todos os registros";
            if ($this->input->get('client')) {
                $userid = $this->input->get('client');
                if($dateW != "")
                    $dateW .= " AND ";
                $attends = $this->db->query("SELECT DISTINCT `nota_atendimento` as nota,(SELECT descricao FROM nota_atendimento WHERE nota = nota_atendimento) as descric, (SELECT span_color FROM nota_atendimento WHERE nota = nota_atendimento) as color,(SELECT COUNT(nota_atendimento) FROM tbltickets WHERE nota_atendimento = nota AND ".$dateW."userid = ".$userid.") as numero FROM  tbltickets WHERE ".$dateW."`nota_atendimento` IS NOT NULL AND userid = ".$userid." ORDER BY nota DESC")->result_array();
                $result = "<div class='panel-body'><h4 style='margin-top: 0px; margin-bottom: 0px;'>".$periodo." de [ ".get_company_name($userid)." ]<hr>";
                foreach ($attends as $attend)
                {
                    $result .= "<span style='font-size: 100%; background: inherit;' class='label label-".$attend['color']."'>".$attend['descric']." = ".$attend['numero']."</span>&nbsp;&nbsp;&nbsp;";
                }
                $result .= "</h4></div>";
                $result .= "<hr>";
//                $tickets = $this->db->select("  ")->get("");
                $result .= "<div class='panel-body'><table class='table dt-table' id='subconsulta-table'  style='margin-top: 0px;'>";
                $result .= "<thead><tr><th>TicketID</th><th>Assunto</th><th>Atendente</th><th>Avaliação do Atendimento</th><th>Status</th><th>Data</th></tr></thead><tbody>";

                $tickets = $this->db->where($dateW."userid = ".$userid)->where("nota_atendimento IS NOT NULL")->order_by("ticketid","DESC")->get("tbltickets")->result_array();
                foreach ($tickets as $ticket)
                {
                    $result .= "<tr><td><a href='".admin_url("tickets/ticket/".$ticket['ticketid'])."'> ".$ticket['ticketid']."</a></td><td>".$ticket['subject']."</td><td>".get_nota_formated($ticket['nota_tecnico'],true).get_staff_full_name($ticket['assigned'])."</td><td>".get_nota_formated($ticket['nota_atendimento'])."</td><td>".ticket_status_translate($ticket['status'])."</td><td>".Carbon\Carbon::parse($ticket['date'])->format("d/m/y H:i:s")."</td></tr>";
                }
                $result .= "</tbody></table><i class='fa fa-bookmark'><span style='font-style: italic;'> Avaliação do Técnico - Passe o mouse para detalhes</span></i></div>";
                echo $result;
                die();
            }
            if($this->input->get("type") == "destaque")
            {
                $data['featured'] = $this->attendance_evaluation_model->get_media_geral($dateW);
                $attends = $this->db->query("SELECT DISTINCT nota_atendimento as nota, descricao, span_color, (SELECT COUNT(nota_atendimento) FROM tbltickets WHERE nota_atendimento = nota AND ".$dateW.") as numero FROM tbltickets INNER JOIN nota_atendimento ON tbltickets.nota_atendimento = nota_atendimento.id WHERE `nota_atendimento` IS NOT NULL AND ".$dateW." ORDER BY nota DESC")->result_array();
                $result = "<h4 style='margin-top: 0px; margin-bottom: 0px;'>";
//                $result = "";
                foreach ($attends as $attend)
                {
                    $result .= "<span style='font-size: 100%; background: inherit;' class='label label-".$attend['span_color']."'>".$attend['descricao']." = ".$attend['numero']."</span>&nbsp;&nbsp;&nbsp;";
                }
                $result .= "</h4>";
                if(PAINEL == INORTE)
                    echo json_encode(number_format($data['featured'], 2));
                else {
                    if($data['featured'] != false)
                        echo json_encode(array("nota" => $data['featured']->descricao, "span" => $data['featured']->span_color, "detalhado" => $result));
                    else
                        echo json_encode(false);
                }
                die();
            }
            else {
                $aColumns = array(
                    'tbltickets.userid',
                    'company',
                    'AVG(nota_atendimento)',
                    'count(tbltickets.assigned)',
                    '1'
                );
                $sIndexColumn = "userid";
                $sTable = 'tbltickets';
                $sJoin = array(
                    " join tblclients on tblclients.userid = tbltickets.userid ",
                );
                $sWhere = array(" WHERE tbltickets.nota_atendimento is not null ");
//                if ($this->input->get('date')) {
                    array_push($sWhere, " AND ".$dateW);
//                }
                if(PAINEL == INORTE)
                    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $sJoin, $sWhere, array(), " GROUP BY tbltickets.userid ORDER BY AVG(nota_atendimento) ASC ", 1);
                else
                    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $sJoin, $sWhere, array(), " GROUP BY tbltickets.userid");
                $output = $result['output'];
                $rResult = $result['rResult'];
                foreach ($rResult as $aRow) {
                    $row = array();
                    for ($i = 0; $i < count($aColumns); $i++) {
                        $_data = $aRow[$aColumns[$i]];
                        if ($aColumns[$i] == "AVG(nota_atendimento)") {
                            if(PAINEL == INORTE)
                                $_data = _format_number($aRow['AVG(nota_atendimento)'], 2);
                            else
                                $_data = $this->db->where("nota",round($aRow['AVG(nota_atendimento)']))->get("nota_atendimento")->row("descricao");
                        }
                        else if ($aColumns[$i] == "1")
                           $_data = '<button class="btn btn-success" onclick="detalhes('.$aRow['tbltickets.userid'].'); return false;" style="margin: auto; vertical-align: middle">+</button>';
                        $row[] = $_data;
                    }

                    $output['aaData'][] = $row;
                }
                echo json_encode($output);
                die();
            }
        }

        $data['title'] = 'Avaliação do Atendimento';
        $data['media_geral'] = $this->attendance_evaluation_model->get_media_geral("partner_id = ".get_staff_partner_id()."");
//        $data['media_cliente'] = $this->attendance_evaluation_model->get_media_atendimentos();
//        $data['attends'] = $this->db->query("SELECT DISTINCT `nota_atendimento` as notaatt, (SELECT descricao FROM nota_atendimento WHERE nota = nota_atendimento) as descric, (SELECT span_color FROM nota_atendimento WHERE nota = nota_atendimento) as color,(SELECT COUNT(nota_atendimento) FROM tbltickets WHERE notaatt = nota_atendimento) as numero FROM  tbltickets WHERE `nota_atendimento` IS NOT NULL ORDER BY nota DESC")->result_array();
        $data['attends'] = $this->db->query("SELECT DISTINCT nota_atendimento as nota, descricao, span_color, (SELECT COUNT(nota_atendimento) FROM tbltickets WHERE nota_atendimento = nota AND partner_id = ".get_staff_partner_id().") as numero FROM tbltickets INNER JOIN nota_atendimento ON tbltickets.nota_atendimento = nota_atendimento.id WHERE `nota_atendimento` IS NOT NULL AND partner_id = ".get_staff_partner_id()." ORDER BY nota DESC")->result_array();
        $this->load->view('admin/utilities/attendance_evaluation', $data);
    }



}