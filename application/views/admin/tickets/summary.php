<div class="row">
 <?php
 $statuses = $this->tickets_model->get_ticket_status();
 ?>
 <div class="_filters _hidden_inputs hidden tickets_filters">
  <?php
  echo form_hidden('my_tickets', in_array('my_tickets', $this->session->tempdata('default_tickets_list_statuses')) || in_array('my_tickets', $default_tickets_list_statuses));
  if(is_admin()){
    $ticket_assignees = $this->tickets_model->get_tickets_assignes_disctinct();
    foreach($ticket_assignees as $assignee){
      echo form_hidden('ticket_assignee_'.$assignee['assigned'], in_array($assignee['assigned'], $this->session->tempdata('assigned')) ? true : false) ;
    }
  }
  foreach($statuses as $status){
    $val = '';
    if($chosen_ticket_status != ''){
      if($chosen_ticket_status == $status['ticketstatusid']){
        $val = $chosen_ticket_status;
      }
    } else {
      if(in_array($status['ticketstatusid'], $default_tickets_list_statuses)){
        $val = 1;
      }
    }
    echo form_hidden('ticket_status_'.$status['ticketstatusid'],$val);
  } ?>
</div>
<div class="col-md-12">
  <h4 class="text-success no-margin"><?php echo _l('tickets_summary');
  ?></h4>
  </div>
  <?php
  $where = '';
  if (!is_admin() && !has_permission('avaliacao_atendimento', '', 'view')) {
    if (get_option('staff_access_only_assigned_departments') == 1) {
      $departments_ids = array();
      if (count($staff_deparments_ids) == 0) {
        $departments = $this->departments_model->get();
        foreach($departments as $department){
          array_push($departments_ids,$department['departmentid']);
        }
      } else {
       $departments_ids = $staff_deparments_ids;
     }
     if(count($departments_ids) > 0){
      $where = 'AND department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="'.get_staff_user_id().'")';
    }
  }
}

$statuses_to_show_all = array(4, 24, 26, 31, 32, 33, 34);
//$aux_partner_statuses =  ' AND tbltickets.partner_id = ' . get_staff_partner_id();

foreach($statuses as $status){
  $_where = '';
  $partner = '';
  if(!isset($_SESSION['all_partners'])) {
      if (isset($cpf_cnpj) && $cpf_cnpj != '') {
          $partner = ' AND tbltickets.partner_id = ' . $cpf_cnpj;
      } else {
          $partner = ' AND tbltickets.partner_id = ' . get_staff_partner_id();
          if((has_permission('avaliacao_atendimento', '', 'view') && !is_partner()) && !is_admin()){
                $partner = '';
          }
      }
  }
  if($where == ''){
    $_where = 'status='.$status['ticketstatusid'] . $partner;
  } else{
    $_where = 'status='.$status['ticketstatusid'] . ' '.$where . $partner;
  }
  if(isset($project_id)){
    $_where = $_where . ' AND project_id='.$project_id;
  }
  ?>
  <div class="col-md-2 col-xs-6 mbot15 border-right">
    <a href="#" data-cview="ticket_status_<?php echo $status['ticketstatusid']; ?>" onclick="dt_custom_view('ticket_status_<?php echo $status['ticketstatusid']; ?>','.tickets-table','ticket_status_<?php echo $status['ticketstatusid']; ?>',true); return false;">
      <h3 class="bold"><?php echo total_rows('tbltickets',$_where); ?></h3>
      <span style="color:<?php echo $status['statuscolor']; ?>">
        <?php echo ticket_status_translate($status['ticketstatusid']); ?>
      </span>
    </a>
  </div>
<?php } ?>
<?php if(PAINEL == QUANTUM){?>
    <div class="col-md-2 col-xs-6 mbot15 border-right">
        <a href="" onClick="return false;" data-toggle="collapse" data-target="#categories" aria-expanded="false" aria-controls="categories">
            <h3 class="bold"><?php echo total_rows('tbltickets', ('plantao=1 OR priority=5')); ?></h3>
            <span style="color:#c0c0c0">
                CATEGORIAS
            </span>
        </a>
    </div>
</div>
<div class="row collapse" id="categories">
    <hr class="hr-panel-heading" />
    <div class="col-md-2 col-xs-6 mbot15 border-right">
        <a href="#" data-cview="ticket_status_38" onclick="dt_custom_view('1','.tickets-table','plantao',true); return false;">
            <h3 class="bold"><?php echo total_rows('tbltickets', ('plantao=1')); ?></h3>
            <span style="color:#f11327">
                PLANTÃO
            </span>
        </a>
    </div>
    <div class="col-md-2 col-xs-6 mbot15 border-right">
        <a href="#" data-cview="ticket_status_38" onclick="dt_custom_view('5','.tickets-table','atualizacao',true); return false;">
            <h3 class="bold"><?php echo total_rows('tbltickets', ('priority=5')); ?></h3>
            <span style="color:#f11327">
                Atualização
            </span>
        </a>
    </div>
<?php }?>
</div>
<hr class="hr-panel-heading" />
