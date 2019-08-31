<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tickets_model extends CRM_Model
{

    const PRIORITY = [
        4 => 1,    //BAIXO(4) = 1
        3 => 2,    //MEDIO(3) = 2
        2 => 3,    //ALTO(1) = 4
        1 => 4,    //URGENTE(2) = 3
        5 => 5,    //ATUALIZAÇÃO(5) = 5
        6 => 6,    //AGENDADO(6) = 6
    ];

    private $piping = false;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('time_status_model');
    }

    private function _maybe_fix_pipe_encoding_chars($text)
    {
        $text = str_replace("ð", "ğ", $text);
        $text = str_replace("þ", "ş", $text);
        $text = str_replace("ý", "ı", $text);
        $text = str_replace("Ý", "İ", $text);
        $text = str_replace("Ð", "Ğ", $text);
        $text = str_replace("Þ", "Ş", $text);
        return $text;
    }

    public function insert_piped_ticket($data)
    {
        $this->piping = true;
        $attachments = $data['attachments'];
        $subject = $this->_maybe_fix_pipe_encoding_chars($data['subject']);
        // Prevent insert ticket to database if mail delivery error happen
        // This will stop createing a thousand tickets
        $system_blocked_subjects = array(
            'Mail delivery failed',
            'failure notice',
            'Returned mail: see transcript for details',
            'Undelivered Mail Returned to Sender',
        );

        $subject_blocked = false;

        foreach ($system_blocked_subjects as $sb) {
            if (strpos('x' . $subject, $sb) !== false) {
                $subject_blocked = true;
                break;
            }
        }

        if ($subject_blocked == true) {
            return;
        }

        $message = $this->_maybe_fix_pipe_encoding_chars($data['body']);
        $name = $data['fromname'];
        $email = $data['email'];
        $to = $data['to'];
        $subject = $subject;
        $message = $message;
        $mailstatus = false;
        $spam_filters = $this->db->get('tblticketsspamcontrol')->result_array();
        foreach ($spam_filters as $filter) {
            $type = $filter['type'];
            $value = $filter['value'];
            if ($type == "sender") {
                if (strtolower($value) == strtolower($email)) {
                    $mailstatus = "Blocked Sender";
                }
            }
            if ($type == "subject") {
                if (strpos("x" . strtolower($subject), strtolower($value))) {
                    $mailstatus = "Blocked Subject";
                }
            }
            if ($type == "phrase") {
                if (strpos("x" . strtolower($message), strtolower($value))) {
                    $mailstatus = "Blocked Phrase";
                }
            }
        }
        // No spam found
        if (!$mailstatus) {
            $pos = strpos($subject, "[Ticket ID: ");
            if ($pos === false) {
            } else {
                $tid = substr($subject, $pos + 12);
                $tid = substr($tid, 0, strpos($tid, "]"));
                $this->db->where('ticketid', $tid);
                $data = $this->db->get('tbltickets')->row();
                $tid = $data->ticketid;
            }
            $to = trim($to);
            $toemails = explode(",", $to);
            $department_id = false;
            $userid = false;
            foreach ($toemails as $toemail) {
                if (!$department_id) {
                    $this->db->where('email', $toemail);
                    $data = $this->db->get('tbldepartments')->row();
                    if ($data) {
                        $department_id = $data->departmentid;
                        $to = $data->email;
                    }
                }
            }
            if (!$department_id) {
                $mailstatus = "Department Not Found";
            } else {
                if ($to == $email) {
                    $mailstatus = "Blocked Potential Email Loop";
                } else {
                    $message = trim($message);
                    $this->db->where('active', 1);
                    $this->db->where('email', $email);
                    $result = $this->db->get('tblstaff')->row();
                    if ($result) {
                        if ($tid) {
                            $data = array();
                            $data['message'] = $message;
                            $data['status'] = 1;
                            if ($userid == false) {
                                $data['name'] = $name;
                                $data['email'] = $email;
                            }
                            $reply_id = $this->add_reply($data, $tid, $result->staffid, $attachments);
                            if ($reply_id) {
                                $mailstatus = "Ticket Reply Imported Successfully";
                            }
                        } else {
                            $mailstatus = "Ticket ID Not Found";
                        }
                    } else {
                        $this->db->where('email', $email);
                        $result = $this->db->get('tblcontacts')->row();
                        if ($result) {
                            $userid = $result->userid;
                            $contactid = $result->id;
                        }
                        if ($userid == false && get_option('email_piping_only_registered') == '1') {
                            $mailstatus = "Unregistered Email Address";
                        } else {
                            $filterdate = date("YmdHis", mktime(date("H"), date("i") - 15, date("s"), date("m"), date("d"), date("Y")));
                            $query = 'SELECT count(*) as total FROM tbltickets WHERE date > "' . $filterdate . '" AND (email="' . $this->db->escape($email) . '"';
                            if ($userid) {
                                $query .= " OR userid=" . (int)$userid;
                            }
                            $query .= ")";
                            $result = $this->db->query($query)->row();
                            if (10 < $result->total) {
                                $mailstatus = "Exceeded Limit of 10 Tickets within 15 Minutes";
                            } else {
                                if (isset($tid)) {
                                    $data = array();
                                    $data['message'] = $message;
                                    $data['status'] = 1;
                                    if ($userid == false) {
                                        $data['name'] = $name;
                                        $data['email'] = $email;
                                    } else {
                                        $data['userid'] = $userid;
                                        $data['contactid'] = $contactid;

                                        $this->db->where('userid', $userid);
                                        $this->db->where('ticketid', $tid);
                                        $t = $this->db->get('tbltickets')->row();
                                        if (!$t) {
                                            $abuse = true;
                                        }
                                    }
                                    if (!isset($abuse)) {
                                        $reply_id = $this->add_reply($data, $tid, null, $attachments);
                                        if ($reply_id) {
                                            // Dont change this line
                                            $mailstatus = "Ticket Reply Imported Successfully";
                                        }
                                    } else {
                                        $mailstatus = 'Ticket ID Not Found For User';
                                    }
                                } else {
                                    if (get_option('email_piping_only_registered') == 1 && !$userid) {
                                        $mailstatus = "Blocked Ticket Opening from Unregistered User";
                                    } else {
                                        if (get_option('email_piping_only_replies') == '1') {
                                            $mailstatus = "Only Replies Allowed by Email";
                                        } else {
                                            $data = array();
                                            $data['department'] = $department_id;
                                            $data['subject'] = $subject;
                                            $data['message'] = $message;
                                            $data['contactid'] = $contactid;
                                            $data['priority'] = get_option('email_piping_default_priority');
                                            if ($userid == false) {
                                                $data['name'] = $name;
                                                $data['email'] = $email;
                                            } else {
                                                $data['userid'] = $userid;
                                            }
                                            $tid = $this->add($data, null, $attachments);
                                            // Dont change this line
                                            $mailstatus = "Ticket Imported Successfully";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($mailstatus == "") {
            $mailstatus = "Ticket Import Failed";
        }
        $this->db->insert('tblticketpipelog', array(
            'date' => date('Y-m-d H:i:s'),
            'email_to' => $to,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'status' => $mailstatus
        ));

        return $mailstatus;
    }

    /**
     * @param $cpf_cnpj
     * @return bool
     */
    public function get_partner_by_cpf($cpf_cnpj){
        $partner = $this->db
            ->where('partner_cnpj', $cpf_cnpj)
            ->where('partner_cnpj!=', null)
            ->get('tblstaff')->row();
        if($partner)
            return $partner;

        return false;
    }

    private function process_pipe_attachments($attachments, $ticket_id, $reply_id = '')
    {
        if (!empty($attachments)) {
            $ticket_attachments = array();
            $allowed_extensions = explode(',', get_option('ticket_attachments_file_extensions'));

            $path = FCPATH . 'uploads/ticket_attachments' . '/' . $ticket_id . '/';

            foreach ($attachments as $attachment) {
                $filename = $attachment["filename"];
                $filenameparts = explode(".", $filename);
                $extension = end($filenameparts);
                $extension = strtolower($extension);
                if (in_array('.' . $extension, $allowed_extensions)) {
                    $filename = implode(array_slice($filenameparts, 0, 0 - 1));
                    $filename = trim(preg_replace("/[^a-zA-Z0-9-_ ]/", "", $filename));
                    if (!$filename) {
                        $filename = "attachment";
                    }
                    if (!file_exists($path)) {
                        mkdir($path);
                        $fp = fopen($path . 'index.html', 'w');
                        fclose($fp);
                    }
                    $filename = unique_filename($path, $filename . "." . $extension);
                    $fp = fopen($path . $filename, "w");
                    fwrite($fp, $attachment["data"]);
                    fclose($fp);
                    array_push($ticket_attachments, array(
                        'file_name' => $filename,
                        'filetype' => get_mime_by_extension($filename)
                    ));
                }
            }
            $this->insert_ticket_attachments_to_database($ticket_attachments, $ticket_id, $reply_id);
        }
    }

    public function getSegundosServicos($id = '')
    {
        $this->db->select('tblsecondservice.secondServiceid, tblsecondservice.name as name, tblsecondservice.serviceid');
        if (is_numeric($id)) {
            $this->db->where('tblsecondservice.serviceid', $id);
            $this->db->order_by('tblsecondservice.serviceid', 'asc');
            return $this->db->get('tblsecondservice')->result_array();
        }
    }


    public function get($id = '', $where = array())
    {
        $this->db->select('*,tbltickets.userid,tbltickets.name as from_name,tbltickets.email as ticket_email, tbldepartments.name as department_name, tblpriorities.name as priority_name, statuscolor, tbltickets.admin, tblservices.name as service_name, service, tblticketstatus.name as status_name,tbltickets.ticketid,subject,tblcontacts.firstname as user_firstname,.tblcontacts.lastname as user_lastname,tblstaff.firstname as staff_firstname, tblstaff.lastname as staff_lastname,lastreply,message,tbltickets.status,subject,department,priority,tblcontacts.email,adminread,clientread,date,tbltickets.ip');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.id = tbltickets.contactid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->join('tblpriorities', 'tblpriorities.priorityid = tbltickets.priority', 'left');
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('tbltickets.ticketid', $id);

            return $this->db->get('tbltickets')->row();
        }
        $this->db->order_by('lastreply', 'asc');

        return $this->db->get('tbltickets')->result_array();
    }

    public function get_few_ticket_info_by_id($id){
        return $this->db
            ->select('ticketid, subject, firstname, lastname, assigned')
            ->join('tblcontacts', 'tblcontacts.id = tbltickets.contactid', 'left')
            ->where('ticketid', $id)
            ->get('tbltickets')->row();
    }

    /**
     * Get ticket by id and all data
     * @param  mixed $id ticket id
     * @param  mixed $userid Optional - Tickets from USER ID
     * @return object
     */
    public function get_ticket_by_id($id, $userid = '')
    {
        $this->db->select('*,tbltickets.userid,tbltickets.name as from_name,tbltickets.email as ticket_email, tbldepartments.name as department_name, tblpriorities.name as priority_name, statuscolor, tbltickets.admin, tblservices.name as service_name, service, tblticketstatus.name as status_name,tbltickets.ticketid,subject,tblcontacts.firstname as user_firstname,.tblcontacts.lastname as user_lastname,tblstaff.firstname as staff_firstname, tblstaff.lastname as staff_lastname,lastreply,message,tbltickets.status,subject,department,priority,tblcontacts.email,adminread,clientread,date,tbltickets.ip, tbltickets.assigned, tbltickets.partner_id');
        $this->db->from('tbltickets');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.id = tbltickets.contactid', 'left');
        $this->db->join('tblpriorities', 'tblpriorities.priorityid = tbltickets.priority', 'left');
        $this->db->where('tbltickets.ticketid', $id);
        if (is_numeric($userid)) {
            $this->db->where('tbltickets.userid', $userid);
        }
        $ticket = $this->db->get()->row();
        if ($ticket) {
            if ($ticket->admin == null || $ticket->admin == 0) {
                if ($ticket->contactid != 0) {
                    $ticket->submitter = $ticket->user_firstname . ' ' . $ticket->user_lastname;
                } else {
                    $ticket->submitter = $ticket->from_name;
                }
            } else {
                if ($ticket->contactid != 0) {
                    $ticket->submitter = $ticket->user_firstname . ' ' . $ticket->user_lastname;
                } else {
                    $ticket->submitter = $ticket->from_name;
                }
                $ticket->opened_by = $ticket->staff_firstname . ' ' . $ticket->staff_lastname;
            }

            $ticket->attachments = $this->get_ticket_attachments($id);
        }


        return $ticket;
    }

    /**
     * Insert ticket attachments to database
     * @param  array $attachments array of attachment
     * @param  mixed $ticketid
     * @param  boolean $replyid If is from reply
     */
    public function insert_ticket_attachments_to_database($attachments, $ticketid, $replyid = false)
    {
        foreach ($attachments as $attachment) {
            $attachment['ticketid'] = $ticketid;
            $attachment['dateadded'] = date('Y-m-d H:i:s');
            if ($replyid !== false && is_int($replyid)) {
                $attachment['replyid'] = $replyid;
            }
            $this->db->insert('tblticketattachments', $attachment);
        }
    }

    /**
     * Get ticket attachments from database
     * @param  mixed $id ticket id
     * @param  mixed $replyid Optional - reply id if is from from reply
     * @return array
     */
    public function get_ticket_attachments($id, $replyid = '')
    {
        $this->db->where('ticketid', $id);
        if (is_numeric($replyid)) {
            $this->db->where('replyid', $replyid);
        } else {
            $this->db->where('replyid', null);
        }
        $this->db->where('ticketid', $id);

        return $this->db->get('tblticketattachments')->result_array();
    }

    /**
     * Add new reply to ticket
     * @param mixed $data reply $_POST data
     * @param mixed $id ticket id
     * @param boolean $admin staff id if is staff making reply
     * @return bool
     */
    public function add_reply($data, $id, $admin = null, $pipe_attachments = false)
    {

        if(isset($data['gambiarra_sinistra'])) {
            if (is_numeric($data['gambiarra_sinistra']))
                $assigned_g = $data['gambiarra_sinistra'];
            else
                $assigned_g = get_staff_user_id();
            unset($data['gambiarra_sinistra']);
        }

        $is_next_attend = false;
        if (isset($data['is_next_attend'])) {
            $is_next_attend = $data['is_next_attend'];
            unset($data['is_next_attend']);
        }

        if (isset($data['assign_to_current_user'])) {
            $assigned = get_staff_user_id();
            unset($data['assign_to_current_user']);
        }else{
            if(!is_admin()) {
                $tk = $this->db->select('assigned')
                    ->where('ticketid', $id)
                    ->get('tbltickets')->row();
                if ($tk->assigned == get_staff_user_id()) {
                    $assigned = get_staff_user_id();
                }
            }else{
                $tk = $this->db->select('assigned')
                    ->where('ticketid', $id)
                    ->get('tbltickets')->row();
                $assigned = $tk->assigned;
            }
        }
        if(isset($data['priority'])){
            $priority = $data['priority'];
        }


        $unsetters = array(
            'note_description',
            'department',
            'priority',
            'subject',
            'assigned',
            'project_id',
            'service',
            'status_top',
            'attachments',
            'DataTables_Table_0_length',
            'DataTables_Table_1_length',
            'custom_fields'
        );
        foreach ($unsetters as $unset) {
            if (isset($data[$unset])) {
                unset($data[$unset]);
            }
        }
        if ($admin !== null) {
            $data['admin'] = $admin;
            $status = $data['status'];
        } else {
            $status = $data['status'];
        }
        if (isset($data['status'])) {
            unset($data['status']);
        }

        $cc = '';
        if (isset($data['cc'])) {
            $cc = $data['cc'];
            unset($data['cc']);
        }

        $data['ticketid'] = $id;
        $data['date'] = date('Y-m-d H:i:s');
        $data['ip'] = $this->input->ip_address();
        $data['message'] = trim($data['message']);

        if ($this->piping == true) {
            $data['message'] = preg_replace('/\v+/u', '<br>', $data['message']);
        }

        // admin can have html
        if ($admin == null) {
            $data['message'] = _strip_tags($data['message']);
            $data['message'] = nl2br_save_html($data['message']);
        }
        //Gambirinha kaka
        if(isset($data['clientid'])){
            $data['userid'] = $data['clientid'];
            $data['contactid'] = get_client_contact_by_id($data['contactid'])->contactid;
            unset($data['clientid']);
        }

        if (!isset($data['userid'])) {
            $data['userid'] = 0;
        }
        if (is_client_logged_in()) {
            $data['contactid'] = get_contact_user_id();
        }
        $data['status'] = $status;
        $_data = do_action('before_ticket_reply_add', array(
            'data' => $data,
            'id' => $id,
            'admin' => $admin,
        ));

        $data = $_data['data'];
        $this->db->insert('tblticketreplies', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($assigned) && $status != 5) {
                $this->db->where('ticketid', $id);
                $this->db->update('tbltickets', array(
                    'assigned' => $assigned
                ));
            }

            if ($pipe_attachments != false) {
                $this->process_pipe_attachments($pipe_attachments, $id, $insert_id);
            } else {
                $attachments = handle_ticket_attachments($id);
                if ($attachments) {
                    $this->insert_ticket_attachments_to_database($attachments, $id, $insert_id);
                }
            }

            $_attachments = $this->get_ticket_attachments($id, $insert_id);

            logActivity('New Ticket Reply [ReplyID: ' . $insert_id . ']');

//            $this->db->select('status, priority');
            $this->db->where('ticketid', $id);
            $old_ticket = $this->db->get('tbltickets')->row();
            $old_ticket_status = $old_ticket->status;
            $old_ticket_priority = $old_ticket->priority;


            $this->db->where('ticketid', $id);
            $this->db->update('tbltickets', array(
                'lastreply' => date('Y-m-d H:i:s'),
                'status' => $status,
                'priority' => $priority ?? $old_ticket_priority,
                'adminread' => 0,
                'clientread' => 0,
                'is_next_attend' => $is_next_attend,
            ));

            //insere as datas na tabela de logs de status
            $this->time_status_model->add($id, $status, get_staff_user_id());

            if ($old_ticket_status != $status) {

                if ($old_ticket_status == 1 && $status == 2) {
                    $this->db->where('ticketid', $id);
                    $this->db->update('tbltickets', array(
                        'assigned' => (PAINEL == INORTE) ? $assigned : $assigned_g
                    ));
                }

                //// Data para o painelsuporte caso status seja alterado para EmAtendimento
                $this->hora_atendimento($id, $status);
                ////////////////////////////////////////

                do_action('after_ticket_status_changed', array(
                    'id' => $id,
                    'status' => $status
                ));
            }

            $this->load->model('emails_model');
            $ticket = $this->get_ticket_by_id($id);
            $userid = $ticket->userid;
            if ($ticket->userid != 0 && $ticket->contactid != 0) {
                $email = $this->clients_model->get_contact($ticket->contactid)->email;
            } else {
                $email = $ticket->ticket_email;
            }
            if ($admin == null) {
                $this->load->model('departments_model');
                $this->load->model('staff_model');
                $staff = $this->staff_model->get('', 1);
                foreach ($staff as $member) {
                    if (get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member($member['staffid'])) {
                        continue;
                    }


                    $staff_departments = $this->departments_model->get_staff_departments($member['staffid'], true);
                    if (in_array($ticket->department, $staff_departments)) {
                        foreach ($_attachments as $at) {
                            $this->emails_model->add_attachment(array(
                                'attachment' => get_upload_path_by_type('ticket') . $id . '/' . $at['file_name'],
                                'filename' => $at['file_name'],
                                'type' => $at['filetype'],
                                'read' => true
                            ));
                        }

                        $merge_fields = array();
                        $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($ticket->userid, $ticket->contactid));
                        $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-reply-to-admin', $id));
                        $this->emails_model->send_email_template('ticket-reply-to-admin', $member['email'], $merge_fields, $id);
                    }
                }
            } else {
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($ticket->userid, $ticket->contactid));
                $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-reply', $id));

                foreach ($_attachments as $at) {
                    $this->emails_model->add_attachment(array(
                        'attachment' => get_upload_path_by_type('ticket') . $id . '/' . $at['file_name'],
                        'filename' => $at['file_name'],
                        'type' => $at['filetype'],
                        'read' => true
                    ));
                }

                $this->emails_model->send_email_template('ticket-reply', $email, $merge_fields, $id, $cc);
            }
            do_action('after_ticket_reply_added', array(
                'data' => $data,
                'id' => $id,
                'admin' => $admin,
                'replyid' => $insert_id
            ));

            return $insert_id;
        }

        return false;
    }

    /**
     *  Delete ticket reply
     * @param   mixed $ticket_id ticket id
     * @param   mixed $reply_id reply id
     * @return  boolean
     */
    public function delete_ticket_reply($ticket_id, $reply_id)
    {
        $this->db->where('id', $reply_id);
        $this->db->delete('tblticketreplies');
        if ($this->db->affected_rows() > 0) {
            // Get the reply attachments by passing the reply_id to get_ticket_attachments method
            $attachments = $this->get_ticket_attachments($ticket_id, $reply_id);
            if (count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    if (unlink(get_upload_path_by_type('ticket') . $ticket_id . '/' . $attachment['file_name'])) {
                        $this->db->where('id', $attachment['id']);
                        $this->db->delete('tblticketattachments');
                    }
                }
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('ticket') . $ticket_id);
                if (count($other_attachments) == 0) {
                    delete_dir(get_upload_path_by_type('ticket') . $ticket_id);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * This functions is used when staff open client ticket
     * @param  mixed $userid client id
     * @param  mixed $id ticketid
     * @return array
     */
    public function get_user_other_tickets($userid, $id)
    {
        $this->db->select('tbldepartments.name as department_name, tblservices.name as service_name,tblticketstatus.name as status_name,tblstaff.firstname as staff_firstname, tblclients.lastname as staff_lastname,ticketid,subject,firstname,lastname,lastreply');
        $this->db->from('tbltickets');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbltickets.department', 'left');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        $this->db->join('tblservices', 'tblservices.serviceid = tbltickets.service', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbltickets.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbltickets.admin', 'left');
        $this->db->where('tbltickets.userid', $userid);
        $this->db->where('tbltickets.ticketid !=', $id);
        $tickets = $this->db->get()->result_array();
        $i = 0;
        foreach ($tickets as $ticket) {
            $tickets[$i]['submitter'] = $ticket['firstname'] . ' ' . $ticket['lastname'];
            unset($ticket['firstname']);
            unset($ticket['lastname']);
            $i++;
        }

        return $tickets;
    }

    /**
     * Get all ticket replies
     * @param  mixed $id ticketid
     * @param  mixed $userid specific client id
     * @return array
     */
    public function get_ticket_replies($id)
    {
        $ticket_replies_order = do_action('ticket_replies_order', 'DESC');

        $this->db->select('tblticketreplies.id,tblticketreplies.ip,tblticketreplies.name as from_name,tblticketreplies.email as reply_email, tblticketreplies.admin, tblticketreplies.userid,tblstaff.firstname as staff_firstname,.tblstaff.lastname as staff_lastname,tblcontacts.firstname as user_firstname,.tblcontacts.lastname as user_lastname,message,date,contactid, tblticketstatus.ticketstatusid as reply_status');
        $this->db->from('tblticketreplies');
        $this->db->join('tblclients', 'tblclients.userid = tblticketreplies.userid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblticketreplies.admin', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.id = tblticketreplies.contactid', 'left');
        $this->db->join('tblticketstatus', 'tblticketreplies.status = tblticketstatus.ticketstatusid', 'left');
        $this->db->where('ticketid', $id);
        $this->db->order_by('date', $ticket_replies_order);
        $replies = $this->db->get()->result_array();
        $i = 0;
        foreach ($replies as $reply) {
            if ($reply['admin'] !== null || $reply['admin'] != 0) {
                // staff reply
                $replies[$i]['submitter'] = $reply['staff_firstname'] . ' ' . $reply['staff_lastname'];
            } else {
                if ($reply['contactid'] != 0) {
                    $replies[$i]['submitter'] = $reply['user_firstname'] . ' ' . $reply['user_lastname'];
                } else {
                    $replies[$i]['submitter'] = $reply['from_name'];
                }
            }
            unset($replies[$i]['staff_firstname']);
            unset($replies[$i]['staff_lastname']);
            unset($replies[$i]['user_firstname']);
            unset($replies[$i]['user_lastname']);
            $replies[$i]['attachments'] = $this->get_ticket_attachments($id, $reply['id']);
            $i++;
        }

        return $replies;
    }

    public function get_last_atendimento($user_id)
    {


        return $this->db
            ->select('ticketid, lastreply, date')
            ->where('userid', $user_id)
            ->order_by('ticketid', 'desc')
            ->limit(1)
            ->get('tbltickets')
            ->row();
    }

    public function get_validation()
    {
        return $this->db
            ->select('validation')
            ->order_by('id', 'desc')
            ->limit(1)
            ->get('tblvalidadelastticket')
            ->row();
    }

    public function get_next_tickets()
    {
        $partner_id = get_staff_partner_id();
        $tickets = $this->db
            ->select('ticketid, priority, lastreply, scheduled_date, is_next_attend')
            ->where('status', 1)
            ->where('is_next_attend', true)
            ->where('partner_id', $partner_id)
            ->where('priority !=', 5)
            ->where('priority !=', 8)
            ->order_by('priority', 'ASC')
            ->order_by('lastreply', 'ASC')
            ->order_by('date', 'ASC')
            ->get('tbltickets')
            ->result_array();

        if (!$tickets) {
            return false;
        }

        foreach ($tickets as $key => $ticket){

            $check_replies = $this->db->where('ticketid', $ticket['ticketid'])->get('tblticketreplies')->result_array();

            if((!empty($check_replies) && ($ticket['priority'] == self::PRIORITY[6])) || ($ticket['priority'] == self::PRIORITY[6])) { // se nao teve nenhuma resposta is um ticket recem aberto
                if (empty($ticket['scheduled_date'])) {
                    unset($tickets[$key]);
                } else {
                    $aux_scheduled_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ticket['scheduled_date']);
                    $now = \Carbon\Carbon::now();
                    if ($aux_scheduled_date->gte($now)) {
                        unset($tickets[$key]);
                    }
                }
            }

        }

        $respondidos = array();
        $less_15_minutes = [];
        foreach ($tickets as $key => $ticket){
            $tickets[$key]['priority'] = self::PRIORITY[$ticket['priority']];
            if($ticket['lastreply'] != null) {
                if (Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ticket['lastreply'])->diffInMinutes(Carbon\Carbon::now()) <= 15) {
                    $less_15_minutes[] = $ticket;
                    unset($tickets[$key]);
                } else {
                    array_push($respondidos, $ticket);
                    unset($tickets[$key]);
                }
            }
        }

        // se (count($tickets) e zero quer dizer que nao tem nenhum ticket ou pelo menos nenhum ticket com menos de 15 min
        // de resposta. Assim, se (count($tickets) for zero e (count($less_15_minutes) maior que zero eu dou push
        //nos ($less_15_minutes) tickets para serem puxados
        if ((count($tickets) <= 0) && (count($less_15_minutes) > 0)){
            foreach ($less_15_minutes as $less){
                $tickets[] = $less;
            }
        }

        // Obtem as colunas em forma de lista simples
        foreach ($tickets as $key => $row) {
            $priority[$key]  = $row['priority'];
            $date[$key] = $row['lastreply'];
        }

        // Ordena os dados por priority crescente, data crescente. Isso me retornar os tickets de maior prioridade
        // e de mais tempo de espera simultaneamente. Magicamente essa func ordena o ultimo array $tickets com base nas
        // 2 listas informadas kkkkk
        array_multisort($priority, SORT_ASC, $date, SORT_ASC, $tickets);


        foreach ($respondidos as $key => $row) {
            $priority_resp[$key]  = $row['priority'];
            $date_resp[$key] = $row['lastreply'];
        }

        array_multisort($priority_resp, SORT_ASC,  $date_resp, SORT_DESC, $respondidos);


        foreach ($respondidos as $resp){
            if(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resp['lastreply'])->diffInMinutes(\Carbon\Carbon::now())){
                array_unshift($tickets, $resp);
            }
        }

        return $tickets;
    }

    public function take_attend_ticket()
    {

        $next_tickets = $this->get_next_tickets();
        if ($next_tickets) {
            return $next_tickets[0]['ticketid'];
        }

        $partner_id = get_staff_partner_id();
        $tickets = $this->db
            ->select('ticketid, priority, lastreply, scheduled_date')
            ->where('status', 1)
            ->where('partner_id', $partner_id)
            ->where('priority !=', 5)
            ->where('priority !=', 8)
            ->get('tbltickets')
            ->result_array();

        if (!$tickets) {
            return false;
        }


        foreach ($tickets as $key => $ticket){

            $check_replies = $this->db->where('ticketid', $ticket['ticketid'])->get('tblticketreplies')->result_array();

            if((!empty($check_replies) && ($ticket['priority'] == self::PRIORITY[6])) || ($ticket['priority'] == self::PRIORITY[6])) { // se nao teve nenhuma resposta is um ticket recem aberto
                if (empty($ticket['scheduled_date'])) {
                    unset($tickets[$key]);
                } else {
                    $aux_scheduled_date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ticket['scheduled_date']);
                    $now = \Carbon\Carbon::now();
                    if ($aux_scheduled_date->gte($now)) {
                        unset($tickets[$key]);
                    }
                }
            }

        }

        $respondidos = array();
        $less_15_minutes = [];
        foreach ($tickets as $key => $ticket){
            $tickets[$key]['priority'] = self::PRIORITY[$ticket['priority']];
            if($ticket['lastreply'] != null) {
                if (Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ticket['lastreply'])->diffInMinutes(Carbon\Carbon::now()) <= 15) {
                    $less_15_minutes[] = $ticket;
                    unset($tickets[$key]);
                } else {
                    array_push($respondidos, $ticket);
                    unset($tickets[$key]);
                }
            }
        }

        // se (count($tickets) e zero quer dizer que nao tem nenhum ticket ou pelo menos nenhum ticket com menos de 15 min
        // de resposta. Assim, se (count($tickets) for zero e (count($less_15_minutes) maior que zero eu dou push
        //nos ($less_15_minutes) tickets para serem puxados
        if ((count($tickets) <= 0) && (count($less_15_minutes) > 0)){
            foreach ($less_15_minutes as $less){
                $tickets[] = $less;
            }
        }

        // Obtem as colunas em forma de lista simples
        foreach ($tickets as $key => $row) {
            $priority[$key]  = $row['priority'];
            $date[$key] = $row['lastreply'];
        }

        // Ordena os dados por priority crescente, data crescente. Isso me retornar os tickets de maior prioridade
        // e de mais tempo de espera simultaneamente. Magicamente essa func ordena o ultimo array $tickets com base nas
        // 2 listas informadas kkkkk
        array_multisort($priority, SORT_ASC, $date, SORT_ASC, $tickets);


        foreach ($respondidos as $key => $row) {
            $priority_resp[$key]  = $row['priority'];
            $date_resp[$key] = $row['lastreply'];
        }

        array_multisort($priority_resp, SORT_ASC,  $date_resp, SORT_DESC, $respondidos);


        foreach ($respondidos as $resp){
            if(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resp['lastreply'])->diffInMinutes(\Carbon\Carbon::now())){
                array_unshift($tickets, $resp);
            }
        }

        return $tickets[0]['ticketid'];
    }

    //função para buscar uma única resposta por id
    public function get_single_ticket_reply($id)
    {
        $this->db->select('tblticketreplies.message');
        $this->db->from('tblticketreplies');
        $this->db->where('id', $id);
        $this->db->order_by('id');
        return $this->db->get()->row();
    }
    ////////////////////////////////////////////////

    /**
     * Add new ticket to database
     * @param mixed $data ticket $_POST data
     * @param mixed $admin If admin adding the ticket passed staff id
     * @return bool
     */
    public function add($data, $admin = null, $pipe_attachments = false, $is_externo = 1)
    {
        if(isset($data['proced'])) {
            $procs = $data['proced'];
            unset($data['proced']);
            $data['procedimento'] = "";
            foreach ($procs as $proc)
                $data['procedimento'] .= $proc . ",";
        }
        if($data['subject'] == "")
            $data['subject'] = get_service1_name($data['service'])."/".get_service2_name($data['servicenv2']);

        if ($admin !== null) {
            $data['admin'] = $admin;
            unset($data['ticket_client_search']);
        }

        if(!isset($data['service']) && $is_externo == 1){
            $data['service'] = get_service1_id('ND');
            $data['servicenv2'] = get_service2_id('ND', $data['service']);
        }

        if(!isset($data['assigned']) && $is_externo == 1){
            $data['assigned'] = get_option('clients_predefined_assign');
        }

        if (isset($data['assigned']) && $data['assigned'] == '') {
            $data['assigned'] = 0;
        }else {
            $assigned = $data['assigned'];
        }
        if(!isset($data['name_soli'])){
            $data['name_soli'] = '';
        }
        //Definindo id do parceiro
        if (!isset($data['partner_id'])) {
            if(PAINEL == INORTE &&  !$is_externo){
                $this->load->model('clients_model');
                $data['partner_id'] = $this->clients_model->get($data['userid'])->partner_id;
            }else{
                $data['partner_id'] = get_staff_partner_id();
            }
        }else if ($data['partner_id'] == '') {
            $data['partner_id'] = get_staff_partner_id();
        }

        if (isset($data['project_id']) && $data['project_id'] == '') {
            $data['project_id'] = 0;
        }
        if ($admin == null) {
            if (isset($data['email'])) {
                $data['userid'] = 0;
                $data['contactid'] = 0;
            } else {
                // Opened from customer portal otherwise is passed from pipe or admin area
                if (!isset($data['userid']) && !isset($data['contactid'])) {
                    $data['userid'] = get_client_user_id();
                    $data['contactid'] = get_contact_user_id();
                }
            }
            $data['status'] = 1;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }


        // CC is only from admin area
        $cc = '';
        if (isset($data['cc'])) {
            $cc = $data['cc'];
            unset($data['cc']);
        }

        if (isset($data['scheduled_date'])){
            if (!empty($data['scheduled_date'])) {
                $data['scheduled_date'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $data['scheduled_date'])
                    ->format('Y-m-d H:i');
            }else{
                $data['scheduled_date'] = null;
            }
        }

        $data['date'] = date('Y-m-d H:i:s');
        $data['ticketkey'] = md5(uniqid(time(), true));
        $data['status'] = 1;

        $data['status'] = 1;

        $data['message'] = trim($data['message']);
        $data['subject'] = trim($data['subject']);

        if (!isset($data['plantao']))
            $data['plantao'] = 0;

        if ($this->piping == true) {
            $data['message'] = preg_replace('/\v+/u', '<br>', $data['message']);
        }
        // Admin can have html
        if ($admin == null) {
            $data['message'] = _strip_tags($data['message']);
            $data['subject'] = _strip_tags($data['subject']);
            $data['message'] = nl2br_save_html($data['message']);
        }
        //Gambirinha akakak
        if(isset($data['clientid'])){
            $data['userid'] = $data['clientid'];
            unset($data['clientid']);
        }

        if (!isset($data['userid'])) {
            $data['userid'] = 0;
        }else{
            if(!$is_externo) {
                $data['userid'] = get_client_contact_by_id($data['contactid'])->clientid;
                $data['contactid'] = get_client_contact_by_id($data['contactid'])->contactid;
            }
        }
        if (isset($data['priority']) && $data['priority'] == '' || !isset($data['priority'])) {
            $data['priority'] = 0;
        }


        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }
        // Seta a coluna is_externo para 0(significa que foi gravado por uma pessoa do suporte)
        if (isset($is_externo)) {
            $data['is_externo'] = $is_externo;
        }

        $data['ip'] = $this->input->ip_address();
        $_data = do_action('before_ticket_created', array(
            'data' => $data,
            'admin' => $admin
        ));
        $data = $_data['data'];
        $this->db->insert('tbltickets', $data);
        $ticketid = $this->db->insert_id();
        if ($ticketid) {
            refresh_panel();
            handle_tags_save($tags, $ticketid, 'ticket');
            if (isset($custom_fields)) {
                handle_custom_fields_post($ticketid, $custom_fields);
            }

            $this->load->model('emails_model');
            if (isset($data['assigned']) && $data['assigned'] != 0) {
                if ($data['assigned'] != get_staff_user_id()) {
                    $notified = add_notification(array(
                        'description' => 'not_ticket_assigned_to_you',
                        'touserid' => $data['assigned'],
                        'fromcompany' => 1,
                        'fromuserid' => null,
                        'link' => 'tickets/ticket/' . $ticketid,
                        'additional_data' => serialize(array(
                            $data['subject']
                        ))
                    ));

                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
                    $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-assigned-to-admin', $ticketid));

                    $this->db->where('staffid', $data['assigned']);
                    $assignedEmail = $this->db->get('tblstaff')->row()->email;
                    $this->emails_model->send_email_template('ticket-assigned-to-admin', $assignedEmail, $merge_fields, $ticketid);

                    if ($notified) {
                        pusher_trigger_notification(array($data['assigned']));
                    }
                }
            }


            //insere as datas na tabela de logs de status
            $this->time_status_model->add($ticketid, $data['status'], (isset($data['assigned']) ? $data['assigned'] : 0));

            //////////////////// insere a data de inicio na tabela horaatendimento //////////////////////
            $this->db->insert('horaatendimento', array(
                'date_espera' => date('Y-m-d H:i:s'),
                'date_atendimento' => date('Y-m-d H:i:s'),
                'idticket' => $ticketid,
            ));
            /////////////////////////////////////////////////////////////////////////////////////////////
            if ($pipe_attachments != false) {
                $this->process_pipe_attachments($pipe_attachments, $ticketid);
            } else {

                $attachments = handle_ticket_attachments($ticketid);
                if ($attachments) {
                    $this->insert_ticket_attachments_to_database($attachments, $ticketid);
                }
            }

            $_attachments = $this->get_ticket_attachments($ticketid);


            if (isset($data['userid']) && $data['userid'] != false) {
                $email = $this->clients_model->get_contact($data['contactid'])->email;
            } else {
                $email = $data['email'];
            }

            $template = 'new-ticket-opened-admin';
            if ($admin == null) {

                $template = 'ticket-autoresponse';

                $this->load->model('departments_model');
                $this->load->model('staff_model');
                $staff = $this->staff_model->get('', 1, array(), true);

                $notifiedUsers = array();
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
                $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('new-ticket-created-staff', $ticketid));

                foreach ($staff as $member) {
                    if (get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member($member['staffid'])) {
                        continue;
                    }
                    $staff_departments = $this->departments_model->get_staff_departments($member['staffid'], true);
                    if (in_array($data['department'], $staff_departments)) {

                        foreach ($_attachments as $at) {
                            $this->emails_model->add_attachment(array(
                                'attachment' => get_upload_path_by_type('ticket') . $ticketid . '/' . $at['file_name'],
                                'filename' => $at['file_name'],
                                'type' => $at['filetype'],
                                'read' => true
                            ));
                        }

                        $this->emails_model->send_email_template('new-ticket-created-staff', $member['email'], $merge_fields, $ticketid);

                        if (get_option('receive_notification_on_new_ticket') == 1) {
                            $notified = add_notification(array(
                                'description' => 'not_new_ticket_created',
                                'touserid' => $member['staffid'],
                                'fromcompany' => 1,
                                'fromuserid' => null,
                                'link' => 'tickets/ticket/' . $ticketid,
                                'additional_data' => serialize(array(
                                    $data['subject']
                                ))
                            ));
                            if ($notified) {
                                array_push($notifiedUsers, $member['staffid']);
                            }
                        }
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }

            if ($admin != null) {
                // Admin opened ticket from admin area add the attachments to the email
                foreach ($_attachments as $at) {
                    $this->emails_model->add_attachment(array(
                        'attachment' => get_upload_path_by_type('ticket') . $ticketid . '/' . $at['file_name'],
                        'filename' => $at['file_name'],
                        'type' => $at['filetype'],
                        'read' => true
                    ));
                }
            }

            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
            $merge_fields = array_merge($merge_fields, get_ticket_merge_fields($template, $ticketid));

            $this->emails_model->send_email_template($template, $email, $merge_fields, $ticketid, $cc);

            do_action('after_ticket_added', $ticketid);
            logActivity('New Ticket Created [ID: ' . $ticketid . ']');


            if (PAINEL == QUANTUM && $assigned) {

                $preDefine = $this->db->select(["message"])->from("tblpredefinedreplies")->order_by("id", "ASC")->limit(2)->get()->result_array();
                $now = \Carbon\Carbon::now();

                if ($preDefine) {

                    $data_reply['message'] = $now->hour > 12 ? $preDefine[0]["message"] : $preDefine[1]["message"];

                } else {
                    $data_reply['message'] = "";
                }
                $data_reply["assign_to_current_user"] = $assigned;
                $data_reply["gambiarra_sinistra"] = $assigned;
                $data_reply['status'] = 2;

                $this->add_reply($data_reply, $ticketid, $assigned);


            }

            return $ticketid;
        }

        return false;
    }

    /**
     * Get latest 5 client tickets
     * @param  integer $limit Optional limit tickets
     * @param  mixed $userid client id
     * @return array
     */
    public function get_client_latests_ticket($limit = 5, $userid = '')
    {
        $this->db->select('tbltickets.userid, ticketstatusid, statuscolor, tblticketstatus.name as status_name,tbltickets.ticketid, subject, date');
        $this->db->from('tbltickets');
        $this->db->join('tblticketstatus', 'tblticketstatus.ticketstatusid = tbltickets.status', 'left');
        if (is_numeric($userid)) {
            $this->db->where('tbltickets.userid', $userid);
        } else {
            $this->db->where('tbltickets.userid', get_client_user_id());
        }
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Delete ticket from database and all connections
     * @param  mixed $ticketid ticketid
     * @return boolean
     */
    public function delete($ticketid)
    {
        $affectedRows = 0;
        do_action('before_ticket_deleted', $ticketid);
        // final delete ticket
        $this->db->where('ticketid', $ticketid);
        $this->db->delete('tbltickets');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            $this->db->where('ticketid', $ticketid);
            $attachments = $this->db->get('tblticketattachments')->result_array();
            if (count($attachments) > 0) {
                if (is_dir(get_upload_path_by_type('ticket') . $ticketid)) {
                    if (delete_dir(get_upload_path_by_type('ticket') . $ticketid)) {
                        foreach ($attachments as $attachment) {
                            $this->db->where('id', $attachment['id']);
                            $this->db->delete('tblticketattachments');
                            if ($this->db->affected_rows() > 0) {
                                $affectedRows++;
                            }
                        }
                    }
                }
            }

            $this->db->where('relid', $ticketid);
            $this->db->where('fieldto', 'tickets');
            $this->db->delete('tblcustomfieldsvalues');

            // Delete replies
            $this->db->where('ticketid', $ticketid);
            $this->db->delete('tblticketreplies');

            $this->db->where('rel_id', $ticketid);
            $this->db->where('rel_type', 'ticket');
            $this->db->delete('tblnotes');

            $this->db->where('rel_id', $ticketid);
            $this->db->where('rel_type', 'ticket');
            $this->db->delete('tbltags_in');

            // Get related tasks
            $this->db->where('rel_type', 'ticket');
            $this->db->where('rel_id', $ticketid);
            $tasks = $this->db->get('tblstafftasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
        }
        if ($affectedRows > 0) {
            logActivity('Ticket Deleted [ID: ' . $ticketid . ']');

            return true;
        }

        return false;
    }

    /**
     * Update ticket data / admin use
     * @param  mixed $data ticket $_POST data
     * @return boolean
     */
    public function update_single_ticket_settings($data)
    {
        $data['userid'] = get_client_contact_by_id($data['contactid'])->clientid;
        $data['contactid'] = get_client_contact_by_id($data['contactid'])->contactid;

        $affectedRows = 0;
        $data = do_action('before_ticket_settings_updated', $data);

        $ticketBeforeUpdate = $this->get_ticket_by_id($data['ticketid']);

        if (isset($data['custom_fields']) && count($data['custom_fields']) > 0) {
            if (handle_custom_fields_post($data['ticketid'], $data['custom_fields'])) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        if (handle_tags_save($tags, $data['ticketid'], 'ticket')) {
            $affectedRows++;
        }

        if (isset($data['priority']) && $data['priority'] == '' || !isset($data['priority'])) {
            $data['priority'] = 0;
        }

        if ($data['assigned'] == '') {
            $data['assigned'] = 0;
        }

        if (!isset($data['plantao']))
            $data['plantao'] = 0;

        if($data['subject'] == "")
            $data['subject'] = get_service1_name($data['service'])."/".get_service2_name($data['servicenv2']);

        if (isset($data['project_id']) && $data['project_id'] == '') {
            $data['project_id'] = 0;
        }

        if (isset($data['scheduled_date'])){
            if (!empty($data['scheduled_date'])) {
                $data['scheduled_date'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $data['scheduled_date'])
                    ->format('Y-m-d H:i');
            }else{
                $data['scheduled_date'] = null;
            }
        }

        if (isset($data['is_next_attend'])) {
            $data['is_next_attend'] = true;
        } else {
            $data['is_next_attend'] = false;
        }

        if(PAINEL == INORTE) {
            //Gambirinha akakak
            if (isset($data['clientid'])) {
                $data['userid'] = $data['clientid'];
            }
        }
        unset($data['clientid']);


        $this->db->where('ticketid', $data['ticketid']);
        $this->db->update('tbltickets', $data);
        if ($this->db->affected_rows() > 0) {
            do_action('ticket_settings_updated',
                array(
                    'ticket_id' => $data['ticketid'],
                    'original_ticket' => $ticketBeforeUpdate,
                    'data' => $data)
            );
            $affectedRows++;
        }

        $sendAssignedEmail = false;

        $current_assigned = $ticketBeforeUpdate->assigned;
        if ($current_assigned != 0) {
            if ($current_assigned != $data['assigned']) {
                if ($data['assigned'] != 0 && $data['assigned'] != get_staff_user_id()) {
                    $sendAssignedEmail = true;
                    $notified = add_notification(array(
                        'description' => 'not_ticket_reassigned_to_you',
                        'touserid' => $data['assigned'],
                        'fromcompany' => 1,
                        'fromuserid' => null,
                        'link' => 'tickets/ticket/' . $data['ticketid'],
                        'additional_data' => serialize(array(
                            $data['subject']
                        ))
                    ));
                    if ($notified) {
                        pusher_trigger_notification(array($data['assigned']));
                    }
                }
            }
        } else {
            if ($data['assigned'] != 0 && $data['assigned'] != get_staff_user_id()) {
                $sendAssignedEmail = true;
                $notified = add_notification(array(
                    'description' => 'not_ticket_assigned_to_you',
                    'touserid' => $data['assigned'],
                    'fromcompany' => 1,
                    'fromuserid' => null,
                    'link' => 'tickets/ticket/' . $data['ticketid'],
                    'additional_data' => serialize(array(
                        $data['subject']
                    ))
                ));

                if ($notified) {
                    pusher_trigger_notification(array($data['assigned']));
                }
            }
        }
        if ($sendAssignedEmail === true) {
            $this->load->model('emails_model');
            $merge_fields = array();

            $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $data['contactid']));
            $merge_fields = array_merge($merge_fields, get_ticket_merge_fields('ticket-assigned-to-admin', $data['ticketid']));

            $this->db->where('staffid', $data['assigned']);
            $assignedEmail = $this->db->get('tblstaff')->row()->email;
            $this->emails_model->send_email_template('ticket-assigned-to-admin', $assignedEmail, $merge_fields, $data['ticketid']);
        }
        if ($affectedRows > 0) {
            logActivity('Ticket Updated [ID: ' . $data['ticketid'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Função que verifica as funções ao alterar status do Ticket
     * @param $ticketid
     * @param $statusid
     * @return string
     */
    public function functions_change_ticket_status($ticketid,$statusid)
    {
        $status = $this->get_ticket_status($statusid);
        $ticket = $this->get_ticket_by_id($ticketid);
        $email = $this->staff_model->get($ticket->assigned)->email;
        $emails = $this->staff_model->get($ticket->assigned)->email_sec;

        $script = "";
        if($status->send_mail == "1")
            $script .= "<script>$('#SendMailModal').modal('show');</script>";
//            $this->emails_model->send_simple_email($email, $subject, $message);

        if($status->create_event == "1")
            $script .= "<script>$('#tomodal')[0].value = '".$email."; ".$emails."'; $('#cabecamodal')[0].value = 'Status do ticket alterado para: ".ticket_status_translate($statusid)."'; $('#newEventModal').modal('show');</script>";

        return $script;
    }

    /**
     * Virifica as regras ao mudar status do ticket
     * @param $ticketid
     * @param $statusid
     * @return array|bool
     */
    public function rules_change_ticket_status($ticketid,$statusid)
    {
        if($statusid == 3 && PAINEL == QUANTUM)
        {
            $resultcon = $this->db->query("SELECT tblservices.name as service,status FROM tbltickets INNER JOIN tblservices ON tbltickets.service = tblservices.serviceid WHERE tbltickets.ticketid = ".$ticketid)->row();
            $service = $resultcon->service;
            $idantigo = $resultcon->status;
            $message = "";
            $msg = "Não foi possivel alterar status para " . ticket_status_translate($statusid) . ".</br>";

            $script = '<script>$("#status_top").val("'.$idantigo.'");$("#status_top").selectpicker("refresh");$("#status").val("'.$idantigo.'");$("#status").selectpicker("refresh");</script>';

            if($service == "ND") {
                $message .= 'Escolha um serviço e salve.';
            }
            $uid = $this->db->select('userid')->where('ticketid', $ticketid)->get('tbltickets')->row('userid');
            $server_info = $this->db->where('description = "server_info"')->where("customer_id",$uid)->get('tblvault')->row();

            if($server_info == null || $server_info->server_address == "")
                $message .= "<hr>Sem informações do servidor do cliente.";

            if($message != "") {
                $msg .= $message.$script;
                return array('status'=>'falha','alert' => 'danger', 'message' => $msg);
            }
            else
                return true;
        }
        return true;
    }


    public function ticketdev_check_rules($ticketid, $statusid)
    {
        $auxiliador = 0;
        if(PAINEL == INORTE){
            $key_statuses = array(
                26 => 1, // DEV - Espera // Nao iniciado
                34 => 2, // DEV - Reanalise // Aguardando feedback
                24 => 3, // DEV - Teste // Em teste
                33 => 4, // DEV - Atendimento // Em progresso
                32 => 5, // DEV - Atendido // Concluido
            );
            if(array_key_exists($statusid, $key_statuses)) {
                $this->load->model('tasks_model');
                $tasks = $this->tasks_model->get_tasks_by_ticketid($ticketid);
                foreach ($tasks as $task) {
                    if ($key_statuses[$statusid] == $task['status']) {
                        $auxiliador++;
                    }
                }
                if ($auxiliador == 0)
                    return false;
            }
        }


        return true;
    }

    /**
     * C<ha></ha>nge ticket status
     * @param  mixed $id ticketid
     * @param  mixed $status status id
     * @return array
     */
    public function change_ticket_status($id, $status)
    {
        $script = "";
        $rules = $this->rules_change_ticket_status($id, $status);
        if($rules["status"] == "falha") {
            return $rules;
        }
        $this->db->where('ticketid', $id);
        $this->db->update('tbltickets', array(
            'status' => $status
        ));
        $alert = 'warning';
        $message = _l('ticket_status_changed_fail');
        if ($this->db->affected_rows() > 0) {
            $script = $this->functions_change_ticket_status($id, $status);
            $alert = 'success';
            $message = _l('ticket_status_changed_successfully');
            do_action('after_ticket_status_changed', array(
                'id' => $id,
                'status' => $status
            ));
            refresh_panel();
        }

        //insere as datas na tabela de logs de status
        $this->time_status_model->add($id, $status, get_staff_user_id());

        //// Data para o painelsuporte caso status seja alterado para EmAtendimento
        $this->hora_atendimento($id, $status);
        ////////////////////////////////////////

        return array(
            'alert' => $alert,
            'message' => $message.$script
        );
    }

    // Priorities

    /**
     * Get ticket priority by id
     * @param  mixed $id priority id
     * @return mixed     if id passed return object else array
     */
    public function get_priority($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('priorityid', $id);

            return $this->db->get('tblpriorities')->row();
        }

        return $this->db->get('tblpriorities')->result_array();
    }

    public function get_priority_without_atualizacao_if_not_admin()
    {
        if (is_admin() || is_developer()) {
            return $this->db->get('tblpriorities')->result_array();
        }

        return $this->db
            ->where('name <>', 'Atualização')
            ->get('tblpriorities')->result_array();
    }

    /**
     * Add new ticket priority
     * @param array $data ticket priority data
     * @return
     */
    public function add_priority($data)
    {
        $this->db->insert('tblpriorities', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Ticket Priority Added [ID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }

        return $insert_id;
    }

    /**
     * Update ticket priority
     * @param  array $data ticket priority $_POST data
     * @param  mixed $id ticket priority id
     * @return boolean
     */
    public function update_priority($data, $id)
    {
        $this->db->where('priorityid', $id);
        $this->db->update('tblpriorities', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Priority Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete ticket priorit
     * @param  mixed $id ticket priority id
     * @return mixed
     */
    public function delete_priority($id)
    {
        $current = $this->get($id);
        // Check if the priority id is used in tbltickets table
        if (is_reference_in_table('priority', 'tbltickets', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('priorityid', $id);
        $this->db->delete('tblpriorities');
        if ($this->db->affected_rows() > 0) {
            if (get_option('email_piping_default_priority') == $id) {
                update_option('email_piping_default_priority', '');
            }
            logActivity('Ticket Priority Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    // Predefined replies

    /**
     * Get predefined reply  by id
     * @param  mixed $id predefined reply id
     * @return mixed if id passed return object else array
     */
    public function get_predefined_reply($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tblpredefinedreplies')->row();
        }

        return $this->db->get('tblpredefinedreplies')->result_array();
    }

    /**
     * Add new predefined reply
     * @param array $data predefined reply $_POST data
     */
    public function add_predefined_reply($data)
    {
        $this->db->insert('tblpredefinedreplies', $data);
        $insertid = $this->db->insert_id();
        logActivity('New Predefined Reply Added [ID: ' . $insertid . ', ' . $data['name'] . ']');

        return $insertid;
    }

    /**
     * Update predefined reply
     * @param  array $data predefined $_POST data
     * @param  mixed $id predefined reply id
     * @return boolean
     */
    public function update_predefined_reply($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblpredefinedreplies', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Predefined Reply Updated [ID: ' . $id . ', ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete predefined reply
     * @param  mixed $id predefined reply id
     * @return boolean
     */
    public function delete_predefined_reply($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblpredefinedreplies');
        if ($this->db->affected_rows() > 0) {
            logActivity('Predefined Reply Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    // Ticket statuses
    /**
     * Get ticket status by id
     * @param  mixed $id status id
     * @return mixed     if id passed return object else array
     */

    public function get_ticket_status($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('ticketstatusid', $id);

            return $this->db->get('tblticketstatus')->row();
        }
        $this->db->order_by('statusorder', 'asc');

        return $this->db->get('tblticketstatus')->result_array();
    }

    public function get_dev_status(){
        return $this->db
            ->where_in('ticketstatusid', array(26, 24))
            ->get('tblticketstatus')->result_array();
    }

    public function get_pending_status(){
        return $this->db
            ->where_in('ticketstatusid', array(18))
            ->get('tblticketstatus')->result_array();
    }

    /**
     * Add new ticket status
     * @param array ticket status $_POST data
     * @return mixed
     */
    public function add_ticket_status($data)
    {
        $this->db->insert('tblticketstatus', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Ticket Status Added [ID: ' . $insert_id . ', ' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update ticket status
     * @param  array $data ticket status $_POST data
     * @param  mixed $id ticket status id
     * @return boolean
     */
    public function update_ticket_status($data, $id)
    {
        $this->db->where('ticketstatusid', $id);
        $this->db->update('tblticketstatus', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Status Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete ticket status
     * @param  mixed $id ticket status id
     * @return mixed
     */
    public function delete_ticket_status($id)
    {
        $current = $this->get_ticket_status($id);
        // Default statuses cant be deleted
        if ($current->isdefault == 1) {
            return array(
                'default' => true
            );
            // Not default check if if used in table
        } elseif (is_reference_in_table('status', 'tbltickets', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('ticketstatusid', $id);
        $this->db->delete('tblticketstatus');
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Status Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    // Ticket services
    public function get_service($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('serviceid', $id);

            return $this->db->get('tblservices')->row();
        }
        $this->db->order_by('name', 'asc');

        $array = $this->db->get('tblservices')->result_array();

        foreach ($array as $key => $a) {
            if ($a['name'] == 'ND') {
                //$aux = $a;
                unset($array[$key]);
                array_unshift($array, $a);
            }
        }

        return $array;
    }

    public function get_secondservice($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('serviceid', $id);

            return $this->db->get('tblsecondservice')->row();
        }
        $this->db->order_by('secondServiceid', 'desc');

        return $this->db->get('tblsecondservice')->result_array();
    }


    public function add_service($data)
    {
        $this->db->insert('tblservices', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Ticket Service Added [ID: ' . $insert_id . '.' . $data['name'] . ']');
        }

        return $insert_id;
    }

    public function add_servicenv2($data)
    {
        $this->db->insert('tblsecondservice', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Ticket Service Added [ID: ' . $insert_id . '.' . $data['name'] . ']');
        }

        return $insert_id;
    }

    public function update_service($data, $id)
    {
        $this->db->where('serviceid', $id);
        $this->db->update('tblservices', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Service Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function add_avaliacao($data, $ticketid){
        if(isset($data['nota_atendimento_desc'])) {
            $nota_atendimento_desc = $data['nota_atendimento_desc'];
            unset($data['nota_atendimento_desc']);
        }
        if(isset($data['nota_tecnico_desc'])) {
            $nota_tecnico_desc = $data['nota_tecnico_desc'];
            unset($data['nota_tecnico_desc']);
        }
        if(isset($data['nota_sistema_desc'])) {
            $nota_sistema_desc = $data['nota_sistema_desc'];
            unset($data['nota_sistema_desc']);
        }
        $this->db->where('ticketid', $ticketid);
        $this->db->update('tbltickets', $data);


        unset($data['nota_atendimento']);
        unset($data['nota_tecnico']);
        unset($data['nota_sistema']);
        $data['ticketid'] = $ticketid;
        $data['nota_atend_desc'] = $nota_atendimento_desc;
        $data['nota_tecnico_desc'] = $nota_tecnico_desc;
        $data['nota_sistema_desc'] = $nota_sistema_desc;
        $check = $this->db->where('ticketid', $ticketid)
            ->get('tbldescricaoavaliacao')->result();
        if($check){
            $this->db->where('ticketid', $ticketid);
            $this->db->update('tbldescricaoavaliacao', $data);
        }else{
            $query = $this->db->insert('tbldescricaoavaliacao', $data);
        }

        if($query || $this->db->affected_rows() > 0){
            logActivity('Avaliação realizada!');
            return true;
        }

        return false;
    }

    public function update_servicenv2($data, $id)
    {
        $this->db->where('secondServiceid', $id);
        $this->db->update('tblsecondservice', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Service Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function delete_service($id)
    {
        // o OU foi dicionado para caso o usuário tente deletar um serviço no qual já foi
        // inserido um serviço nível 2 
        if (is_reference_in_table('service', 'tbltickets', $id) || is_reference_in_table('serviceid', 'tblsecondservice', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('serviceid', $id);
        $this->db->delete('tblservices');
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Service Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    public function delete_servicenv2($id)
    {
        // verifica se o serviço nivel 2 está referenciado na tabela tickets
        // caso false, ele pode ser deletado
        if (is_reference_in_table('servicenv2', 'tbltickets', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('secondServiceid', $id);
        $this->db->delete('tblsecondservice');
        if ($this->db->affected_rows() > 0) {
            logActivity('Ticket Service Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @return array
     * Used in home dashboard page
     * Displays weekly ticket openings statistics (chart)
     */
    public function get_weekly_tickets_opening_statistics()
    {
        if (!is_admin()) {
            if (get_option('staff_access_only_assigned_departments') == 1) {
                $this->load->model('departments_model');
                $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                $departments_ids = array();
                if (count($staff_deparments_ids) == 0) {
                    $departments = $this->departments_model->get();
                    foreach ($departments as $department) {
                        array_push($departments_ids, $department['departmentid']);
                    }
                } else {
                    $departments_ids = $staff_deparments_ids;
                }
                if (count($departments_ids) > 0) {
                    $this->db->where('department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                }
            }
        }
        $this->db->where('CAST(date as DATE) >= "' . date('Y-m-d', strtotime('monday this week')) . '" AND CAST(date as DATE) <= "' . date('Y-m-d', strtotime('sunday this week')) . '"');

        $tickets = $this->db->get('tbltickets')->result_array();
        $chart = array(
            'labels' => get_weekdays(),
            'datasets' => array(
                array(
                    'label' => _l('home_weekend_ticket_opening_statistics'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor' => '#c53da9',
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => array(
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0
                    )
                )
            )
        );
        foreach ($tickets as $ticket) {
            $ticket_day = date('l', strtotime($ticket['date']));
            $i = 0;
            foreach (get_weekdays_original() as $day) {
                if ($ticket_day == $day) {
                    $chart['datasets'][0]['data'][$i]++;
                }
                $i++;
            }
        }

        return $chart;
    }

    public function add_spam_filter($data)
    {
        $this->db->insert('tblticketsspamcontrol', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return true;
        }

        return false;
    }

    public function edit_spam_filter($data)
    {
        $this->db->where('id', $data['id']);
        unset($data['id']);
        $this->db->update('tblticketsspamcontrol', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function delete_spam_filter($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblticketsspamcontrol');
        if ($this->db->affected_rows() > 0) {
            logActivity('Tickets Spam Filter Deleted');

            return true;
        }

        return false;
    }

    public function get_tickets_by_staffid($staffid = '', $where = false){

        if($staffid != '')
            $this->db->where('tblticketstimestatus.staffid', $staffid);

        $this->db->select('tbltickets.*, MIN(tblticketstimestatus.datetime), ANY_VALUE(tblstaff.staffid) as staffid, ANY_VALUE(tblstaff.firstname) as firstname'); // Tem que agrupar usando o MIN pq o mysql ta loko
        $this->db->join('tblticketstimestatus', 'tblticketstimestatus.ticketid = tbltickets.ticketid', 'INNER');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblticketstimestatus.staffid', 'INNER');
        if($where){
            $this->db->where($where);
        }
        $this->db->group_by('tbltickets.ticketid');
        return $this->db->get('tbltickets')->result_array();

    }

    public function get_tickets_assignes_disctinct()
    {
        $userid = "";
        if (is_partner(get_staff_user_id())){
            $userid = get_staff_user_id();
            return $this->db->query("SELECT DISTINCT(assigned) as assigned FROM tbltickets WHERE assigned =" . $userid)->result_array();
        }
        return $this->db->query("SELECT DISTINCT(assigned) as assigned FROM tbltickets WHERE assigned != 0")->result_array();
    }

    public function hora_atendimento($id, $status){

        if ($status == 1) {
            $this->db->where('horaatendimento.idticket', $id);
            $aux = $this->db->get('horaatendimento')->row();
            if ($aux != null) {
                $this->db->where('horaatendimento.idticket', $id);
                $this->db->update('horaatendimento', array(
                    'date_espera' => date('Y-m-d H:i:s'),
                    //'idticket' => $id,
                ));
            } else {
                $this->db->insert('horaatendimento', array(
                    'date_espera' => date('Y-m-d H:i:s'),
                    //'date_atendimento' => date('Y-m-d H:i:s'),
                    'idticket' => $id,
                ));
            }
        } else if ($status == 2 || $status == 27) {
            $this->db->where('horaatendimento.idticket', $id);
            $aux = $this->db->get('horaatendimento')->row();
            if ($aux != null) {
                $this->db->where('horaatendimento.idticket', $id);
                $this->db->update('horaatendimento', array(
                    'date_atendimento' => date('Y-m-d H:i:s'),
                    //'idticket' => $id,
                ));
            } else {
                $this->db->insert('horaatendimento', array(
                    //'date_espera' => date('Y-m-d H:i:s'),
                    'date_atendimento' => date('Y-m-d H:i:s'),
                    'idticket' => $id,
                ));
            }
        } else {
            $this->db->where('horaatendimento.idticket', $id);

            $this->db->delete('horaatendimento');

        }
    }

    public function verify_assigneds($ids){
        foreach ($ids as $id){
            $assigned = $this->db->select('assigned')
                ->where('ticketid', $id)
                ->get('tbltickets')->row();
            if($assigned->assigned == 0)
                return false;
        }
        return true;
    }

    public function verifyNewTicket($staffid = "")
    {
        if(boolval(get_option('bloqueio_tickets')))
        {
            $limit_hour = get_option('ticket_block_limit_time_attendance');
            $limit_quant = get_option('ticket_block_limit_number_attendance');

            $tickets = $this->db->query("SELECT DISTINCT tbltickets.ticketid FROM tbltickets INNER JOIN tblticketstimestatus ON tblticketstimestatus.ticketid = tbltickets.ticketid WHERE assigned = ".intval($staffid)." AND statusid = status AND status = 2 AND ((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(datetime)) > (".intval($limit_hour)."*60*60))")->result_array();
            $msg = array("status"=>true);
            if($tickets != NULL)
            {
                if(count($tickets) >= $limit_quant)
                    $msg = array("status"=>false);
            }
            return $msg;
        }
        else
            return array("status"=>true);
    }

    public function get_ticket_timestatus($status, $taskid){
        return $this->db
            ->select('datetime')
            ->where('statusid', $status)
            ->where('ticketid', $taskid)
            ->order_by('datetime DESC')
            ->limit(1)
            ->get('tblticketstimestatus')->row();
    }
}
