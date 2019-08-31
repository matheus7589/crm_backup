<?php
defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = array(
    'tbltickets.ticketid',
    'subject',
    'name_soli as name',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltickets.ticketid and rel_type="ticket" ORDER BY tag_order ASC) as tags',
    'tbldepartments.name as department_name',
    'tblservices.name as service_name',
    'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
    'status',
    'priority',
    'lastreply',
    'tbltickets.date',
    '(SELECT GROUP_CONCAT(tblstafftasks.id SEPARATOR " - ") FROM tblstafftasks WHERE tbltickets.ticketid = tblstafftasks.rel_id and tblstafftasks.rel_type = "ticket"  ORDER BY tblstafftasks.id ASC) as tasks'
);

$taskColumn = 11;
$companyColumn = 6;
$tagsColumns = 3;
if ($this->_instance->input->get('bulk_actions')) {
    array_unshift($aColumns, '1');
    $companyColumn++;
    $tagsColumns++;
    $taskColumn++;
}

$additionalSelect = array(
    'adminread',
    'tbltickets.userid',
    'statuscolor',
    'tbltickets.name as ticket_opened_by_name',
    'tbltickets.email',
    'tbltickets.userid',
    'assigned',
    'tblclients.cnpj_or_cpf',
    'tblclients.social_reason',
    'tblclients.company',
    'tbltickets.partner_id as part_id',
    'tblclients.active as active',
);

$join = array(
    'LEFT JOIN tblservices ON tblservices.serviceid = tbltickets.service',
    'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbltickets.department',
    'LEFT JOIN tblticketstatus ON tblticketstatus.ticketstatusid = tbltickets.status',
    'LEFT JOIN tblclients ON tblclients.userid = tbltickets.userid',
    'LEFT JOIN tblpriorities ON tblpriorities.priorityid = tbltickets.priority',
);

$custom_fields = get_custom_fields('tickets', array(
    'show_on_table' => 1,
));
$i             = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_'.$i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_'.$i;
    }
    array_push($aColumns, 'ctable_'.$i.'.value as '.$select_as);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $i . ' ON tbltickets.ticketid = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

$where = array();
$filter = array();

$all_partners = false;
$avalia_atendimento = (has_permission('avaliacao_atendimento', '', 'view') && !is_partner());
if(isset($_SESSION['all_partners'])){
    $all_partners = true;
}


if (isset($userid) && $userid != '') {
    array_push($where, 'AND tbltickets.userid = ' . $userid);
} elseif (isset($by_email)) {
    array_push($where, 'AND tbltickets.email = "'.$by_email.'"');
}
if (isset($where_not_ticket_id)) {
    array_push($where, 'AND tbltickets.ticketid != ' . $where_not_ticket_id);
}
if($this->_instance->input->post('partner_id') && $all_partners == false)
{
    array_push($where, ' AND tbltickets.partner_id = ' . $this->_instance->input->post('partner_id'));
}
else
{
    if($this->_instance->input->post('dev_only')){ //Gambira pra mostrar os status dev pro parceiro
        $statuses = $this->_instance->tickets_model->get_dev_status();
        $aux = '';
        foreach ($statuses as $status){
            $aux .= $status['ticketstatusid'] . ', ';
        }

        if($this->_instance->input->post('ticket_status_24') || $this->_instance->input->post('ticket_status_26')){
            if($this->_instance->input->post('ticket_status_24')){
                array_push($where, ' AND tbltickets.status = 24');
            }else{
                array_push($where, ' AND tbltickets.status = 26');
            }
        }else{
            array_push($where, ' AND tbltickets.status in (' . substr($aux, 0, (strlen($aux)-2)) . ')');
        }

//        if($all_partners == false)
            array_push($where, ' AND tbltickets.partner_id != ' . get_staff_partner_id());
    }
    else if($this->_instance->input->post('pending_only')) {
        $statuses = $this->_instance->tickets_model->get_pending_status();
        $aux = '';
        foreach ($statuses as $status){
            $aux .= $status['ticketstatusid'] . ', ';
        }
        array_push($where, ' AND tbltickets.status in (' . substr($aux, 0, (strlen($aux)-2)) . ')');
//        if ($all_partners == false)
            array_push($where, ' AND tbltickets.partner_id = ' . get_staff_partner_id());
        array_push($where, ' AND assigned = ' . get_staff_user_id());
    } else { // filtro normal do parceiro
//        if($this->_instance->input->post('ticket_status_24') || $this->_instance->input->post('ticket_status_26')){
//            // outra gambira para o filtro de status da tabela de DEV parceiro
//            $partner = ' 0 ';
//        }else{
//            $partner = get_staff_partner_id();
//        }
        if (($all_partners == false && $avalia_atendimento == false) || $all_partners == false && is_admin()) {
            $twhere = ' AND tbltickets.partner_id = ' . get_staff_partner_id();

            array_push($where, $twhere);
        }
    }
}

if ($this->_instance->input->post('project_id')) {
    array_push($where, 'AND project_id = ' . $this->_instance->input->post('project_id'));
}

if ($this->_instance->input->post('plantao')) {
    array_push($where, 'AND plantao = ' . $this->_instance->input->post('plantao'));
}
if ($this->_instance->input->post('atualizacao')) {
    array_push($where, 'AND priority = ' . $this->_instance->input->post('atualizacao'));
}

if($this->_instance->input->post('dev_only')){
    $statuses = $this->_instance->tickets_model->get_dev_status();
}else{
    $statuses = $this->_instance->tickets_model->get_ticket_status();
}

$_statuses = array();
foreach ($statuses as $__status) {
    if ($this->_instance->input->post('ticket_status_'.$__status['ticketstatusid'])) {
        array_push($_statuses, $__status['ticketstatusid']);
    }
}

if (count($_statuses) > 0) {
    array_push($filter, 'AND status IN (' . implode(', ', $_statuses) . ') ');
}

if ($this->_instance->input->post('my_tickets')) {
    array_push($where, 'AND assigned = ' . get_staff_user_id());
}

$assignees = $this->_instance->tickets_model->get_tickets_assignes_disctinct();
$_assignees = array();
foreach ($assignees as $__assignee) {
    if ($this->_instance->input->post('ticket_assignee_'.$__assignee['assigned'])) {
        array_push($_assignees, $__assignee['assigned']);
    }
}
if (count($_assignees) > 0) {
    array_push($filter, 'AND assigned IN (' . implode(', ', $_assignees) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND ('.prepare_dt_filter($filter).')');
}
// If userid is set, the the view is in client profile, should be shown all tickets
if (!is_admin() && !has_permission('avaliacao_atendimento', '', 'view')) {
    if (get_option('staff_access_only_assigned_departments') == 1) {
        $this->_instance->load->model('departments_model');
        $staff_deparments_ids = $this->_instance->departments_model->get_staff_departments(get_staff_user_id(), true);
        $departments_ids = array();
        if (count($staff_deparments_ids) == 0) {
            $departments = $this->_instance->departments_model->get();
            foreach ($departments as $department) {
                array_push($departments_ids, $department['departmentid']);
            }
        } else {
            $departments_ids = $staff_deparments_ids;
        }
        if (count($departments_ids) > 0) {
            array_push($where, 'AND department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="'.get_staff_user_id().'")');
        }
    }
}

$sIndexColumn = 'ticketid';
$sTable       = 'tbltickets';
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];


$rResult = $result['rResult'];

$p_ini = '<a ';
$p_end = '</a>';

foreach ($rResult as $aRow) {
    $row = array();
//    $partner_id = 0;
    if(PAINEL == INORTE){
        if(is_partner()){
            $p_ini = '<p ';
            $p_end = '</p>';
        }
    }
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] == '1') {
            $_data = '<div class="checkbox"><input type="checkbox" value="'.$aRow['tbltickets.ticketid'].'"><label></label></div>';
        } elseif ($aColumns[$i] == 'lastreply') {
            if ($aRow[$aColumns[$i]] == null) {
                $_data = _l('ticket_no_reply_yet');
            } else {
                $_data = _dt($aRow[$aColumns[$i]]);
                //$_data = time_ago_specific($aRow[$aColumns[$i]]);
            }
        } elseif ($aColumns[$i] == 'subject' || $aColumns[$i] == 'tbltickets.ticketid') {
            // Ticket is assigned
            if ($aRow['assigned'] != 0) {
                if ($aColumns[$i] != 'tbltickets.ticketid') {
                    $_data .= $p_ini . ' href="' . admin_url('profile/' . $aRow['assigned']) . '" data-toggle="tooltip" title="' . get_staff_full_name($aRow['assigned']) . '" class="pull-left mright5" target="_blank">' . staff_profile_image($aRow['assigned'], array(
                            'staff-profile-image-xs'
                        )) . $p_end;
                }
            }
            $_data = '<a ' . ' href="' . admin_url('tickets/ticket/' . $aRow['tbltickets.ticketid']) . '" class="valign">' . $_data . '</a>';
        } elseif ($i == $tagsColumns) {
            $_data = render_tags($_data);
        } elseif ($i == $companyColumn) {
            if ($aRow['userid'] != 0) {
                $active = ($aRow['active']) ? '' : 'class="label label-danger" data-toggle="tooltip" data-placement="top" title="Cliente Desativado"';
                $_data = $p_ini . ' href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank" ' . $active . '>' . $aRow['company'] . $p_end;
            } else {
                $_data = $aRow['ticket_opened_by_name'];
            }
        } elseif ($aColumns[$i] == 'status') {
            $_data = '<span class="label inline-block" style="border:1px solid ' . $aRow["statuscolor"] . '; color:' . $aRow['statuscolor'] . '">' . ticket_status_translate($aRow['status']) . '</span>';
        } elseif ($aColumns[$i] == 'tbltickets.date') {
            $_data = _dt($_data);
        } elseif ($aColumns[$i] == 'priority') {
            $_data = ticket_priority_translate($aRow['priority']);
        }elseif($i == $taskColumn){
            $auxiliar = explode(" - ", $_data);
            $auxiliar = array_filter($auxiliar, function($value) { return $value !== ''; });
            $aux = '';
            //unset($_data);
            foreach($auxiliar as $tarefa){
                $aux .= '<a target="_blank" href="' . admin_url('tasks/view/' . $tarefa) . '"><span class="label inline-block" style="border:1px solid green; color: green">' . $tarefa . '</span></a>';
                $aux .= ' ';
            }
            $_data = $aux;
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;

        if ($aRow['adminread'] == 0) {
            $row['DT_RowClass'] = 'text-danger bold';
        }

    }
    $disableEdit = 'tickets/ticket/' . $aRow['tbltickets.ticketid'];
    $disableDelete = 'tickets/delete/' . $aRow['tbltickets.ticketid'];
    $disableButton = array();
    $delete_class = '_delete';
    if(($aRow['part_id'] != get_staff_partner_id()) && is_partner()){
        if(PAINEL == INORTE) {
            $disableEdit = '#';
            $disableDelete = '#';
            $disableButton['disabled'] = 'disabled';
            $delete_class = '';
        }
    }

    $options = icon_btn($disableEdit, 'pencil-square-o', 'custom-btn-default', $disableButton);
    $options .= icon_btn($disableDelete, 'remove', 'btn-danger '. $delete_class, $disableButton);

    $row[]   = $options;

    $output['aaData'][] = $row;
}
