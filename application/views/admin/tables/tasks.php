<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionEdit = has_permission('tasks', '', 'edit');
$bulkActions = $this->_instance->input->get('bulk_actions');

$aColumns = array(
    'name',
    'startdate',
    '(SELECT date from tbltickets where rel_id = tbltickets.ticketid) as  data_ticket',
//    'duedate',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tblstafftasks.id and rel_type="task" ORDER by tag_order ASC) as tags',
    '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM tblstafftaskassignees JOIN tblstaff ON tblstaff.staffid = tblstafftaskassignees.staffid WHERE taskid=tblstafftasks.id ORDER BY tblstafftaskassignees.staffid) as assignees',
    'priority',
    'status'
);

if ($bulkActions) {
    array_unshift($aColumns, '1');
}

$sIndexColumn = "id";
$sTable       = 'tblstafftasks';

$where = array();
$join          = array();

include_once(APPPATH . 'views/admin/tables/includes/tasks_filter.php');

$custom_fields = get_table_custom_fields('tasks');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON tblstafftasks.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

$aColumns = do_action('tasks_table_sql_columns', $aColumns);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
        'tblstafftasks.id',
        'rel_type',
        'rel_id',
        '(CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, \' (\',tblexpenses.expense_name,\')\') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END) as rel_name',
        'billed',
        '(SELECT staffid FROM tblstafftaskassignees WHERE taskid=tblstafftasks.id AND staffid='.get_staff_user_id().') as is_assigned',
        '(SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM tblstafftaskassignees WHERE taskid=tblstafftasks.id ORDER BY tblstafftaskassignees.staffid) as assignees_ids'
    )
);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();

    if ($bulkActions) {
        $row[] = '<div class="checkbox"><input type="checkbox" value="'.$aRow['id'].'"><label></label></div>';
    }

    $outputName = '<a href="'.admin_url('tasks/view/'.$aRow['id']).'" class="display-block main-tasks-table-href-name'.(!empty($aRow['rel_id']) ? ' mbot5' : '').'" onclick="init_task_modal(' . $aRow['id'] . ', 0); return false;">' . $aRow['name'] . '</a>';
    if ($aRow['rel_name']) {

        $link = '#';

        $relName = $aRow['rel_name'];
        if($aRow['rel_type'] == 'customer'){
            $link = admin_url('clients/client/'.$aRow['rel_id']);
        } else if($aRow['rel_type'] == 'invoice'){
            $link = admin_url('invoices/list_invoices/'.$aRow['rel_id']);
            $relName = format_invoice_number($relName);
        } else if($aRow['rel_type'] == 'project'){
            $link = admin_url('projects/view/'.$aRow['rel_id']);
        } else if($aRow['rel_type'] == 'estimate'){
            $link = admin_url('estimates/list_estimates/'.$aRow['rel_id']);
            $relName = format_estimate_number($relName);
        } else if($aRow['rel_type'] == 'contract'){
            $link = admin_url('contracts/contract/'.$aRow['rel_id']);
        } else if($aRow['rel_type'] == 'ticket'){
            $link = admin_url('tickets/ticket/'.$aRow['rel_id']);
        } else if($aRow['rel_type'] == 'expense'){
            $link = admin_url('expenses/list_expenses/'.$aRow['rel_id']);
        } else if($aRow['rel_type'] == 'lead'){
            $link = admin_url('leads/index/'.$aRow['rel_id']);
        } else if($aRow['rel_type'] == 'proposal') {
            $link = admin_url('proposals/list_proposals/'.$aRow['rel_id']);
            $relName = format_proposal_number($relName);
        }
        $outputName .= '<span class="hide"> - </span><a class="text-muted" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . $link . '">' . $relName . '</a>';
    }

    $row[] = $outputName;

    $row[] = _d($aRow['startdate']);

    $row[] = _d($aRow['data_ticket']);

    $row[] = render_tags($aRow['tags']);

    $outputAssignees = '';

    $assignees        = explode(',', $aRow['assignees']);
    $assigneeIds        = explode(',', $aRow['assignees_ids']);
    $export_assignees = '';
    foreach ($assignees as $key => $assigned) {
        $assignee_id = $assigneeIds[$key];
        if ($assigned != '') {
            $outputAssignees .= '<a href="' . admin_url('profile/' . $assignee_id) . '">' .
            staff_profile_image($assignee_id, array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $assigned
            )) . '</a>';
            // For exporting
            $export_assignees .= $assigned . ', ';
        }
    }
    if ($export_assignees != '') {
        $outputAssignees .= '<span class="hide">' . mb_substr($export_assignees, 0, -2) . '</span>';
    }

    $row[] = $outputAssignees;

    $row[] = '<span class="text-' . get_task_priority_class($aRow['priority']) . ' inline-block">' . task_priority($aRow['priority']) . '</span>';

    $status = get_task_status_by_id($aRow['status']);
    $outputStatus = '<span class="inline-block label" style="color:'.$status['color'].';border:1px solid '.$status['color'].'" task-status-table="'.$aRow['status'].'">' . $status['name'];

    if ($aRow['status'] == 5) {
        $outputStatus .= '<i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" ></i>';
    } else {
        $outputStatus .= '<i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
    }

    $outputStatus .= '</span>';

    $row[] = $outputStatus;

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $hook_data = do_action('tasks_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook_data['output'];

    $options = '';

    if ($hasPermissionEdit) {
        $options .= icon_btn('#', 'pencil-square-o', 'btn-default pull-right mleft5', array(
            'onclick' => 'edit_task(' . $aRow['id'] . '); return false'
        ));
    }

    $class = 'btn-success no-margin';

    $tooltip        = '';
    if ($aRow['billed'] == 1 || !$aRow['is_assigned'] || $aRow['status'] == 5) {
        $class = 'btn-default disabled';
        if ($aRow['status'] == 5) {
            $tooltip = ' data-toggle="tooltip" data-title="' . format_task_status($aRow['status'], false, true) . '"';
        } elseif ($aRow['billed'] == 1) {
            $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_billed_cant_start_timer') . '"';
        } elseif (!$aRow['is_assigned']) {
            $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_start_timer_only_assignee') . '"';
        }
    }

    $atts  = array(
        'onclick' => 'timer_action(this,' . $aRow['id'] . '); return false'
    );

    if ($timer = $this->_instance->tasks_model->is_timer_started($aRow['id'])) {
        $options .= icon_btn('#', 'clock-o', 'btn-danger pull-right no-margin', array(
            'onclick' => 'timer_action(this,' . $aRow['id'] . ',' . $timer->id . '); return false'
        ));
    } else {
        $options .= '<span' . $tooltip . ' class="pull-right">' . icon_btn('#', 'clock-o', $class . ' no-margin', $atts) . '</span>';
    }

    $row[]              = $options;

    $rowClass = '';
//    if ((!empty($aRow['data_ticket']) && $aRow['data_ticket'] < date('Y-m-d')) && $aRow['status'] != 5) {
//        $rowClass = 'text-danger bold ';
//    }

    $row['DT_RowClass'] = $rowClass;

    $output['aaData'][] = $row;
}
