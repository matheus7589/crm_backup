<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 26/10/2017
 * Time: 16:14
 */

class Attend_by_technician extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("attend_by_technician_model");
        $this->load->model("attend_average_time_model");
        $this->load->model('tickets_model');
    }

    public function show()
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        $where = "";
        if($this->input->post())
        {
            $wstatus = "";
            $filtro = $this->input->post();
            if($filtro['date_from'] != "" || $filtro['date_from'] != NULL)
                $date_from = \Carbon\Carbon::createFromFormat('d/m/Y', $filtro['date_from'])->format('Y-m-d');
            else
                if(PAINEL == INORTE){
                    $date_from = \Carbon\Carbon::now()->subDays(1);
                }else {
                    $date_from = "0000-00-00 00:00:00.000000";
                }

            if($filtro['date_to'] != "" || $filtro['date_to'] != NULL)
                $date_to = \Carbon\Carbon::createFromFormat('d/m/Y', $filtro['date_to'])->format('Y-m-d');
            else
                $date_to = \Carbon\Carbon::now();

            if($filtro['status'] != "" || $filtro['status'] != NULL)
                $wstatus = "AND status = ".$filtro['status'];

            $where = " AND tbltickets.date BETWEEN '".$date_from."' AND '".$date_to."' ".$wstatus;
        }else{
            if (PAINEL == INORTE){
                $date_from = \Carbon\Carbon::now()->subDays(1);
                $date_to = \Carbon\Carbon::now();
                $where = " AND tbltickets.date BETWEEN '".$date_from."' AND '".$date_to."' ";
            }
        }
//        BL
        if(PAINEL == QUANTUM) {
            $data['assigneds'] = $this->db->query("SELECT DISTINCT CONCAT(tblstaff.firstname,' ',tblstaff.lastname) 
            AS name,(SELECT COUNT(*) FROM tbltickets WHERE tbltickets.assigned = tblstaff.staffid" . $where . ") 
            AS numatt,staffid FROM tbltickets INNER JOIN tblstaff ON tbltickets.assigned = tblstaff.staffid 
            WHERE tblstaff.partner_id = " . get_staff_partner_id() . $where . " ORDER BY 2 DESC ")->result_array();
        }else{
            $data['assigneds'] = $this->staff_model->get_staff($where);
        }
        $data['title'] = 'Relatório de Atendimentos';
        $data['status'] = $this->attend_by_technician_model->get_ticket_status();
        $data['status']['callback_translate'] = 'ticket_status_translate';
        $data['statuses'] = $this->tickets_model->get_ticket_status();
        $this->load->view('admin/utilities/attend_by_technician', $data);
    }

    public function get_attends($from, $to, $status){
        $staff = $this->attend_average_time_model->get_technicians();

        $retorno = array();

        foreach ($staff as $staff_key => $staff_value){
            $atendimento = 0;

            $tickets = $this->attend_by_technician_model->get_tickets($staff_value['staffid'], $from, $to, $status);

            foreach ($tickets as $key => $value){
                if($status != ''){
                    if($value['statusid'] == $status) {
                        $atendimento++;
                    }
                }else{
                    if($value['statusid'] == 2){
                        $atendimento++;
                    }
                }
            }

            array_push($retorno, array(
                'atendimento' => $atendimento,
                'staffid' => $staff_value['staffid'],
            ));
        };

        return $retorno;

    }

    function compare_attend($a, $b)
    {
        return $b[1] - $a[1];
    }

}