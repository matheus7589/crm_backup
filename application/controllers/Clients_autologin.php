<?php
/**
 * Created by PhpStorm.
 * User: dejair
 * Date: 29/09/17
 * Time: 09:27
 */

class Clients_autologin extends Clients_controller
{


    public function autologin($cnpj, $cpf = '')
    {
        $this->load->model('Authentication_model');
        if (is_client_logged_in()) {
            $this->Authentication_model->logout(false);
        }

        $data = [];
        if($this->input->post())
            $data = $this->input->post();

        if($this->input->get()){
            $open_ticket = boolval($this->input->get('open_ticket'));
            $data['nome'] = $this->input->get('nome');
        }

        $success = $this->Authentication_model->loginauto($cpf, $cnpj, true, false, $data, $open_ticket);
        if (is_array($success) && isset($success['memberinactive'])) {
            set_alert('danger', _l('inactive_account'));
            redirect(site_url('clients/login'));
        } elseif ($success == false) {
            set_alert('danger', _l('client_invalid_username_or_password'));
            redirect(site_url('clients/login'));
        }
        do_action('after_contact_login');
        redirect(site_url());

        if (get_option('allow_registration') == 1) {
            $data['title'] = _l('clients_login_heading_register');
        } else {
            $data['title'] = _l('clients_login_heading_no_register');
        }
        $data['bodyclass'] = 'customers_login';

        $this->data = $data;
        $this->view = 'login';
        $this->layout();
    }

}