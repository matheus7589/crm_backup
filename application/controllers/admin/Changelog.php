<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento 04
 * Date: 27/11/2017
 * Time: 12:10
 */

class Changelog extends Admin_controller
{
    public function show()
    {
        if($this->input->post())
        {
            $dataa = $this->input->post();
            $dataa['Data'] = to_sql_date($dataa['Data'], true);
            if($dataa['slug'] == "")
                $dataa['slug'] = NULL;
            /** Aqui começa a gambira */
            if($dataa['rel_type'] == 'external_link'){
                $dataa['slug'] = $dataa['rel_id'];
                $dataa['rel_id'] = 0;
            }

            $dataa['visible'] = false;
            /** Aqui termina a gambira */

            $this->db->insert('changelog', $dataa);
            if($this->db->affected_rows() > 0)
                set_alert("success","Inserido com sucesso!");
            else
                set_alert("danger","Erro ao inserir");
            header("location:".admin_url("utilities/changelog"));
        }

        $this->load->model("Knowledge_base_model");
        $data['title'] = 'Relatório de Mudanças';
        $data['versions'] = $this->db->query("SELECT DISTINCT Version, moduleid_fk FROM changelog ORDER BY Version DESC")->result();
        $data['modules'] = $this->db->order_by("name","ASC")->get("changelog_modules")->result_array();
        $data['knowledgebase'] = $this->Knowledge_base_model->get();
        $this->load->view('admin/utilities/changelog', $data);
    }

    public function addmodule()
    {
        $data['name'] = $this->input->post('name');
        $this->db->insert('changelog_modules', $data);
        if($this->db->affected_rows() > 0)
            set_alert("success","Módulo cadastrado com sucesso!");
        else
            set_alert("danger","Erro ao cadastrar módulo");
        header("location:".admin_url("utilities/changelog"));
    }

    public function edit()
    {
        if(!has_permission('utilities', '', 'edit')){
            access_denied('utilities');
        }
        if($this->input->post())
        {

            if($this->input->get("tipo") == "edit_module")
            {
                $data = $this->input->post();
                if($data != null){
                    $id = $data['moduleid'];
                    unset($data['moduleid']);
                    $this->db->where('moduleid', $id)->update('changelog_modules', $data);
                    if ($this->db->affected_rows() > 0)
                        echo json_encode(array("tipo" => "success", "msg" => "Editado com sucesso!"));
                    else
                        echo json_encode(array("tipo" => "danger", "msg" => "Não foi possível editar."));
                    die();
                }
            }
            if($this->input->get("tipo") == "edit")
            {
                $data = $this->input->post();
                $data['Data'] = to_sql_date($data['Data']);
                $id = $data['id'];
                if(isset($data['rel_type']) && $data['rel_type'] == 'external_link'){
                    $data['slug'] = $data['rel_id'];
                }
                if(isset($data['slug']) && $data['slug'] == "")
                    $data['slug'] = NULL;
                unset($data['id']);
                $this->db->where("change_id", $id)->update("changelog", $data);
                if ($this->db->affected_rows() > 0)
                    echo json_encode(array("tipo" => "success", "msg" => "Alterado com sucesso!"));
                else
                    echo json_encode(array("tipo" => "danger", "msg" => "Não foi possível alterar."));
                die();
            }
            else if($this->input->get("tipo") == "delete")
            {
                $data = $this->input->post();
                $id = $data['id'];
                $this->db->where("change_id", $id)->delete("changelog");
                if ($this->db->affected_rows() > 0) {
                    echo json_encode(array("tipo" => "success", "msg" => "Deletado com sucesso!"));
                    set_alert("success","Deletado com sucesso!");
                }
                else
                    echo json_encode(array("tipo" => "danger", "msg" => "Falha ao deletar."));
                die();
            }

            else if($this->input->get("tipo") == "delete_module")
            {
                $data = $this->input->post();
                $id = $data['moduleid'];
                $this->db->where('moduleid_fk', $id)->delete('changelog');
                if ($this->db->affected_rows() > 0) {
                    $this->db->where('moduleid', $id)->delete('changelog_modules');
                    if ($this->db->affected_rows() > 0) {
                        echo json_encode(array("tipo" => "success", "msg" => "Deletado com sucesso!"));
                        set_alert("success","Deletado com sucesso!");
                    }
                    else
                        echo json_encode(array("tipo" => "danger", "msg" => "Falha ao deletar."));
                    die();
                }

            }
        }
        $this->load->model("Knowledge_base_model");
        $data['versions'] = $this->db->query("SELECT DISTINCT Version, moduleid_fk FROM changelog ORDER BY Version DESC")->result();
        $data['modules'] = $this->db->order_by("name","ASC")->get("changelog_modules")->result_array();
        $data['knowledgebase'] = $this->Knowledge_base_model->get();
        $this->load->view("admin/changelog/edit",$data);
    }

    public function change_log_status($id, $state){
        unset($data);
        $data['visible'] = $state;
        $this->db->where('change_id', $id);
        $this->db->update('changelog', $data);
        if($this->db->affected_rows() > 0)
            logActivity("Status do registro alterado. [LogID: ".$id."]");
    }


    public function bulk_action()
    {
        if ($this->input->post()) {
            $total_deleted = 0;
            $ids = $this->input->post('ids');
            $autor = $this->input->post('autor');
            $data = $this->input->post('data');
            $tipo = $this->input->post('tipo');
            $version = $this->input->post('version');
            $slug = $this->input->post('slug');
            $moduleid_fk = $this->input->post('moduleid_fk');
            $rel_type = $this->input->post('rel_type');
            $rel_id = $this->input->post('rel_id');
            $visible = $this->input->post('visible');
            $is_admin = is_admin();

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        $this->db->where('change_id', $id)->delete('changelog');
                            if ($this->db->affected_rows() > 0) {
                                $total_deleted++;
                            }
                    } else {
                        if ($autor) {

                            $this->db->where('change_id', $id);
                            $this->db->update('changelog', array(
                                'Autor' => $autor
                            ));
                        }
                        if ($data) {
                            $this->db->where('change_id', $id);
                            $this->db->update('changelog', array(
                                'Data' => to_sql_date($data)
                            ));
                        }
                        if ($tipo) {
                            $this->db->where('change_id', $id);
                            $this->db->update('changelog', array(
                                'Tipo' => $tipo
                            ));
                        }

                        if ($version) {
                            $this->db->where('change_id', $id);
                            $this->db->update('changelog', array(
                                'Version' => $version
                            ));
                        }

                        if ($slug) {
                            $this->db->where('change_id', $id);
                            $this->db->update('changelog', array(
                                'slug' => $slug
                            ));
                        }

                        if ($moduleid_fk) {
                            $this->db->where('change_id', $id);
                            $this->db->update('changelog', array(
                                'moduleid_fk' => $moduleid_fk
                            ));
                        }

                        if($rel_type){
                            $this->db->where('change_id', $id);
                            $this->db->update('changelog', array(
                                'rel_type' => $rel_type
                            ));
                        }

                        if($rel_id){
                            $this->db->where('change_id', $id);
                            if($rel_type === 'external_link'){
                                $this->db->update('changelog', array(
                                    'slug' => $rel_id
                                ));
                            }else{
                                $this->db->update('changelog', array(
                                    'rel_id' => $rel_id
                                ));
                            }
                        }

                        if ($visible === '1' || $visible === '0') {
                            $this->db->where('change_id', $id);
                            $this->db->update('changelog', array(
                                'visible' => $visible
                            ));
                        }
                    }
                }
            }

            if ($this->input->post('mass_delete')) {
                set_alert('success', _l('total_tickets_deleted', $total_deleted));
            }
        }
    }

    public function get_relation_value(){
        $rel_id = $this->input->get('rel_id');
        $rel_type = $this->input->get('rel_type');
        $rel_data = get_relation_data($rel_type,$rel_id);
        $rel_val = get_relation_values($rel_data,$rel_type);
        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
    }

}