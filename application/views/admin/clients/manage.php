<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if(!is_partner()) {
                                if (has_permission('customers', '', 'create')) { ?>
                                    <a href="<?php echo admin_url('clients/client'); ?>"
                                       class="btn btn-info mright5 test pull-left display-block">
                                        <?php echo _l('new_client'); ?></a>
                                    <a href="<?php echo admin_url('clients/import'); ?>"
                                       class="btn btn-info pull-left display-block mright5 hidden-xs">
                                        <?php echo _l('import_customers'); ?></a>
                                <?php }
                                } ?>
                                    <?php if(!is_partner()){ ?>
                                      <a href="<?php echo admin_url('clients/all_contacts'); ?>" class="btn btn-info pull-left display-block mright5">
                                    <?php echo _l('customer_contacts'); ?></a>

                                        <a onclick="ativa_clients();" class="btn btn-info pull-left display-block mright5">
                                            <?php echo 'Atualiza'; ?></a>
                                    <?php } ?>
                                    <div class="visible-xs">
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-filter" aria-hidden="true"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-left" style="width:300px;">
                                            <li class="active"><a href="#" data-cview="all" onclick="dt_custom_view('','.table-clients',''); return false;"><?php echo _l('customers_sort_all'); ?></a></li>
                                            <li class="divider"></li>
                                            <?php if(count($groups) > 0){ ?>
                                            <li class="dropdown-submenu pull-left groups">
                                                <a href="#" tabindex="-1"><?php echo _l('customer_groups'); ?></a>
                                                <ul class="dropdown-menu dropdown-menu-left">
                                                    <?php foreach($groups as $group){ ?>
                                                    <li><a href="#" data-cview="customer_group_<?php echo $group['id']; ?>" onclick="dt_custom_view('customer_group_<?php echo $group['id']; ?>','.table-clients','customer_group_<?php echo $group['id']; ?>'); return false;"><?php echo $group['name']; ?></a></li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                            <div class="clearfix"></div>
                                            <li class="divider"></li>
                                            <?php } ?>
                                            <li class="dropdown-submenu pull-left invoice">
                                                <a href="#" tabindex="-1"><?php echo _l('invoices'); ?></a>
                                                <ul class="dropdown-menu dropdown-menu-left">
                                                    <?php foreach($invoice_statuses as $status){ ?>
                                                    <li>
                                                        <a href="#" data-cview="invoices_<?php echo $status; ?>" data-cview="1" onclick="dt_custom_view('invoices_<?php echo $status; ?>','.table-clients','invoices_<?php echo $status; ?>'); return false;"><?php echo _l('customer_have_invoices_by',format_invoice_status($status,'',false)); ?></a>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                            <div class="clearfix"></div>
                                            <li class="divider"></li>
                                            <li class="dropdown-submenu pull-left estimate">
                                                <a href="#" tabindex="-1"><?php echo _l('estimates'); ?></a>
                                                <ul class="dropdown-menu dropdown-menu-left">
                                                    <?php foreach($estimate_statuses as $status){ ?>
                                                    <li>
                                                        <a href="#" data-cview="estimates_<?php echo $status; ?>" onclick="dt_custom_view('estimates_<?php echo $status; ?>','.table-clients','estimates_<?php echo $status; ?>'); return false;">
                                                            <?php echo _l('customer_have_estimates_by',format_estimate_status($status,'',false)); ?>
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                            <div class="clearfix"></div>
                                            <li class="divider"></li>
                                            <li class="dropdown-submenu pull-left project">
                                                <a href="#" tabindex="-1"><?php echo _l('projects'); ?></a>
                                                <ul class="dropdown-menu dropdown-menu-left">
                                                    <?php foreach($project_statuses as $status){ ?>
                                                    <li>
                                                        <a href="#" data-cview="projects_<?php echo $status['id']; ?>" onclick="dt_custom_view('projects_<?php echo $status['id']; ?>','.table-clients','projects_<?php echo $status['id']; ?>'); return false;">
                                                            <?php echo _l('customer_have_projects_by',$status['name']); ?>
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                            <div class="clearfix"></div>
                                            <li class="divider"></li>
                                            <li class="dropdown-submenu pull-left proposal">
                                                <a href="#" tabindex="-1"><?php echo _l('proposals'); ?></a>
                                                <ul class="dropdown-menu dropdown-menu-left">
                                                    <?php foreach($proposal_statuses as $status){ ?>
                                                    <li>
                                                        <a href="#" data-cview="proposals_<?php echo $status; ?>" onclick="dt_custom_view('proposals_<?php echo $status; ?>','.table-clients','proposals_<?php echo $status; ?>'); return false;">
                                                            <?php echo _l('customer_have_proposals_by',format_proposal_status($status,'',false)); ?>
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                            <div class="clearfix"></div>
                                            <?php if(count($contract_types) > 0) { ?>
                                            <li class="divider"></li>
                                            <li class="dropdown-submenu pull-left contract_types">
                                                <a href="#" tabindex="-1"><?php echo _l('contract_types'); ?></a>
                                                <ul class="dropdown-menu dropdown-menu-left">
                                                    <?php foreach($contract_types as $type){ ?>
                                                    <li>
                                                        <a href="#" data-cview="contract_type_<?php echo $type['id']; ?>" onclick="dt_custom_view('contract_type_<?php echo $type['id']; ?>','.table-clients','contract_type_<?php echo $type['id']; ?>'); return false;">
                                                            <?php echo _l('customer_have_contracts_by_type',$type['name']); ?>
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                            <?php } ?>
                                            <?php if(count($customer_admins) > 0 && (has_permission('customers','','create') || has_permission('customers','','edit'))){ ?>
                                            <li class="divider"></li>
                                            <li class="dropdown-submenu pull-left responsible_admin">
                                                <a href="#" tabindex="-1"><?php echo _l('responsible_admin'); ?></a>
                                                <ul class="dropdown-menu dropdown-menu-left">
                                                    <?php foreach($customer_admins as $cadmin){ ?>
                                                    <li>
                                                        <a href="#" data-cview="responsible_admin_<?php echo $cadmin['staff_id']; ?>" onclick="dt_custom_view('responsible_admin_<?php echo $cadmin['staff_id']; ?>','.table-clients','responsible_admin_<?php echo $cadmin['staff_id']; ?>'); return false;">
                                                            <?php echo get_staff_full_name($cadmin['staff_id']); ?>
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <?php if(has_permission('customers','','view') || have_assigned_customers()) {
                                    $where_summary = '';
                                    $partner = '';
                                    if(!has_permission('customers','','view')){
                                        $where_summary = ' AND userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id='.get_staff_user_id().')';
                                    }
                                    if(is_partner() || (isset($_SESSION['partner_id'])) && $_SESSION['partner_id'] != 0){
                                        $partner = " and partner_id='" . get_staff_partner_id() . "' ";
                                    }
                                    ?>
                                    <hr class="hr-panel-heading" />
                                    <div class="row mbot15">
                                        <?php if((PAINEL != QUANTUM) || (PAINEL == QUANTUM && in_array(get_staff_user_id(), array(2, 36, 40, 42)))) {?>
                                        <div class="col-md-12">
                                            <h3 class="text-success no-margin"><?php echo _l('customers_summary'); ?></h3>
                                        </div>
                                        <div class="col-md-2 col-xs-6 border-right hide">
                                        <a href="#" onclick="dt_custom_view_client('.table-clients',0,'1');">
                                            <h3 class="bold"><?php echo total_rows('tblclients',($where_summary != '' ? substr($where_summary,5) : '')); ?></h3>
                                            <span class="text-dark"><?php echo _l('customers_summary_total'); ?></span>
                                        </a>
                                        </div>
                                        <div class="col-md-2 col-xs-6 border-right">
                                        <a href="#" onclick="dt_custom_view('1','.table-clients','active');">
                                            <h3 class="bold"><?php echo total_rows('tblclients', ($where_summary != '' ? substr($where_summary . ' active=1 ' . $partner,5) : ' active=1 ' . $partner)); ?></h3>
                                            <span class="text-success"><?php echo _l('active_customers'); ?></span>
                                            </a>
                                        </div>
                                        <div class="col-md-2 col-xs-6 border-right">
                                        <a href="#" onclick="dt_custom_view('0','.table-clients','active');">
                                            <h3 class="bold"><?php echo total_rows('tblclients', ($where_summary != '' ? substr($where_summary . ' active=0 ' . $partner,5) : ' active=0 ' . $partner)); ?></h3>
                                            <span class="text-danger"><?php echo _l('inactive_active_customers'); ?></span>
                                        </a>
                                        </div>
                                        <div class="col-md-2 col-xs-6 border-right hide">
                                            <h3 class="bold"><?php echo total_rows('tblcontacts','active=1'.$where_summary); ?></h3>
                                            <span class="text-info"><?php echo _l('customers_summary_active'); ?></span>
                                        </div>
                                        <div class="col-md-2  col-xs-6 border-right hide">
                                            <h3 class="bold"><?php echo total_rows('tblcontacts','active=0'.$where_summary); ?></h3>
                                            <span class="text-danger"><?php echo _l('customers_summary_inactive'); ?></span>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-2 col-xs-6 hide">
                                            <h3 class="bold"><?php echo total_rows('tblcontacts','last_login LIKE "'.date('Y-m-d').'%"'.$where_summary); ?></h3>
                                            <span class="text-muted">
                                                <?php if(count($contacts_logged_in_today)> 0){
                                                 $contactsTemplate = '';
                                                 foreach($contacts_logged_in_today as $contact){
                                                    $url = admin_url('clients/client/'.$contact['userid'].'?contactid='.$contact['id']);
                                                    $fullName = $contact['firstname'] . ' ' . $contact['lastname'];
                                                    $dateLoggedIn = _dt($contact['last_login']);
                                                    $html = "<a href='$url' target='_blank'>$fullName</a><br /><small>$dateLoggedIn</small><br />";
                                                    $contactsTemplate .= htmlspecialchars('<p class="mbot5">'.$html.'</p>');
                                                }
                                                ?>
                                                <i class="fa fa-user pointer" data-toggle="popover" data-title="<?php echo _l('customers_summary_logged_in_today'); ?>" data-html="true" data-content="<?php echo $contactsTemplate; ?>">
                                                </i>
                                                <?php } ?>
                                                <?php echo _l('customers_summary_logged_in_today'); ?></span>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="_filters _hidden_inputs hidden">
                                <?php

                                    echo form_hidden('active');

                                    foreach($groups as $group){
                                     echo form_hidden('customer_group_'.$group['id']);
                                 }
                                 foreach($contract_types as $type){
                                     echo form_hidden('contract_type_'.$type['id']);
                                 }
                                 foreach($invoice_statuses as $status){
                                     echo form_hidden('invoices_'.$status);
                                 }
                                 foreach($estimate_statuses as $status){
                                     echo form_hidden('estimates_'.$status);
                                 }
                                 foreach($project_statuses as $status){
                                    echo form_hidden('projects_'.$status['id']);
                                }
                                foreach($proposal_statuses as $status){
                                    echo form_hidden('proposals_'.$status);
                                }
                                foreach($customer_admins as $cadmin){
                                    echo form_hidden('responsible_admin_'.$cadmin['staff_id']);
                                }
                                ?>
                            </div>
                            <div class="panel_s">
                                <div class="panel-body">
                                    <?php if(!is_partner()) {?><a href="#" data-toggle="modal" data-target="#customers_bulk_action" class="btn btn-info mbot15"><?php echo _l('bulk_actions'); ?></a><?php }?>
                                   <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                       <div class="modal-content">
                                          <div class="modal-header">
                                             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                             <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                         </div>
                                         <div class="modal-body">
                                          <?php if(has_permission('customers','','delete')){ ?>
                                          <div class="checkbox checkbox-danger">
                                            <input type="checkbox" name="mass_delete" id="mass_delete">
                                            <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                        </div>
                                        <hr class="mass_delete_separator" />
                                        <?php } ?>
                                        <div id="bulk_change">
                                         <?php echo render_select('move_to_groups_customers_bulk[]',$groups,array('id','name'),'customer_groups','', array('multiple'=>true),array(),'','',false); ?>
                                         <p class="text-danger"><?php echo _l('bulk_action_customers_groups_warning'); ?></p>
                                     </div>
                                 </div>
                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                     <a href="#" class="btn btn-info" onclick="customers_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                 </div>
                             </div><!-- /.modal-content -->
                         </div><!-- /.modal-dialog -->
                     </div><!-- /.modal -->

                     <div class="clearfix"></div>
                     <?php
                     $table_data = array();
                     $_table_data = array(
                        '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>',
                        '#',
                        _l('clients_list_company'),
                        _l('contact_primary'),
                        _l('company_primary_email'),
                        _l('Versão'),
                        _l('clients_list_phone'),
                        _l('customer_active'),
                        _l('customer_groups'),
                         'CNPJ',
                         'Razão Social',
                        );

                   foreach($_table_data as $_t){
                        array_push($table_data,$_t);
                    }

//                    $custom_fields = get_custom_fields('customers',array('show_on_table'=>1));
//                    foreach($custom_fields as $field){
//                        array_push($table_data,$field['name']);
//                    }

                    $table_data = do_action('customers_table_columns',$table_data);

                    $_op = _l('options');

                    array_push($table_data, $_op);
                    render_datatable($table_data,'clients');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
    var CustomersServerParams = {};
    $.each($('._hidden_inputs._filters input'),function(){
     CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
 });

    $(function () {
    });

    var headers_clients = $('.table-clients').find('th');
    var not_sortable_clients = (headers_clients.length - 1);
    initDataTable('.table-clients', window.location.href, [], [], CustomersServerParams,<?php echo do_action('customers_table_default_order',json_encode(array(2,'ASC'))); ?>, [1]);
    dt_custom_view('1','.table-clients','active');
    function customers_bulk_action(event) {
        var r = confirm(appLang.confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var mass_delete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {};
            if(mass_delete == false || typeof(mass_delete) == 'undefined'){
                data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
                if (data.groups.length == 0) {
                    data.groups = 'remove_all';
                }
            } else {
                data.mass_delete = true;
            }
            var rows = $('.table-clients').find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            $(event).addClass('disabled');
            setTimeout(function(){
              $.post(admin_url + 'clients/bulk_action', data).done(function() {
                 window.location.reload();
             });
          },50);
        }
    }

    function dt_custom_view_client(table, column, val) {
        var tableApi = $(table).DataTable();
//        console.log(tableApi.column(column).data());
        tableApi.column(column).search(val).draw();
    }
    
    function ativa_clients() {

        $.alert({
            columnClass: 'col-md-12 ',
            title: 'Selecione os Clientes para Atualização',
            // escapeKey: true,
            backgroundDismiss: true,
            type: 'red',
            typeAnimated: true,
            content: function(){
                var self = this;
                self.setContent('');
                return $.ajax({
                    url: admin_url + 'clients/get_ativa_clients/',
                    dataType: 'html',
                    method: 'get'
                }).done(function (response) {
                    self.setContentAppend(response);
                }).fail(function(){
                    // self.setContentAppend('<div>Fail!</div>');
                }).always(function(){
                    // self.setContentAppend('<div>Always!</div>');
                });
            },
            onContentReady: function(){
                initDataTableOffline('#atualiza', [0, 'asc'], [], [0, 1, 2, 3, 4, 5]);
            }
        });
    }
    
    function list_versoes(userid) {
        $.alert({
            columnClass: 'col-md-6 col-md-offset-3 ',
            title: 'Versões Atuais',
            // escapeKey: true,
            backgroundDismiss: true,
            type: 'green',
            typeAnimated: true,
            content: function(){
                var self = this;
                self.setContent('');
                return $.ajax({
                    url: admin_url + 'clients/get_versoes/' + userid,
                    dataType: 'html',
                    method: 'get'
                }).done(function (response) {
                    self.setContentAppend(response);
                }).fail(function(){
                    // self.setContentAppend('<div>Fail!</div>');
                }).always(function(){
                    // self.setContentAppend('<div>Always!</div>');
                });
            },
            onContentReady: function(){
                initDataTableOffline('#versoes', [0, 'asc'], [], []);
            }
        });
    }


</script>
</body>
</html>
