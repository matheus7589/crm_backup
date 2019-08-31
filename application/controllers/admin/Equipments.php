<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 01/02/2018
 * Time: 16:36
 */

class Equipments extends Admin_controller
{
    public function index()
    {
        if(!has_permission('equipments','','view'))
            access_denied('equipments');
        if ($this->input->is_ajax_request())
            $this->perfex_base->get_table_data('equipments');

        $data["title"] = "Controle de Equipamentos";
        $partner = get_staff_partner_id();
        $data['equipments'] = $this->db->get("tblequipmentmodel")->result_array();
        $data['staffs'] = $this->db->select("staffid ,CONCAT(firstname,' ',lastname) as name")->where("active", 1)->where("partner_id",$partner)->get("tblstaff")->result_array();
        $this->load->view("admin/equipments/manager",$data);
    }

    public function index_patrimony()
    {
        if(!has_permission('equipments','','view'))
            access_denied('equipments');
        if($this->input->post())
        {
            if(!has_permission('equipments','','create'))
                access_denied('equipments');
            $_data = $this->input->post();
            $_data['data_aquisicao'] = to_sql_date($_data['data_aquisicao']);

            $this->db->insert("tblpatrimony_bens",$_data);
            //--------------
            $data['data_from'] = $_data['data_aquisicao'];
            $data["status"] = 0;
            $data["flag_out"] = 0;
            $data["staffid"] = NULL;
            $data["tipo"] = "Patrimônio";
            $data["description"] = $_data['descricao'];
            $data["patrimonyid"] = $this->db->insert_id();

            $this->db->insert("tblequipments_in",$data);
            //--------------

            if($this->db->affected_rows() > 0)
                set_alert("success","Patrimônio cadastrado com Sucesso!");
            else
                set_alert("danger","Erro ao cadastrar patrimônio.");

            header("location:".admin_url("equipmens/patrimony"));
        }
        $data['categories'] = $this->db->get("tblpatrimony_categories")->result_array();
        $data['title'] = "Controle Patrimonial";
        $this->load->view("admin/equipments/patrimony/manager", $data);
    }

    public function patrimony_category()
    {
        if(!has_permission('equipments','','create'))
            access_denied('equipments');
        $data["name"] = $this->input->post('name');
        $this->db->insert("tblpatrimony_categories",$data);

        if($this->db->affected_rows() > 0)
            set_alert("success","Categoria cadastrada com Sucesso!");
        else
            set_alert("danger","Erro ao cadastrar categoria.");

        header("location:".admin_url("equipmens/patrimony"));
    }

    public function addother()
    {
        if(!has_permission('equipments','','create'))
            access_denied('equipments');
        $data = $this->input->post();
        $this->db->insert("tblotherrelations",$data);
        $type = "danger";
        $message = "Não foi possível cadastrar";
        if($this->db->affected_rows() > 0)
        {
            $type = "success";
            $message = "Cadastrado com sucesso!";
        }
        echo json_encode(array("type"=>$type,"message"=>$message));
    }

    /**
     * Cadastra Entrada de equipamentos
     */
    public function add_equip_in()
    {
        if(!has_permission('equipments','','edit'))
            access_denied('equipments');
        $type = "danger";
        $message = "Não foi possível cadastrar Entrada";

        $data = $this->input->post();
        unset($data["equipment"]);
        $data['data_from'] = to_sql_date($data['data_from'],true);
        $data["status"] = 1;

        $this->db->insert("tblequipments_in",$data);
        if($this->db->affected_rows() > 0)
        {
            $type = "success";
            $message = "Cadastrado com sucesso!";
        }
        echo json_encode(array("type"=>$type,"message"=>$message));
    }

    /**
     * Cadastra Saída de equipamentos
     */
    public function add_equip_out()
    {
        if(!has_permission('equipments','','edit'))
            access_denied('equipments');
        $data = $this->input->post();

        $id = $data["equipment"];
        unset($data["equipment"]);
        $data["equipmentid"] = $id;
        $data_["flag_out"] = 1;

        $data['data_from'] = to_sql_date($data['data_from'],true);
        $data["description"] = $this->db->select("description")->where('equipinid', $id)->get("tblequipments_in")->row("description");
        $data['status'] = 1;
//        $data['flag_in_or_out'] = 1;//0 = Entrou | 1 = Saiu
        $this->db->insert("tblequipments_mov",$data);
        $this->db->where('equipinid', $id)->update('tblequipments_in',$data_);

        $type = "danger";
        $message = "Não foi possível cadastrar Saída";
        if($this->db->affected_rows() > 0)
        {
            $type = "success";
            $message = "Cadastrado com sucesso!";
        }
        echo json_encode(array("type"=>$type,"message"=>$message));
    }

    public function out_to_in()
    {
        if(!has_permission('equipments','','edit'))
            access_denied('equipments');
        $type = "danger";
        $message = "Erro ao realizar movimentação.";

        $equipid = $this->input->post("equipid");
        $outid = $this->input->post("outid");
        $data_["flag_out"] = 0;
        $data["status"] = "0";
        $data["data_to"] = \Carbon\Carbon::now()->toDateTimeString();

        $this->db->where('equipinid', $equipid)->update('tblequipments_in',$data_);
        $this->db->where('equipoutid', $outid)->update('tblequipments_mov',$data);
        if($this->db->affected_rows() > 0)
        {
            $type = "success";
            $message = "Movimentação realizada com Sucesso!";
        }
        echo json_encode(array("type"=>$type,"message"=>$message));
    }

    public function in_to_return()
    {
        if(!has_permission('equipments','','edit'))
            access_denied('equipments');
        $type = "danger";
        $message = "Erro ao realizar movimentação.";

        $equipid = $this->input->post("equipid");

        $data_["flag_out"] = 1;
        $data_["status"] = 0;
        $data_["data_to"] = \Carbon\Carbon::now()->toDateTimeString();

        $this->db->where('equipinid', $equipid)->update('tblequipments_in',$data_);
//        $this->db->where('equipoutid', $outid)->update('tblequipments_mov',$data);
        if($this->db->affected_rows() > 0)
        {
            $type = "success";
            $message = "Movimentação realizada com Sucesso!";
        }
        echo json_encode(array("type"=>$type,"message"=>$message));
    }

    public function patrimony_out($id,$state)
    {
        if(!has_permission('equipments','','edit'))
            access_denied('equipments');
        $data['status'] = $state;
        $this->db->where('patrimonyid',$id);
        $this->db->update('tblequipments_in',$data);
        if($this->db->affected_rows()>0) {
            if($state == 1)
                logActivity("Patrimônio adicionado aos item de saída. [PatrimônioID: " . $id . "]");
            else
                logActivity("Patrimônio removido dos item de saída. [PatrimônioID: " . $id . "]");
        }
    }
    public function add_equipments_models()
    {
        if($this->input->post())
        {
            $data_t = $this->input->post();
            $data['nome'] = $data_t['add_equipment'];
            $this->db->insert("tblequipmentmodel",$data);
            if($this->db->affected_rows() > 0)
                set_alert("success", "Equipamento adicionado com sucesso.");
            else
                set_alert("danger", "Erro ao cadastrar equipamento");
            header("location: " . admin_url('equipmens/add_equipments'));
        }
        $data['equipments'] = $this->db->get("tblequipmentmodel")->result_array();

        $this->load->view("admin/equipments/add_equipments",$data);
    }
}