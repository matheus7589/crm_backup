<?php
/**
 * Created by PhpStorm.
 * User: matheus.machado
 * Date: 17/05/2018
 * Time: 08:52
 */

class Painel_desenvolvimento extends Admin_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("painel_desenvolvimento_model");
        $this->load->model("attendance_evaluation_model");

    }


    public function index()
    {
        if (PAINEL == QUANTUM) {
            if (!is_admin()) {
                redirect(admin_url());
            }
        }
        $data['featured'] = $this->attendance_evaluation_model->get_media_geral();
        $this->load->view("admin/painel_desenvolvimento", $data);
    }

    public function data()
    {
        $this->load->helper("json_response");
        $painel_desenvolvimento_model = $this->painel_desenvolvimento_model;
        $response["atendimento_interno"] = $this->painel_desenvolvimento_model->pending_sup($painel_desenvolvimento_model::DEV_ATENDIMENTO);
        $response["atendimento_reanalise"] = $this->painel_desenvolvimento_model->pending_sup($painel_desenvolvimento_model::DEV_REANALISE);
        $response["suporte_pendente"] = $this->painel_desenvolvimento_model->pending_sup($painel_desenvolvimento_model::DEV_TESTE);
        $response["em_espera"] = $this->painel_desenvolvimento_model->naoIniciado();
        $response["atendentes_disponiveis"] = $this->painel_desenvolvimento_model->staffDisponiveis();
        $response["senha"] = password_day();

        json_response($response);
    }

}