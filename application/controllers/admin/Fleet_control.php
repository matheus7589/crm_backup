<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 27/12/2017
 * Time: 13:14
 */

class Fleet_control extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        if (!has_permission('fleet'))
            access_denied('fleet');
        $this->load->model('fleet_control_model');
    }

    public function index()
    {
        $data['title'] = "Controle de frota";
        if(PAINEL == INORTE) {
            $data['staff'] = $this->db->select("staffid ,CONCAT(firstname,' ',lastname) as name")
                ->where('doc_cnh_isenabled = 1 and doc_cnh_num is not null and doc_cnh_categorycnh is not null and doc_cnh_validade > CURDATE()')
                ->get("tblstaff")->result_array();
        }else{
            $data['staff'] = $this->db->select("staffid ,CONCAT(firstname,' ',lastname) as name")->get("tblstaff")->result_array();
        }
        $data['vehicles'] = $this->db->get("tblfleetvehicles")->result_array();
        $data['fleetout'] = $this->db->query("SELECT *,(SELECT `descricao` FROM tblfleetvehicles WHERE tblfleetvehicles.vehicleid = tblfleetout.vehicleid) as vname FROM `tblfleetout` WHERE state = 0")->result_array();
        $this->load->view("admin/fleet_control/manager",$data);
    }

    //Veículo
    public function vehicles()
    {
        $data['vehicles'] = $this->db->query("SELECT vehicleid,descricao,active,placa,tipo FROM `tblfleetvehicles`")->result_array();
        $data['title'] = "Veículos";
        $data['staff'] = $this->db->query("SELECT staffid ,CONCAT(firstname,' ',lastname) as name FROM tblstaff")->result_array();
        $this->load->view("admin/fleet_control/vehicles", $data);
    }

    public function change_vehicle_status($id,$state)
    {
        if(!has_permission('fleet','','edit'))
            access_denied('fleet');
        unset($data);
        $data['active'] = $state;
        $this->db->where('vehicleid',$id);
        $this->db->update('tblfleetvehicles',$data);
        if($this->db->affected_rows()>0)
            logActivity("Status do veículo alterado. [VéiculoID: ".$id."]");
    }

    public function vehicles_single($id)
    {
        if(!has_permission('fleet','','edit'))
            access_denied('fleet');

        if ($this->input->get('date')) {
            if ($this->input->get('date_to') == "false")
                $date_to = Carbon\Carbon::now()->toDateString();
            else
                $date_to = to_sql_date($this->input->get('date_to'));
            if ($this->input->get('date_from') == "false")
                $date_from = '0000-00-00';
            else
                $date_from = to_sql_date($this->input->get('date_from'));

            echo json_encode($this->db->select("SUM(`km_final`-`km_inicial`) as distancia")
                ->where("vehicleid",$id)
                ->where('data BETWEEN "' . $date_from . '" AND "' . $date_to . '"')
                ->get("tblfleetout")
                ->row("distancia"));

            die();
        }
        $vehicle = $this->fleet_control_model->get_vehicles($id);
        if($vehicle)
        {
            $data['title'] = "Informações do veículo";
            $data['vehicle'] = $vehicle;
            $data['vehicles'] = $this->db->query("SELECT vehicleid,descricao,placa,tipo FROM `tblfleetvehicles`")->result_array();
            $data['staff'] = $this->db->query("SELECT staffid ,CONCAT(firstname,' ',lastname) as name FROM tblstaff")->result_array();
            $data['outputs'] = $this->db->where("vehicleid",$id)->order_by("data DESC")->get("tblfleetout")->result_array();
            $data['supplys'] = $this->db->where("vehicleid",$id)->get("tblfleetsupply")->result_array();
            $this->load->view("admin/fleet_control/vehicles_single",$data);
        }
        else
        {
            set_alert('danger', 'Veículo não encontrado.');
            header('location:' . admin_url('fleet/vehicles'));
        }
    }
    public function add_vehicle()
    {
        if(!has_permission('fleet','','create'))
            access_denied('fleet');
        if($this->input->post())
        {
            $data = $this->input->post();
            if($this->fleet_control_model->add_vehicle($data))
                set_alert('success', 'Veículo registrado com sucesso!');
            else
                set_alert('danger', 'Erro ao registrar veículo.');
            header("location:".admin_url("fleet/vehicles"));
        }
    }

    public function update_vehicle()
    {
        if(!has_permission('fleet','','edit'))
            access_denied('fleet');

        if($this->input->post())
        {
            $data = $this->input->post();
            $vid = $data['vehicleid'];
            unset($data['vehicleid']);
            $this->db->where("vehicleid",$vid)->update("tblfleetvehicles", $data);
            if($this->db->affected_rows()>0) {
                set_alert('success', 'Veículo atualizado com sucesso!');
                logActivity("Veículo editado. [VéiculoID: ".$vid."]");
            }
            else
                set_alert('danger', 'Erro ao atualizar veículo.');
        }
        header("location:".admin_url("fleet/vehicles/".$vid));
    }

    public function delete_vehicle()
    {
        if(!has_permission('fleet','','delete'))
            access_denied('fleet');
        if($this->input->post())
        {
            $data = $this->input->post();
            $vehicleid = $data['vehicleid'];
            if($this->fleet_control_model->delete_vehicle($vehicleid))
                set_alert('success', 'Veículo excluido com sucesso!');
            else
                set_alert('danger', 'Erro ao excluir veículo.');
            header("location:".admin_url("fleet/vehicles"));
        }
    }

    //Saídas
    public function out_verification($data)
    {
        //Vencimento documentação motorista
        if(is_numeric($data['staffid'])) {
            $staff = $data['staffid'];
            $habstaff = $this->db->select("doc_cnh_isenabled,doc_cnh_validade")->where("staffid", $staff)->get("tblstaff")->row();
//            echo json_encode($habstaff);
            if ($habstaff->doc_cnh_isenabled == 0)
                return array("status" => false, "message" => "Erro ao registrar saída.</br>O Colaborador não é habilitado.", "type" => "danger");
            if (strtotime(\Carbon\Carbon::parse($habstaff->doc_cnh_validade)) < strtotime(\Carbon\Carbon::now()))
                return array("status" => false, "message" => "Erro ao registrar saída.</br>CNH do Colaborador Vencido.", "type" => "danger");

            if (\Carbon\Carbon::parse($habstaff->doc_cnh_validade)->year == \Carbon\Carbon::now()->year) {
                if (\Carbon\Carbon::parse($habstaff->doc_cnh_validade)->month == \Carbon\Carbon::now()->month)
                    return array("status" => true, "message" => "CNH do Colaborador vence este mês.", "type" => "warning");
                if (\Carbon\Carbon::parse($habstaff->doc_cnh_validade)->month - 1 == \Carbon\Carbon::now()->month)
                    return array("status" => true, "message" => "CNH do Colaborador vence mês que vem.", "type" => "warning");
            }
        }

        return array("status"=>true);
    }

    public function out_delete()
    {
        if($this->input->post())
        {
            $outid = $this->input->post()["outiddelete"];
            $saida = $this->db->where('idsaida', $outid)->get('tblfleetout')->row();
            $this->db->where('idsaida', $outid)->delete('tblfleetout');
            if ($this->db->affected_rows()>0){
                set_alert('success', 'Saída deletada com sucesso.');
                logActivity("Exclusão de Saída Fota [Motivo: ".$saida->motivo.". Obs: ".$saida->obs.".]");
            }
            else
                set_alert('danger', 'Erro ao deletar saída.');
            header('location:' . admin_url('fleet'));
        }
    }

    public function fleet_out()
    {
        if(!has_permission('fleet','','create'))
            access_denied('fleet');
        if($this->input->post())
        {
            $data = $this->input->post();
//            echo json_encode($data);
            $verification = $this->out_verification($data);
            if($verification["status"])
            {
                if ($this->fleet_control_model->fleet_out($data))
                    set_alert($verification['type']??'success', 'Saída registrada com sucesso!</br>'.$verification['message']??'');
                else
                    set_alert('danger', 'Erro ao registrar saída.');
            }
            else
            {
                set_alert($verification['type'],$verification['message']);
            }
            header('location:' . admin_url('fleet'));
        }
    }

    //Respostas para JS
    public function get_vehicle_out($vehicleid)
    {
        if(!has_permission('fleet','','view'))
            access_denied('fleet');
        echo json_encode($this->db->query("SELECT *,DATE_FORMAT(data,'%d/%m/%Y') as dateform,DATE_FORMAT(datetime_inicial,'%d/%m/%Y %H:%i') as datetime_inicialform,DATE_FORMAT(datetime_final,'%d/%m/%Y %H:%i') as datetime_finalform FROM tblfleetout WHERE state = 1 AND vehicleid = ".$vehicleid)->row());
    }

    public function get_out($outid)
    {
        if(!has_permission('fleet','','view'))
            access_denied('fleet');
        if($this->input->post('motivo'))
        {
            $data = $this->input->post();
            if($data['data'])
                $data['data'] = to_sql_date($data['data'], false);
            else
                unset($data['data']);
            if($data['datetime_inicial'])
                $data['datetime_inicial'] = to_sql_date($data['datetime_inicial'], true);
            else
                unset($data['datetime_inicial']);
            if($data['datetime_final'])
                $data['datetime_final'] = to_sql_date($data['datetime_final'], true);
            else
                unset($data['datetime_final']);
            set_alert("danger", "Erro ao editar saída");
            $this->db->where("idsaida",$outid)->update('tblfleetout',$data);
            if($this->db->affected_rows() > 0) {
                set_alert("success", "Saída editada com sucesso!");
                logActivity("Saída editada. [ID: ".$outid."]");
            }
            header("location:".admin_url("fleet"));
            die();
        }
        $data['out'] = $this->db->query("SELECT *,DATE_FORMAT(data,'%d/%m/%Y') as dateform,DATE_FORMAT(datetime_inicial,'%d/%m/%Y %H:%i') as datetime_inicialform,DATE_FORMAT(datetime_final,'%d/%m/%Y %H:%i') as datetime_finalform FROM tblfleetout WHERE idsaida = " . $outid)->row();
        if ($this->input->is_ajax_request()) {
            echo json_encode($data['out']);
            die();
        }
        $data['vehicles'] = $this->db->get("tblfleetvehicles")->result_array();
        $data['outid'] = $outid;
        $data['staff'] = $this->db->select("staffid ,CONCAT(firstname,' ',lastname) as name")->get("tblstaff")->result_array();
        $this->load->view("admin/fleet_control/out_edit",$data);
    }

    public function get_relation_value(){
        $rel_id = $this->input->get('rel_id');
        $rel_type = $this->input->get('rel_type');
        $rel_data = get_relation_data($rel_type,$rel_id);
        $rel_val = get_relation_values($rel_data,$rel_type);
        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
    }

    public function get_vehicle($vehicleid)
    {
        if(!has_permission('fleet','','view'))
            access_denied('fleet');
        $data = $this->db->query("SELECT * FROM `tblfleetvehicles` WHERE vehicleid = ".$vehicleid)->row();
        $data->km_ultimo = $this->db->query("SELECT km_final FROM `tblfleetout` WHERE vehicleid = ".$vehicleid." ORDER BY `idsaida` DESC")->row("km_final");

        echo json_encode($data);
    }

    //Abastecimento
    public function add_supply()
    {
        if(!has_permission('fleet','','create'))
            access_denied('fleet');
        if($this->input->post())
        {
            $data = $this->input->post();
            if($this->fleet_control_model->add_supply($data))
                set_alert('success', 'Abastecimento registrado com sucesso!');
            else
                set_alert('danger', 'Erro ao registrar abastecimento.');
        }
        header('location:' . admin_url('fleet/vehicles'));
    }
}