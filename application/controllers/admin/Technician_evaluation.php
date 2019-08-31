<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 14/12/2017
 * Time: 16:39
 */
defined('BASEPATH') or exit('No direct script access allowed');
class Technician_evaluation extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("technician_evaluation_model");
    }

    public function show(){

        if ($this->input->is_ajax_request()) {
            $dateW = "";
            if ($this->input->get('date')) {
                if ($this->input->get('date_to') == "false")
                    $date_to = Carbon\Carbon::now()->toDateString();
                else
                    $date_to = to_sql_date($this->input->get('date_to'));
                if ($this->input->get('date_from') == "false")
                    $date_from = '0000-01-01';
                else
                    $date_from = to_sql_date($this->input->get('date_from'));

                $dateW = 'date BETWEEN "' . $date_from . '" AND "' . $date_to . '"';
                $periodo = "Exibindo de ".Carbon\Carbon::parse($date_from)->format("d/m/Y")." até ".Carbon\Carbon::parse($date_to)->format("d/m/Y");
            }
            else
                $periodo = "Exibindo todos os registros";
            if ($this->input->get('staff')) {
                $staff = $this->input->get('staff');
                if($dateW != "")
                    $dateW .= " AND ";
                $notas = $this->db->select("nota_tecnico")->distinct()->where("nota_tecnico IS NOT NULL AND ".$dateW."assigned = ".$staff."")->order_by("nota_tecnico DESC")->get("tbltickets")->result_array();
                $attends = array();
                //Eu coloquei essas consultas dentro do laço pq foi o único jeito de funcionar essa bagaça
                //Se tiver pesando(pq será no máximo 10 consultas), tentar fazer em uma consulta só
                //Codigo funciona +- SELECT DISTINCT `nota_tecnico` as nota_t,(SELECT descricao FROM nota_atendimento WHERE nota_atendimento.nota = tbltickets.nota_tecnico) as descric, (SELECT span_color FROM nota_atendimento WHERE nota_atendimento.nota = tbltickets.nota_tecnico) as color, (SELECT COUNT(tbltickets.nota_tecnico) FROM tbltickets WHERE tbltickets.nota_tecnico = nota_t AND ".$dateW."assigned = ".$staff.") as numero FROM tbltickets WHERE ".$dateW."`nota_tecnico` IS NOT NULL AND assigned = ".$staff." ORDER BY nota_t DESC
                foreach ($notas as $nota)
                    array_push($attends,$this->db->query("SELECT span_color,descricao, (SELECT count(nota_tecnico) FROM tbltickets WHERE nota_tecnico = ".$nota['nota_tecnico']." AND ".$dateW."assigned = ".$staff.") as numero FROM nota_atendimento WHERE nota = ".$nota['nota_tecnico'])->row());
                $result = "<div class='panel-body' id='headcabe'><h4 style='margin-top: 0px; margin-bottom: 0px;'>".$periodo." de [".get_staff_full_name($staff)."]<hr>";
                foreach ($attends as $attend)
                {
                    $result .= "<span style='font-size: 100%; background: inherit;' class='label label-".$attend->span_color."'>".$attend->descricao." = ".$attend->numero."</span>&nbsp;&nbsp;&nbsp;";
                }
                $result .= "</h4></div>";
                $title = $result;
                $result .= "<hr>";
//                $tickets = $this->db->select("  ")->get("");
                $result .= "<div class='panel-body'><table class='table dt-table' id='subconsulta-table' style='margin-top: 0px;'>";
                $result .= "<thead><tr><th>TicketID</th><th>Assunto</th><th>Atendente</th><th>Avaliação do Técnico</th><th>Status</th><th>Data</th></tr></thead><tbody>";

                $tickets = $this->db->where($dateW."assigned = ".$staff)->where("nota_tecnico IS NOT NULL")->order_by("ticketid","DESC")->get("tbltickets")->result_array();
                foreach ($tickets as $ticket)
                {
                    $result .= "<tr><th><a href='".admin_url("tickets/ticket/".$ticket['ticketid'])."'> ".$ticket['ticketid']."</a></th><th>".get_nota_formated($ticket['nota_atendimento'],true).$ticket['subject']."</th><th>".get_staff_full_name($ticket['assigned'])."</th><th>".get_nota_formated($ticket['nota_tecnico'])."</th><th>".ticket_status_translate($ticket['status'])."</th><th>".Carbon\Carbon::parse($ticket['date'])->format("d/m/y H:i:s")."</th></tr>";
                }
                $result .= "</tbody></table><i class='fa fa-bookmark'><span style='font-style: italic;'> Avaliação Geral - Passe o mouse para detalhes</span></i></div>";
                echo $result;
                die();
            }
            if($this->input->get("type") == "destaque")
            {
                $data['featured'] = $this->technician_evaluation_model->featured_support($dateW);
                echo json_encode($data['featured']);
                die();
            }
            else {
                $aColumns = array(
                    'tbltickets.assigned',
                    'CONCAT(firstname," ",lastname)',
                    'AVG(nota_atendimento)',
                    'count(tbltickets.assigned)',
                    '1'
                );
                $sIndexColumn = "assigned";
                $sTable = 'tbltickets';
                $sJoin = array(
                    " join tblstaff on tblstaff.staffid = tbltickets.assigned ",
                );
                if(PAINEL == QUANTUM)
                    $sWhere = array(" WHERE tbltickets.nota_atendimento is not null");
                else
                    $sWhere = array(" WHERE tbltickets.nota_atendimento is not null AND assigned not in (0, 54, 4) ");
                if ($this->input->get('date')) {
                    array_push($sWhere, " AND ".$dateW);
                }
                if(PAINEL == QUANTUM)
                    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $sJoin, $sWhere, array(), " GROUP BY tbltickets.assigned");
                else
                    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $sJoin, $sWhere, array(), " GROUP BY tbltickets.assigned ORDER BY AVG(nota_atendimento) DESC ", 1);
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
                        else if ($aColumns[$i] == "1") {
                            $_data = '<button class="btn btn-success" onclick="detalhes('.$aRow['tbltickets.assigned'].'); return false;" style="margin: auto; vertical-align: middle">+</button>';
                        }
                        $row[] = $_data;
                    }

                    $output['aaData'][] = $row;
                }
                echo json_encode($output);
                die();
            }
        }

        $data['title'] = 'Avaliação do Atendimento';
        $data['featured'] = $this->technician_evaluation_model->featured_support();
        $this->load->view('admin/utilities/technician_evaluation', $data);
    }


}