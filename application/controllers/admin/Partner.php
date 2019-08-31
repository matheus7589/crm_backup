<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento 04
 * Date: 29/11/2017
 * Time: 14:41
 */

class Partner extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        if (!has_permission('partner'))
            access_denied('partner');
    }

    public function show()
    {
        permission_partner();

        if ($this->input->is_ajax_request())
        {
            $this->perfex_base->get_table_data('partner');
        }
        $data['partners'] = $this->db->get("tblpartner")->result_array();
        $data['title'] = 'Parceiro';
        $this->load->view('admin/partner/manage', $data);
    }
    public function delete()
    {
        if (!has_permission('partner','','delete'))
            access_denied('partner');
        if($this->input->post())
        {
            $data = $this->input->post();
            $id = $data["id"];
            $dest_partner_id = ($data["partner_dest"]!="")?$data["partner_dest"]:0;
            $data1["partner_id"] = $dest_partner_id;
            if($id != 0)
            {
                $parceiro = $this->db->select("lastname")->where('partner_id', $id)->get('tblpartner')->row()->lastname;
                $destname = $this->db->select("lastname")->where('partner_id', $dest_partner_id)->get('tblpartner')->row()->lastname;

                $this->db->where('partner_id', $id)->update("tblleads",$data1);
                logActivity("Leads de [".$parceiro."] trasferidos para [".$destname."].");
                $this->db->where('partner_id', $id)->update("tblclients",$data1);
                logActivity("Clientes de [".$parceiro."] trasferidos para [".$destname."].");
                $this->db->where('partner_id', $id)->update("tbltickets",$data1);
                logActivity("Tickets de [".$parceiro."] trasferidos para [".$destname."].");
                $this->db->where('partner_id', $id)->delete("tblstaff");
                logActivity("Colaboradores de [".$parceiro."] excluidos.");

                $this->db->where('partner_id', $id)->delete('tblpartner');
                if($this->db->affected_rows() > 0) {
                    set_alert('success', 'Parceiro deletado com sucesso.');
                    logActivity("Parceiro [".$parceiro."] excluido.");
                }
                else
                    set_alert('success', 'Erro ao deletar parceiro.');
                header('location:' . APP_BASE_URL . 'admin/partner');
            }
            else
            {
                set_alert('danger', 'Não é possível deletar principal.');
                header('location:' . APP_BASE_URL . 'admin/partner');
            }
        }
    }
    public function alter($id)
    {
        if (!has_permission('partner','','edit'))
            access_denied('partner');
        $this->load->model('partner_model');
        $partner = $this->partner_model->get($id);
        if (!$partner)
        {
            if($this->input->get() && PAINEL == INORTE){
                $name = $this->input->get('name');
                if($name === 'Todos'){
                    $_SESSION['all_partners'] = true;
                }else{
                    if(isset($_SESSION['all_partners'])){
                        unset($_SESSION['all_partners']);
                    }
                }
            }
            set_alert('danger', 'Parceiro não encontrado.');
            header('location:' . APP_BASE_URL . 'admin/partner');
        }
        else
        {
            if(isset($_SESSION['all_partners']) && PAINEL == INORTE){
                unset($_SESSION['all_partners']);
            }
            $_SESSION['partner_id'] = $id;
            logActivity("Mudou a seção para parceiro [".$partner[0]['firstname']."]");
            set_alert('success', 'Alterado para '.$partner[0]['firstname']);
            header('location:' . APP_BASE_URL . 'admin/partner');
        }
    }

    public function edit($id)
    {
        $this->load->model('partner_model');
        $data['title'] = 'Infomações Parceiro';
        $data['id'] = $id;

        $partner = $this->partner_model->get($id);
        $data['partner'] = $partner;
        if (!$partner)
        {
            blank_page('Parceiro não Encontrado');
        }
        if($this->input->post())
        {
            $up = $this->partner_model->update($this->input->post(), $id);
            if($up)
            {
                set_alert('success', 'Parceiro atualizado com sucesso.');
                header('location:' . APP_BASE_URL . 'admin/partner');
            }
            else
            {
                set_alert('success', 'Não foi possível atualizar informações do parceiro.');
                header('location:' . APP_BASE_URL . 'admin/partner/'.$id);
            }
        }
        $this->load->view('admin/partner/profile', $data);
    }

    public function add()
    {
        if (!has_permission('partner','','create'))
            access_denied('partner');
        if ($this->input->post() && !$this->input->is_ajax_request())
        {
            $this->load->model('partner_model');
            $datap = $this->input->post(null, false);
            if($this->partner_model->add($datap))
                set_alert("success","Parceiro adicionado com sucesso!");
            else
                set_alert("danger","Erro ao adicionar parceiro.");

            header("location: ".admin_url()."partner");
        }
        $data['title'] = 'Adicionar Parceiro';
        $this->load->view('admin/partner/add', $data);
    }
    public function change_partner_status($id,$state)
    {
        if (!has_permission('partner','','edit'))
            access_denied('partner');
        unset($data);
        $data['active'] = $state;
        $this->db->where('partner_id',$id);
        $this->db->update('tblpartner',$data);
        if($state == 1)
            $state = "Ativo";
        else
            $state = "Inativo";
        if($this->db->affected_rows() > 0)
            logActivity("Status do parceiro [IdParceiro: ".$id."] alterado para [".$state."].");
    }
}