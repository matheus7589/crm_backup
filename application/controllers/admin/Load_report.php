<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 14/10/2017
 * Time: 10:16
 */

class Load_report extends Admin_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("load_report_model");
        $this->load->model("tasks_model");

    }

    public function load(){
//        $data['report'] = $this->load_report_model->get_tasks();
        $data['staff'] = $this->load_report_model->get_staff();
        $data['title'] = "Relatório de Cargas";
        $this->load->view("admin/utilities/load_report", $data);
    }

    public function kan_ban_load(){
        $data['report'] = $this->tasks_model->do_kanban_query(1, $this->input->get('search'), 1, false, array(), array('sort_by'=>$this->input->get('sort_by'),'sort'=>$this->input->get('sort'), 'is_load_report'=>true));
        $data['staff'] = $this->load_report_model->get_staff();
        $this->load->view("admin/utilities/kan_ban_load_report", $data);
    }

}