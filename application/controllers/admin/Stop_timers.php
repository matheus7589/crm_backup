<?php


class Stop_timers extends Admin_controller
{

    public function __construct()
    {

        parent::__construct();
        if (!is_admin()) access_denied('Taks');
        $this->load->model('timerstop_model');


    }

    public function create()
    {
        permission_partner();

        $data['timers'] = $this->timerstop_model->all();
        $this->load->helper("dias_semana");

        $this->load->view('admin/tasks/stop_timers', $data);
    }


    public function stop()
    {
        $this->timerstop_model->stop_all_timers();
        set_alert("success", "Todas tarefas foram paradas");
        redirect(admin_url('tarefas/parada/'));
    }


    public function store()
    {


        $this->load->library('form_validation');
        $regex = "/[0-9]{2}:[0-9]{2}/";


        $this->form_validation->set_rules(
            [
                [
                    'field' => 'day[]',
                    'label' => 'Horário de Parda',
                    'rules' => "required",
                ],
                [
                    'field' => 'stoptimer',
                    'label' => 'Horário de Parda',
                    'rules' => "trim|required|regex_match[$regex]",
                ]
            ]
        );

        if ($this->form_validation->run() == false) {
            set_alert("danger", "Erro, Confira os campo e tente novamente");
            redirect(admin_url('tarefas/parada/'));
        }


        $days = [];
        $daysTemp = $this->input->post("day");


        for ($i = 0; $i <= 7; $i++) {
            if (isset($daysTemp[$i])) {
                if ($daysTemp[$i] >= 1 && $daysTemp[$i] <= 7) {
                    $days[] = $daysTemp[$i];
                }

            }
        }


        if ($this->timerstop_model->set_days($days)
            ->set_stoptimers($this->input->post("stoptimer"))
            ->set_staffid(get_staff_user_id())
            ->insert()) {

            set_alert("success", "Parada  de Tarefas cadastrada com sucesso");
        } else {
            set_alert("success", "Erro ao cadastrar parada de tarefas");
        }

        redirect(admin_url('tarefas/parada/'));
    }


    public function destroy($id)
    {

        if ($this->timerstop_model->set_settimerstopid($id)->delete()) {
            set_alert("success", "Parada de tarefas deletada com sucesso");;
        } else {
            set_alert("success", "Erro ao deletar parada de tarefas");
        }

        redirect(admin_url('tarefas/parada/'));
    }


}