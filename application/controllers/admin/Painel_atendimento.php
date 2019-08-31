<?php


use Carbon\Carbon;

class Painel_atendimento extends Admin_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("painel_atedimento_model");
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
        $this->load->view("admin/painel_atendimento_" . PAINEL, $data);
    }

    public function data()
    {
        $this->load->helper("json_response");
        $painel_atedimento_model = $this->painel_atedimento_model;
        $response["atendimento_interno"] = $this->painel_atedimento_model->tikectsAtedimento([$painel_atedimento_model::ATEND_INTERNO, $painel_atedimento_model::ATEND_EXTERNO]);
        $response["atendimento_externo"] = $this->painel_atedimento_model->tikectsAtedimento($painel_atedimento_model::ATEND_EXTERNO);
        $response["suporte_pendente"] = $this->painel_atedimento_model->pending_sup($painel_atedimento_model::SUP_IMPLANTACAO);
        $response["em_espera"] = $this->painel_atedimento_model->ticketsEspera();
        $response["atendentes_disponiveis"] = $this->painel_atedimento_model->staffDisponiveis();
        $response["senha"] = password_day();

        json_response($response);
    }




}