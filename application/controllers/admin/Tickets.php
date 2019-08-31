<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Tickets extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        if (get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member()) {
            redirect(admin_url());
        }
        $this->load->model('tickets_model');
    }

    public function show(){
        $data['title'] = "Tickets desenvolvimento";
        $data['chosen_ticket_status'] = 24;
        $this->load->model('departments_model');
        $data['staff_deparments_ids'] = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
        $data['default_tickets_list_statuses'] = do_action('default_tickets_list_statuses', array(24, 26));
        $this->load->view('admin/tickets/tickets_dev', $data);
    }

    public function set_temp_list_tickets(){
        $tempdata = array();
        $nome = $this->input->get('nome');
        if($this->input->get('value') == '' || $this->input->get('value') == 'my_tickets'){
            $value = $this->input->get('value');
            if($value == ''){
                $this->session->unset_tempdata($nome);
                $this->session->unset_tempdata('assigned');
                array_push($tempdata, '');
                $this->session->set_tempdata($nome, $tempdata, 300);
                die();
            }
        }else{
            $value = preg_replace("/[^0-9]/", "", $this->input->get('value'));
        }

        if($this->input->get('clear') != null && $this->input->get('clear') == 'true'){
            $this->session->unset_tempdata($nome);
        }

        if($this->session->tempdata($nome) != null){
            $tempdata = $this->session->tempdata($nome);
            if(!in_array($value, $tempdata)){
                array_push($tempdata, $value);
                $this->session->set_tempdata($nome, $tempdata, 300);
            }
//            $this->session->unset_tempdata($nome);
        }else {
            array_push($tempdata, $value);
            $this->session->set_tempdata($nome, $tempdata, 300);
        }
    }

    public function del_temp_list_tickets(){
        $nome = $this->input->get('nome');
        if($this->input->get('value') == '' || $this->input->get('value') == 'my_tickets'){
            $value = $this->input->get('value');
        }else{
            $value = preg_replace("/[^0-9]/", "", $this->input->get('value'));
        }

        if($this->session->tempdata($nome) != null){
            $tempdata = $this->session->tempdata($nome);
            if(in_array($value, $tempdata)) {
                $key = array_search($value, $tempdata);
                unset($tempdata[$key]);
                $this->session->set_tempdata($nome, $tempdata, 300);
            }
        }
    }

    public function index($status = '', $userid = '')
    {
        if ($this->input->is_ajax_request()) {
            if (!$this->input->post('filters_ticket_id')) {
                $tableParams = array(
                    'status' => $status,
                    'userid' => $userid
                );
            } else {
                // request for othes tickets when single ticket is opened
                $tableParams = array(
                    'userid' => $this->input->post('filters_userid'),
                    'where_not_ticket_id' => $this->input->post('filters_ticket_id')
                );
                if ($tableParams['userid'] == 0) {
                    unset($tableParams['userid']);
                    $tableParams['by_email'] = $this->input->post('filters_email');
                }
            }

            $this->perfex_base->get_table_data('tickets', $tableParams);
        }

            $data['chosen_ticket_status'] = $status;


        $data['weekly_tickets_opening_statistics'] = json_encode($this->tickets_model->get_weekly_tickets_opening_statistics());
        $data['title'] = _l('support_tickets');
        $this->load->model('departments_model');
        $data['statuses'] = $this->tickets_model->get_ticket_status();
        $data['staff_deparments_ids'] = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
        $data['departments'] = $this->departments_model->get();
        $data['priorities'] = $this->tickets_model->get_priority();
        $data['services'] = $this->tickets_model->get_service();
        $data['ticket_assignees'] = $this->tickets_model->get_tickets_assignes_disctinct();
        $data['bodyclass'] = 'tickets_page';
        $this->load->model("staff_model");
        $data['partners'] = $this->staff_model->getAllPartner();
        if(PAINEL == INORTE) {
            array_push($data['partners'], array(
//                'partner_id' => count($data['partners']),
                'partner_id' => strval($data['partners'][count($data['partners']) - 1]['partner_id'] + 1),
                'firstname' => 'Todos',
            ));
        }

        $default = array(1);

        if(PAINEL == QUANTUM  && $this->db->select("role")->where("staffid",get_staff_user_id())->get("tblstaff")->row("role") == "4") {
            $default = array('my_tickets', 1, 2, 18);
            $this->session->unset_userdata('default_tickets_list_statuses');
        }
        else if(PAINEL == QUANTUM)
            $default = array(1, 2, 4);

        if($this->session->tempdata('default_tickets_list_statuses') != null){
            $data['default_tickets_list_statuses'] = do_action('default_tickets_list_statuses', $this->session->tempdata('default_tickets_list_statuses'));
        }else{
            $data['default_tickets_list_statuses'] = do_action('default_tickets_list_statuses', $default);
        }
        $this->load->view('admin/tickets/list', $data);
    }

    public function add($userid = false)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['message'] = $this->input->post('message', false);
            $id = $this->tickets_model->add($data, get_staff_user_id(), '', 0);
            if ($id) {
                set_alert('success', _l('new_ticket_added_successfully', $id));
                redirect(admin_url('tickets/ticket/' . $id));
            }
        }
        if ($userid !== false) {
            $data['userid'] = $userid;
            $data['client'] = $this->clients_model->get($userid);
            //$data['contact'] = $this->clients_model->get_contacts($userid);
        }
        // Load necessary models
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');

        $data['departments'] = $this->departments_model->get();
        $data['predefined_replies'] = $this->tickets_model->get_predefined_reply();
        $data['priorities'] = $this->tickets_model->get_priority();
        $data['services'] = $this->tickets_model->get_service();
//        $data['staff'] = $this->staff_model->getSupp(true);
        if(isset($_SESSION['all_partners']) || get_staff_partner_id() == 0){
            $data['staff'] = $this->staff_model->getc('', 1, array(
                'key' => 'role',
                'value' => array(1, 4, 6, 7),
            ), false, false, true);
        }else{
            $data['staff'] = $this->staff_model->getc('', 1, array(
                'key' => 'role',
                'value' => array(1, 4, 6, 7),
            ), false, true, true);
        }

        $data['articles'] = $this->knowledge_base_model->get();
        $data['bodyclass'] = 'ticket';
        $data['title'] = _l('new_ticket');
        $this->load->view('admin/tickets/add', $data);

    }

    public function summary($cpf_cnpj = '', $chosen_ticket_status = '')
    {
        if ($this->input->get()) {
            $data['cpf_cnpj'] = $cpf_cnpj ?? '';
            $_SESSION['partner_filter'] = $cpf_cnpj;
            $data['chosen_ticket_status'] = $chosen_ticket_status ?? '';
            $data['default_tickets_list_statuses'] = array(1, 2, 4);
            $this->load->view('admin/tickets/summary', $data);
        }
    }

    public function take_attend(){

        $ticketid = $this->tickets_model->take_attend_ticket();
        $libera = $this->tickets_model->verifyNewTicket(get_staff_user_id());
        if(!$libera["status"]) {
            set_alert('danger', 'Limite de Tickets/Tempo atingido.</br>Fechar tickets aberto antes.');
            redirect(admin_url('tickets'));
        }
        if(!$ticketid){
            set_alert('info', 'Nenhum Ticket para assumir!');
        } else {

            $ticket = $this->tickets_model->get($ticketid);
            if ($ticket->priority == 6) {
                $this->db->where('ticketid', $ticketid);
                $this->db->update('tbltickets', array(
                    'priority' => 2,
                ));
            }

            $data['assign_to_current_user'] = true;
            $data['status'] = 2;
            $data['is_next_attend'] = false;
            $data['gambiarra_sinistra'] = get_staff_user_id();
            if(Carbon\Carbon::now()->hour < 12){
                $data['message'] = $this->tickets_model->get_predefined_reply(2)->message;
            }else{
                $data['message'] = $this->tickets_model->get_predefined_reply(1)->message;
            }
            $replyid = $this->tickets_model->add_reply($data, $ticketid, get_staff_user_id());

            if ($replyid) {
                set_alert('success', 'Ticket Assumido');
            } else {
                set_alert('warning', 'Problema ao Assumir Ticket');
            }

            redirect(admin_url('tickets/ticket/' . $ticketid));
        }
        redirect(admin_url('tickets'));
    }

    public function servicenv2($serviceid)
    {

        if ($this->input->get()) {
            $data = $this->input->get();
            //echo $data['service'];
            if ($data['service']) {
                $this->load->view('admin/tickets/servicenv2', $data);
            }

        } else {
            echo "Não encontrou o service";
        }
    }

    public function delete($ticketid)
    {
        //$auxiliar;
        if (!$ticketid) {
            redirect(admin_url('tickets'));
        }
        $response = $this->tickets_model->delete($ticketid);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('ticket')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_lowercase')));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function add_reply($task_id, $status){
            $this->load->model('tasks_model');
//            if($this->tasks_model->check_number_of_tasks($task_id, $status)) {
                $task = $this->tasks_model->get($task_id);
                switch ($status) {
                    case 1: // Em espera
                        $data['message'] = $this->tickets_model->get_predefined_reply(9)->message;
                        $data['status'] = 26; // DEV - Espera
                        break;
                    case 2: //  Reanalise
//                        $data['message'] = $this->tickets_model->get_predefined_reply(9)->message;
                        $data['message'] = 'Solicitação enviada para Reavaliação';
                        $data['status'] = 34; // DEV - Reanalise
                        break;
                    case 3: //  Em pausa
                        if($task->status == 1){
                            $data['message'] = $this->tickets_model->get_predefined_reply(9)->message;
                        }else{
                            $data['message'] = $this->tickets_model->get_predefined_reply(10)->message;
                        }
                        $data['status'] = 24; // DEV - TESTE
                        break;
                    case 4: // Em progresso
                        if ($task->status == 3 || $task->status == 1) {
                            $data['message'] = $this->tickets_model->get_predefined_reply(9)->message;
                        } else {
                            $data['message'] = $this->tickets_model->get_predefined_reply(13)->message;
                        }
                        $data['status'] = 33; // DEV - Atendimento
                        break;
                    case 5: // Completo
                        $data['message'] = $this->tickets_model->get_predefined_reply(4)->message;
                        $data['status'] = 32; // DEV - Atendido
//                        $data['status'] = 1; /** SUP - Espera */
//                        $data['priority'] = 5;
                        break;
                    default:
                        return false;
                }

                $ticket = $this->db->select('status')
                    ->where('ticketid', $task->rel_id)
                    ->get('tbltickets')->row();

                if($ticket->status == $data['status'])
                    die();

                $id = $this->tickets_model->add_reply($data, $task->rel_id, get_staff_user_id());
                if ($id) {
//                    return true;
                    echo json_encode(array(
                        'message' => 'status do ticket alterado',
                        'success' => true,
                    ));
                    die();
                }
//            }
//            return false;
            echo json_encode(array(
                'message' => 'não foi possível alterar o status do ticket',
                'success' => false,
            ));
            die();
    }

    public function add_contact($clientid, $contact_id = ''){
        $this->load->model('clients_model');
        $data['customer_id'] = $clientid;
        $data['contactid'] = $contact_id;
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['permissions'] = array('1', '5');
            unset($data['contactid']);
            $id = $this->clients_model->add_contact($data, $clientid);
            $message = '';
            $success = false;
            if ($id) {
                handle_contact_profile_image_upload($id);
                $success = true;
                $message = _l('added_successfully', _l('contact'));
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message,
                'has_primary_contact' => (total_rows('tblcontacts', array('userid' => $clientid, 'is_primary' => 1)) > 0 ? true : false)
            ));
            die;
        }

        if ($contact_id == '') {
            $title = _l('add_new', _l('contact_lowercase'));
        }

        $data['client'] = $this->clients_model->get_client($clientid);
        $data['customer_permissions'] = $this->perfex_base->get_contact_permissions();
        $data['title'] = $title;
        $this->load->view('admin/tickets/modals/add_contact', $data);
    }

    public function ticket($id)
    {
        if (!$id) {
            redirect(admin_url('tickets/add'));
        }

        $data['ticket'] = $this->tickets_model->get_ticket_by_id($id);

        if (!$data['ticket']) {
            blank_page(_l('ticket_not_found'));
        }

        if (get_option('staff_access_only_assigned_departments') == 1) {
            if (!is_admin()) {
                $this->load->model('departments_model');
                $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                if (!in_array($data['ticket']->department, $staff_departments)) {
                    set_alert('danger', _l('ticket_access_by_department_denied'));
                    redirect(admin_url('access_denied'));
                }
            }
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['message'] = $this->input->post('message', false);
            $replyid = $this->tickets_model->add_reply($data, $id, get_staff_user_id());

            if ($replyid) {
                set_alert('success', _l('replied_to_ticket_successfully', $id));
            }

            redirect(admin_url('tickets/ticket/' . $id));
        }
        // Load necessary models
        $this->load->model('clients_model');
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');
        $this->load->model('custom_fields_model');
        $this->load->model('nota_atendimento_model');


        $data['contact_id'] = $data['ticket']->contactid;
        $contato = $this->clients_model->get_contact($data['ticket']->contactid);
        $data['ticket']->nome_cliente = ($contato->firstname ?? "") . " " . ($contato->lastname ?? "");


        $cliente = $this->clients_model->get_client($data['ticket']->userid);

        $custom = $this->custom_fields_model->get_userid($data['ticket']->userid, 3);
        $revenda = $this->custom_fields_model->get_userid($data['ticket']->userid, 25);
        $data["client"] = $cliente;
        $data['ticket']->telCliente = $contato->phonenumber ?? null;
        $data['ticket']->nome_fantasia = $cliente->company ?? null;
        $data['ticket']->telEmpresa = $cliente->phonenumber ?? null;
        $data['ticket']->celEmpresa = $custom->value ?? null;
        $data['ticket']->revenda = $revenda->value ?? null;
        $data['ticket']->atendimento = $this->get_last_attend($data['ticket']->userid);
        $data['ticket']->validation = $this->tickets_model->get_validation();
        if (!empty($data['ticket']->scheduled_date))
            $data['ticket']->scheduled_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data['ticket']->scheduled_date)->format('d/m/Y H:i');

        $data['statuses'] = $this->tickets_model->get_ticket_status();
        if(!is_admin() && !has_permission('avaliacao_atendimento', '', 'view')){ // se pode avaliar o atendimento tambem pode colocar o status pra concluido kk
            if(PAINEL == INORTE) {
                foreach ($data['statuses'] as $key => $value) {
                    if ($value['ticketstatusid'] == '5')
                        unset($data['statuses'][$key]);
                }
            }
        }
        $data['statuses']['callback_translate'] = 'ticket_status_translate';

        $data['departments'] = $this->departments_model->get();
        $data['predefined_replies'] = $this->tickets_model->get_predefined_reply();
        $data['priorities'] = $this->tickets_model->get_priority_without_atualizacao_if_not_admin();
        $data['services'] = $this->tickets_model->get_service();
        $data['servicenv2'] = $this->tickets_model->get_secondservice();
        if(isset($_SESSION['all_partners']) || get_staff_partner_id() == 0){
            $data['staff'] = $this->staff_model->get('', 1, array(), false, false);
        }else{
            $data['staff'] = $this->staff_model->get('', 1, array(), false, true);
        }


        $data['articles'] = $this->knowledge_base_model->get();
        $data['ticket_replies'] = $this->tickets_model->get_ticket_replies($id);
        $data['bodyclass'] = 'top-tabs ticket';
        $data['title'] = $data['ticket']->subject;
        $data['ticket']->ticket_notes = $this->misc_model->get_notes($id, 'ticket');
        $data['ticket']->solicitantes = $this->clients_model->get_contacts($data['ticket']->userid, array(), true);

        $data['nota'] = $this->nota_atendimento_model->get_notas();
        $data['observacoes'] = $this->nota_atendimento_model->get_observacoes($id) ?? array();

        $data['server_info'] = $this->db->where('customer_id',$data['ticket']->userid)->where('description = "server_info"')->get('tblvault')->row();
        $this->load->view('admin/tickets/single', $data);
    }

    public function valida_dias()
    {
        $data['valida'] = $this->tickets_model->get_validation();
        $data['staff'] = $this->staff_model->getc('', 1, array("role" => 1), false, true);
        $this->load->view('admin/tickets/valida_dias', $data);
    }

    public function correcao($ticketid)
    {
        if($this->input->post())
        {
            $_data = $this->input->post();
            $affected = 0;
            $data['date'] = to_sql_date($_data['date'],true);
            $data['lastreply'] = to_sql_date($_data['lastreply'],true);
            unset($_data['lastreply']);
            unset($_data['date']);
            $this->db->where('ticketid',$ticketid)->update('tbltickets',$data);
            if ($this->db->affected_rows() > 0)
                $affected++;
            foreach ($_data as $idreply => $datum)
            {
                $this->db->where('id',explode("reply_",$idreply)[1])->update('tblticketreplies',array("date"=>to_sql_date($datum,true)));
                if ($this->db->affected_rows() > 0)
                    $affected++;
            }
            if ($affected > 0)
                set_alert('success', 'Datas atualizadas com sucesso!');
            else
                set_alert('danger', 'Erro ao atualizar datas!');
            redirect(admin_url('tickets/ticket/'.$ticketid));
        }
    }

    public function insere()
    {
        $data = $this->input->post();
        $insert_id = $this->db->insert('tblvalidadelastticket', array(
            'validation' => $data['numero']
        ));
        update_option('ticket_waiting_alert_time',$data['ticket_waiting_alert_time']);
        update_option('ticket_waiting_limit_time',$data['ticket_waiting_limit_time']);
        update_option('ticket_waiting_alert_sound',isset($data['ticket_waiting_alert_sound'])?$data['ticket_waiting_alert_sound']:"0");
        update_option('ticket_waiting_limit_sound',isset($data['ticket_waiting_limit_sound'])?$data['ticket_waiting_limit_sound']:"0");
        update_option('ticket_waiting_alert_sound_type',$data['ticket_waiting_alert_sound_type']);
        update_option('ticket_waiting_limit_sound_type',$data['ticket_waiting_limit_sound_type']);
        update_option('ticket_main_color',$data['ticket_main_color']);
        update_option('painel_refresh_time',$data['painel_refresh_time']);
        update_option('ticket_waiting_alert_time_attendance',$data['ticket_waiting_alert_time_attendance']);
        update_option('ticket_waiting_limit_time_attendance',$data['ticket_waiting_limit_time_attendance']);
        update_option('pertubacao_notify',$data['pertubacao_notify']?$data['pertubacao_notify']:"0");
        update_option('clients_predefined_assign', $data['assigned']);
//        logActivity(json_encode($data));
        if ($insert_id) {
            set_alert("success", "Inserido com sucesso!");
            redirect(admin_url('tickets/validacao'));
        }
    }

    public function edit_message()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['data'] = $this->input->post('data', false);
            if ($data['type'] == 'reply') {
                $this->db->where('id', $data['id']);
                $this->db->update('tblticketreplies', array(
                    'message' => $data['data']
                ));
            } elseif ($data['type'] == 'ticket') {
                $this->db->where('ticketid', $data['id']);
                $this->db->update('tbltickets', array(
                    'message' => $data['data']
                ));
            }
            if ($this->db->affected_rows() > 0) {
                set_alert('success', _l('ticket_message_updated_successfully'));
            }
            redirect(admin_url('tickets/ticket/' . $data['main_ticket']));
        }
    }

    public function delete_ticket_reply($ticket_id, $reply_id)
    {
        if (!$reply_id) {
            redirect(admin_url('tickets'));
        }
        $response = $this->tickets_model->delete_ticket_reply($ticket_id, $reply_id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('ticket_reply')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_reply')));
        }
        redirect(admin_url('tickets/ticket/' . $ticket_id));
    }

    public function change_status_ajax($id, $status)
    {
        if($this->input->is_ajax_request()) {
            if ($this->input->post()) {//Coloquei esse para funcionar o else
                if ($this->input->post("tipo") == "verifica") {
                    $rule = $this->tickets_model->rules_change_ticket_status($id, $status);
                    if ($rule != "true")
                        echo json_encode($rule);
                }
            } else {
                if ($this->tickets_model->ticketdev_check_rules($id, $status)) {
                    echo json_encode($this->tickets_model->change_ticket_status($id, $status));
                } else {
                    echo json_encode(array(
                        'alert' => 'warning',
                        'message' => 'A tarefa não possui este status no momento',
                    ));
                }
            }
        }
    }

    public function update_single_ticket_settings()
    {
        if ($this->input->post()) {
            $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_settings');
            $success = $this->tickets_model->update_single_ticket_settings($this->input->post());
            if ($success) {
                $this->session->set_flashdata('active_tab', true);
                $this->session->set_flashdata('active_tab_settings', true);
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $ticket = $this->tickets_model->get_ticket_by_id($this->input->post('ticketid'));
                    $this->load->model('departments_model');
                    $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    if (!in_array($ticket->department, $staff_departments) && !is_admin()) {
                        set_alert('success', _l('ticket_settings_updated_successfully_and_reassigned', $ticket->department_name));
                        echo json_encode(array(
                            'success' => $success,
                            'department_reassigned' => true
                        ));
                        die();
                    }
                }
                set_alert('success', _l('ticket_settings_updated_successfully'));
            }
            echo json_encode(array(
                'success' => $success
            ));
            die();
        }
    }

    // Priorities
    /* Get all ticket priorities */
    public function priorities()
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        $data['priorities'] = $this->tickets_model->get_priority();
        $data['title'] = _l('ticket_priorities');
        $this->load->view('admin/tickets/priorities/manage', $data);
    }

    /* Add new priority od update existing*/
    public function priority()
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->tickets_model->add_priority($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('ticket_priority')));
                }
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->tickets_model->update_priority($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('ticket_priority')));
                }
            }
            die;
        }
    }

    /* Delete ticket priority */
    public function delete_priority($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        if (!$id) {
            redirect(admin_url('tickets/priorities'));
        }
        $response = $this->tickets_model->delete_priority($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('ticket_priority_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('ticket_priority')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_priority_lowercase')));
        }
        redirect(admin_url('tickets/priorities'));
    }

    /* List all ticket predefined replies */
    public function predefined_replies()
    {
        if (!is_admin()) {
            access_denied('Predefined Replies');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'name'
            );
            $sIndexColumn = "id";
            $sTable = 'tblpredefinedreplies';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(
                'id'
            ));
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . admin_url('tickets/predefined_reply/' . $aRow['id']) . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }
                $options = icon_btn('tickets/predefined_reply/' . $aRow['id'], 'pencil-square-o');
                $row[] = $options .= icon_btn('tickets/delete_predefined_reply/' . $aRow['id'], 'remove', 'btn-danger _delete');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('predefined_replies');
        $this->load->view('admin/tickets/predefined_replies/manage', $data);
    }

    public function get_predefined_reply_ajax($id)
    {
        echo json_encode($this->tickets_model->get_predefined_reply($id));
    }

    public function get_last_attend($contato_id)
    {
        $ultimo_atendimento = $this->tickets_model->get_last_atendimento($contato_id);
        if (isset($ultimo_atendimento)) {
            $hora_Ticket = new DateTime($ultimo_atendimento->lastreply);
            $hora_Atual = new DateTime("now");
            $intervalo = $hora_Ticket->diff($hora_Atual);
            return $ultimo_atendimento = $intervalo->format("%d Dias");
        }
    }

    public function ticket_change_data()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('custom_fields_model');
            $contact_id = $this->input->post('contact_id');
            $client_contact = get_client_contact_by_id($contact_id);
            $contato = $this->clients_model->get_contact($client_contact->contactid);
            $ultimo_atendimento = $this->get_last_attend($client_contact->clientid);
            $validation = $this->tickets_model->get_validation();
            $client = $this->clients_model->get_client($client_contact->clientid);
            $solicitantes = $this->clients_model->get_contacts($client_contact->clientid);
            $solicitantes = $this->format_solicitantes($solicitantes);
            echo json_encode(array(
                'custom_data' => $this->custom_fields_model->get_userid($client_contact->clientid, 3),
                'custom_revenda' => (PAINEL == QUANTUM) ? $this->custom_fields_model->get_userid($client_contact->clientid, 25) : get_partner_name($client),
                'contact_data' => $contato,
                'validation' => $validation->validation,
                'ultimo_atendimento' => $ultimo_atendimento,
                'client_data' => $client,
                'client_partner' => get_partner_name($client->partner_id),
                'customer_has_projects' => customer_has_projects(get_user_id_by_contact_id($client_contact->contactid)),
                'solicitantes' => $solicitantes,
                'primary_contact' => $this->clients_model->get_contact($client->primary_contact),
            ));
        }
    }

    public function format_solicitantes($solicitantes){
        $result = '<option></option>';
        foreach($solicitantes as $solicitante){
            $result = $result . '<option value=' . $solicitante["id"] . '>' . $solicitante["firstname"] . ' ' . $solicitante['lastname'] .'</option>';
        }

        return $result;
    }

    /* Add new reply or edit existing */
    public function predefined_reply($id = '')
    {
        if (!is_admin()) {
            access_denied('Predefined Reply');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['message'] = $this->input->post('message', false);

            if ($id == '') {
                $id = $this->tickets_model->add_predefined_reply($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('predefined_reply')));
                    redirect(admin_url('tickets/predefined_reply/' . $id));
                }
            } else {
                $success = $this->tickets_model->update_predefined_reply($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('predefined_reply')));
                }
                redirect(admin_url('tickets/predefined_reply/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('predefined_reply_lowercase'));
        } else {
            $predefined_reply = $this->tickets_model->get_predefined_reply($id);
            $data['predefined_reply'] = $predefined_reply;
            $title = _l('edit', _l('predefined_reply_lowercase')) . ' ' . $predefined_reply->name;
        }
        $data['title'] = $title;
        $this->load->view('admin/tickets/predefined_replies/reply', $data);
    }

    /* Delete ticket reply from database */
    public function delete_predefined_reply($id)
    {
        if (!is_admin()) {
            access_denied('Delete Predefined Reply');
        }
        if (!$id) {
            redirect(admin_url('tickets/predefined_replies'));
        }
        $response = $this->tickets_model->delete_predefined_reply($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('predefined_reply')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('predefined_reply_lowercase')));
        }
        redirect(admin_url('tickets/predefined_replies'));
    }

    // Ticket statuses
    /* Get all ticket statuses */
    public function statuses()
    {
        if (!is_admin()) {
            access_denied('Ticket Statuses');
        }
        $data['statuses'] = $this->tickets_model->get_ticket_status();
        $data['title'] = 'Ticket statuses';
        $this->load->view('admin/tickets/tickets_statuses/manage', $data);
    }

    /* Add new or edit existing status */
    public function status()
    {
        if (!is_admin()) {
            access_denied('Ticket Statuses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $data = $this->input->post();
                if($data['send_mail'] == true)
                    $data['send_mail'] = 1;
                else
                    $data['send_mail'] = 0;

                $id = $this->tickets_model->add_ticket_status($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('ticket_status')));
                }
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                if($data['create_event'] == true)
                    $data['create_event'] = 1;
                else
                    $data['create_event'] = 0;

                if($data['send_mail'] == true)
                    $data['send_mail'] = 1;
                else
                    $data['send_mail'] = 0;

                $success = $this->tickets_model->update_ticket_status($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('ticket_status')));
                }
            }
            die;
        }
    }

    /* Delete ticket status from database */
    public function delete_ticket_status($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Statuses');
        }
        if (!$id) {
            redirect(admin_url('tickets/statuses'));
        }
        $response = $this->tickets_model->delete_ticket_status($id);
        if (is_array($response) && isset($response['default'])) {
            set_alert('warning', _l('cant_delete_default', _l('ticket_status_lowercase')));
        } elseif (is_array($response) && isset($response['referenced'])) {
            set_alert('danger', _l('is_referenced', _l('ticket_status_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('ticket_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_status_lowercase')));
        }
        redirect(admin_url('tickets/statuses'));
    }

    public function servicesnv2()
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        // foi necessário colocar todas as colunas aqui para não dar conflito da hora de buscar
        // um serviço
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'name',
                'serviceid',
                'secondServiceid',
            );
            $sIndexColumn = "secondServiceid";
            $sTable = 'tblsecondservice';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(//tirei a coluna daqui
            ));
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="#" onclick="doconfirm_edit(this,' . $aRow['secondServiceid'] . ');return false" data-name="' . $aRow['name'] . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array(
                    'data-name' => $aRow['name'],
                    'onclick' => 'doconfirm_edit(this,' . $aRow['secondServiceid'] . '); return false;'
                ));
                $row[] = $options .= icon_btn('tickets/delete_servicenv2/' . $aRow['secondServiceid'], 'remove', 'btn-danger _delete');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('Serviços Nível 2');
        $data['services'] = $this->tickets_model->get_service();
        $this->load->view('admin/tickets/servicesnv2/manage', $data);
    }

    public function nota_atendimento(){
        if($this->input->post()){
            $data = $this->input->post();
            if($data['nota_atendimento'] == "" && $data['nota_tecnico'] == "" && $data['nota_sistema'] == "")
            {
                $data['nota_atendimento'] = NULL;
                $data['nota_tecnico'] = NULL;
                $data['nota_sistema'] = NULL;
            }
            $id = $data['id'];
            unset($data['id']);
            $id_insert = $this->tickets_model->add_avaliacao($data, $id);
            if($id_insert){
                set_alert('success', 'Avaliação adicionada com sucesso');
            }
            redirect(admin_url('tickets/ticket/' . $id));
        }
        redirect(admin_url('tickets/tickets/'));
    }

    public function edit_servicenv2($id = '')
    {
        //$aux;
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        if ($this->input->post()) {
            if (!$this->input->post('isedit')) {
                $id = $this->tickets_model->add_servicenv2($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('service')));
                }
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                //$aux['id']  = $id;
                unset($data['id']);
                unset($data['isedit']);
                $success = $this->tickets_model->update_servicenv2($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('service')));
                } else {
                    set_alert('success', _l("Não inserido!", _l('service')));
                }
            }
            die;
        }
        //$this->load->view('admin/tickets/servicesnv2/manage', $aux);
    }

    /* List all ticket services */
    public function services()
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'name'
            );
            $sIndexColumn = "serviceid";
            $sTable = 'tblservices';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(), array(
                'serviceid'
            ));
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="#" onclick="edit_service(this,' . $aRow['serviceid'] . ');return false" data-name="' . $aRow['name'] . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array(
                    'data-name' => $aRow['name'],
                    'onclick' => 'edit_service(this,' . $aRow['serviceid'] . '); return false;'
                ));
                $row[] = $options .= icon_btn('tickets/delete_service/' . $aRow['serviceid'], 'remove', 'btn-danger _delete');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('services');
        $this->load->view('admin/tickets/services/manage', $data);
    }

    /* Add new service od delete existing one */
    public function service($id = '')
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->tickets_model->add_service($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('service')));
                }
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->tickets_model->update_service($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('service')));
                }
            }
            die;
        }
    }

    /* Delete ticket service from database */
    public function delete_servicenv2($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        if (!$id) {
            redirect(admin_url('tickets/servicesnv2'));
        }
        $response = $this->tickets_model->delete_servicenv2($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('service_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('service')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('service_lowercase')));
        }
        redirect(admin_url('tickets/servicesnv2'));
    }

    /* Delete ticket service from database */
    public function delete_service($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        if (!$id) {
            redirect(admin_url('tickets/services'));
        }
        $response = $this->tickets_model->delete_service($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('service_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('service')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('service_lowercase')));
        }
        redirect(admin_url('tickets/services'));
    }

    public function spam_filters($type = '')
    {
        if (!is_admin()) {
            access_denied('Tickets Spam Filters');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'value'
            );
            $sIndexColumn = "id";
            $sTable = 'tblticketsspamcontrol';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(
                'AND type ="' . $type . '"'
            ), array(
                'id'
            ));
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array(
                    'onclick' => 'edit_spam_filter(this,' . $aRow['id'] . '); return false;',
                    'data-value' => $aRow['value'],
                    'data-type' => $type
                ));
                $row[] = $options .= icon_btn('tickets/delete_spam_filter/' . $aRow['id'], 'remove', 'btn-danger _delete');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('spam_filters');
        $this->load->view('admin/tickets/spam_filters', $data);
    }

    public function spam_filter()
    {
        if (!is_admin()) {
            access_denied('Manage Tickets Spam Filters');
        }
        if ($this->input->post()) {
            if ($this->input->post('id')) {
                $success = $this->tickets_model->edit_spam_filter($this->input->post());
                $message = '';
                if ($success == true) {
                    $message = _l('updated_successfully', _l('spam_filter'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            } else {
                $success = $this->tickets_model->add_spam_filter($this->input->post());
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfully', _l('spam_filter'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }

    public function delete_spam_filter($id)
    {
        if (!is_admin()) {
            access_denied('Delete Ticket Spam Filter');
        }
        $success = $this->tickets_model->delete_spam_filter($id);
        if ($success) {
            set_alert('success', _l('deleted', _l('spam_filter')));
        }
        redirect(admin_url('tickets/spam_filters'));
    }

    public function block_sender()
    {
        if ($this->input->post()) {
            $this->db->insert('tblticketsspamcontrol', array(
                'type' => 'sender',
                'value' => $this->input->post('sender')
            ));
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                set_alert('success', _l('sender_blocked_successfully'));
            }
        }
    }

    public function bulk_action()
    {
        do_action('before_do_bulk_action_for_tickets');
        if ($this->input->post()) {
            $total_deleted = 0;
            $ids = $this->input->post('ids');
            $status = $this->input->post('status');
            $department = $this->input->post('department');
            $service = $this->input->post('service');
            $priority = $this->input->post('priority');
            $tags = $this->input->post('tags');
            $is_admin = is_admin();

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($is_admin) {
                            if ($this->tickets_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    } else {
                        if ($status) {

                            if($status != 1 && $this->tickets_model->verify_assigneds($ids) == false){
                                set_alert('danger', 'Estes tickets só podem ter o status alterado para ' . ticket_status_translate($status) . ' caso TODOS estejam atribuídos à algum técnico!');
                                return false;
                            }
                            $this->db->where('ticketid', $id);
                            $this->db->update('tbltickets', array(
                                'status' => $status
                            ));
                            $this->tickets_model->hora_atendimento($id, $status);
                        }
                        if ($department) {
                            $this->db->where('ticketid', $id);
                            $this->db->update('tbltickets', array(
                                'department' => $department
                            ));
                        }
                        if ($priority) {
                            $this->db->where('ticketid', $id);
                            $this->db->update('tbltickets', array(
                                'priority' => $priority
                            ));
                        }

                        if ($service) {
                            $this->db->where('ticketid', $id);
                            $this->db->update('tbltickets', array(
                                'service' => $service
                            ));
                        }
                        if ($tags) {
                            handle_tags_save($tags, $id, 'ticket');
                        }
                    }
                }
            }

            if ($this->input->post('mass_delete')) {
                set_alert('success', _l('total_tickets_deleted', $total_deleted));
            }
        }
    }

    public function get_tickets_pending()
    {
        echo json_encode($this->db->query("SELECT COUNT(ticketid) as number_assigned FROM tbltickets WHERE status IN (1,2,18,26) AND assigned=" . get_staff_user_id() . ";")->row());
    }

    public function verifyNewTicket($staffid = "")
    {
        echo json_encode($this->tickets_model->verifyNewTicket($staffid));
    }

    public function SetDataServer() // Depois post
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            $qry = $this->db->where('description = "server_info"')->where("customer_id", $data['userid'])->get('tblvault')->row();

            $data['customer_id'] = $data['userid'];

            $this->encryption->decrypt($data['password']);

            unset($data['userid']);
            if ($qry != null) {
                $type = "success";
                $message = "Atualizado com sucesso.";
                $this->db->update('tblvault', $data);
                echo json_encode(array("type" => $type, "message" => $message));
            } else {
                $type = "success";
                $message = "Inserido com sucesso.";
                $this->db->insert('tblvault', $data);
                echo json_encode(array("type" => $type, "message" => $message));
            }
        }
    }

    public function check_services(){

        $service1 = $this->input->get('service1');
        $service2 = $this->input->get('service2');
        $userid = $this->input->get('userid');
        $ticketid = $this->input->get('ticketid');



//        $quantidade = $this->db->query("SELECT COUNT(ticketid) as quantidade FROM tbltickets WHERE service = " . $service1 . " AND servicenv2 = " . $service2 . ";")->row();

        if (empty($ticketid)){
            $tickets_checked = $this->db->query("SELECT ticketid FROM tbltickets
            WHERE service = " . $service1 . " AND servicenv2 = " . $service2 . " AND userid = " . $userid . ";")->result_array();
        }else {
            $tickets_checked = $this->db->query("SELECT ticketid FROM tbltickets
            WHERE ticketid != " . $ticketid . "
            AND service = " . $service1 . " AND servicenv2 = " . $service2 . " AND userid = " . $userid . ";")->result_array();
        }


        if(count($tickets_checked) > 0){
            echo json_encode(array(
               'message' => 'true',
            ));
        }else{
            echo json_encode(array(
                'message' => 'false',
            ));
        }
        die();
    }

    public function get_checked_services_data(){

        $service1 = $this->input->get('service1');
        $service2 = $this->input->get('service2');
        $userid = $this->input->get('userid');
        $ticketid = $this->input->get('ticketid');

        $tickets_checked = $this->db->query("SELECT tblcontacts.lastname, tbltickets.subject, tbltickets.lastreply, 
                                    tblticketstatus.ticketstatusid, tblticketstatus.statuscolor, tbltickets.ticketid,
                                    tblstaff.staffid, tblstaff.firstname, tbltickets.ticketid
                                    FROM tbltickets 
                                    LEFT JOIN tblticketstatus ON tbltickets.status = tblticketstatus.ticketstatusid 
                                    LEFT JOIN tblcontacts ON tbltickets.contactid = tblcontacts.id 
                                    LEFT JOIN tblstaff ON tbltickets.assigned = tblstaff.staffid
                                    " . (empty($ticketid) ? 'WHERE' : " WHERE tbltickets.ticketid != " . $ticketid . " AND ") . "
                                     tbltickets.service = " . $service1 . " AND tbltickets.servicenv2 = " . $service2 . " AND tbltickets.userid = "
                                    . $userid . " GROUP BY tbltickets.ticketid;");


        echo '<table class="table table-striped" id="tb1">
                <thead>
                    <th class="col-md-1" style="font-size: 13px; font-weight: bold">
                        #
                    </th>
                    <th class="col-md-3" style="font-size: 13px; font-weight: bold">
                        Assunto
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Status
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Última Resposta
                    </th>
                    <th class="col-md-2" style="font-size: 13px; font-weight: bold">
                        Técnico
                    </th>
                    <th class="col-md-1" style="font-size: 13px; font-weight: bold">
                        Opções
                    </th>
                </thead>';
        echo '<tbody>';
        foreach ($tickets_checked->result() as $row)
        {
            $resposta = isset($row->lastreply) ? _d($row->lastreply) : 'Sem resposta';
            $tecnico = isset($row->staffid) ? '<a target="_blank" href="'. admin_url('staff/member/' . $row->staffid) .'">' . $row->firstname . '</a>' : 'Não atribuído';
            echo '<tr id="tr_' . $row->ticketid . '">
                    <td>
                        <a target="_blank" href="/crm/admin/tickets/ticket/' . $row->ticketid . '">#' . $row->ticketid . '</a>
                    </td>
                    <td> 
                       '.$row->subject.' 
                    </td> 
                    <td > 
                        <span class="label" style="border:1px solid '.$row->statuscolor.'; color:'.$row->statuscolor.'">'.ticket_status_translate($row->ticketstatusid).'</span> 
                    </td> 
                    <td > 
                        ' . $resposta . '
                    </td> 
                    <td> 
                        ' . $tecnico . '
                    </td> 
                    <td> 
                         <button class="btn btn-success" onclick="openresp(\''.$row->ticketid.'\')" style="vertical-align: middle">+</button>
                         '.icon_btn(APP_BASE_URL.'/admin/tickets/ticket/'. $row->ticketid, 'sign-in').'
                    </td></tr>';

        }
        echo '</tbody>';
        echo "</table>";
    }

    public function sub_table(){
        $ticketid = $this->input->get('ticketid');

        $table =  "<table class='table table-striped' id='tb2'>
                            <thead>
                                <th>
                                    Infomações
                                </th>
                                <th>
                                    Resposta
                                </th>
                            </thead>";
        $table .= "<tbody>";
        foreach ($this->tickets_model->get_ticket_replies($ticketid) as $resp)
        {
            $status = $this->tickets_model->get_ticket_status(intval($resp["reply_status"]));
            if(!empty($status)){
                $stat = '<p><span class="label inline-block" style="border:1px solid '.$status->statuscolor.'; color:'.$status->statuscolor.'">'.ticket_status_translate($resp["reply_status"]).'</span></p>';
            }else{
                $stat = 'Status não informado';
            }
            $table .= '<tr>
                            <td>
                                <div style="font-size: 13px;">
                                    <div>
                                        <p>
                                            <a href="'.APP_BASE_URL.'admin/profile/'.$resp["admin"].'">'.explode(" ", $resp["submitter"])[0].'</a>
                                        </p>
                                        <p class="text-muted">
                                            Colaborador
                                            <br>' . $stat . '
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 13px;">
                                    <div class="clearfix"></div>
                                        '.$resp["message"].'
                                    <br>
                                    <p>-----------------------------</p>
                                    <p>IP: '.$resp["ip"].'</p>
                                </div>
                                <hr>
                                <div class="col-md-12">
                                    <span class="pull-left" style="font-size: 12px;">Postado '.date("d/m/Y H:i:s",strtotime($resp["date"])).'</span>
                                </div>
                            </td>
                        </tr>';
        }
        $table .= '</tbody>';
        $table .= '</table>';
        echo $table;
    }

}
