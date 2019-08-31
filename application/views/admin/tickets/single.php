<?php init_head(); ?>
<style>
    #ribbon_<?php echo $ticket->ticketid; ?> span::before {
        border-top: 3px solid <?php echo $ticket->statuscolor; ?>;
        border-left: 3px solid <?php echo $ticket->statuscolor; ?>;
    }

    .btn-default {
        color: #333;
        background-color: #fff;
        border-color: #ccc;
    }

    .btn {
        text-transform: uppercase;
        font-size: 13.5px;
        outline-offset: 0;
        padding: 5px 10px !important;
        border: 1px solid transparent ;
        transition: all .15s ease-in-out;
        -o-transition: all .15s ease-in-out;
        -moz-transition: all .15s ease-in-out;
        -webkit-transition: all .15s ease-in-out;
    }

</style>
<?php set_ticket_open($ticket->adminread, $ticket->ticketid);
$disabled = ' disabled = "disabled" ';
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
<!--                        --><?php //echo site_url(); ?>
                        <?php echo '<div class="ribbon" id="ribbon_' . $ticket->ticketid . '"><span style="background:' . $ticket->statuscolor . '">' . ticket_status_translate($ticket->ticketstatusid) . '</span></div>'; ?>
                        <ul class="nav nav-tabs no-margin" role="tablist">
                            <li role="presentation"
                                class="<?php if (!$this->session->flashdata('active_tab_settings')) {
                                    echo 'active';
                                } ?>">
                                <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
                                    <?php echo _l('ticket_single_settings'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="<?php if ($this->session->flashdata('active_tab')) {
                                echo 'active';
                            } ?>">
                                <a href="#addreply" aria-controls="addreply" role="tab" data-toggle="tab">
                                    <?php echo _l('ticket_single_add_reply'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tasks" aria-controls="tasks" role="tab" data-toggle="tab">
                                    <?php echo _l('tasks'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#othertickets" aria-controls="othertickets" role="tab" data-toggle="tab">
                                    <?php echo _l('ticket_single_other_user_tickets'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#note" aria-controls="note" role="tab" data-toggle="tab">
                                    <?php echo _l('ticket_single_add_note'); ?>
                                </a>
                            </li>
                            <?php if(PAINEL == QUANTUM){ ?>
                            <?php if(is_admin()){ ?>
                                <li role="presentation">
                                    <a href="" onclick="$('#corrigedatam').modal('show'); return false;">
                                        Corrigir datas
                                    </a>
                                </li>
                            <?php }?>
                            <li role="presentation">
                                <a href="#" onclick="$('#InfoServer').modal('show');">Servidor</a>
                            </li>
                            <?php }?>
                        </ul>
                    </div>
                </div>
                <?php if(PAINEL == INORTE){ ?>
                    <?php if($client->observation != '' || $client->observation != null){ ?>
                    <div class="panel_s">
                        <div class="painel-body">
                            <div class="observation">
                                <div class="observation-title">
                                    Observação do Cliente
                                </div>
                                <div class="observation-content">
                                    <?php echo $client->observation ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                <?php } else{ ?>
                    <div class="panel_s">
                        <div class="painel-body">
                            <div class="observation">
                                <div class="observation-title">
                                    Observação do Cliente
                                </div>
                                <div class="observation-content">
                                    <?php echo $client->observation ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="panel_s">
                    <div class="panel-body">
<!--                        --><?php //echo $ticket->partner_id . ' - ' . get_staff_partner_id(); ?>
                        <fieldset <?php if(($ticket->partner_id != get_staff_partner_id()) && is_partner()) echo 'disabled="disabled"'; ?>>
                        <div class="row">
                            <div class="col-md-8">

                                <h3 id="ticket_subject" class="mtop25">#<?php echo $ticket->ticketid; ?>
                                    - <?php echo $ticket->subject; ?>
                                    <?php if ($ticket->project_id != 0) {
                                        echo '<br /><small>' . _l('ticket_linked_to_project', '<a href="' . admin_url('projects/view/' . $ticket->project_id) . '">' . get_project_name_by_id($ticket->project_id) . '</a>') . '</small>';
                                    } ?>
                                </h3>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if (is_admin()) { ?>
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-6">
                                            <?php echo render_select('status_top', $statuses, array('ticketstatusid', 'name'), 'ticket_single_change_status_top', $ticket->status, array(), array(), '', '', false); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane <?php if ($this->session->flashdata('active_tab')) {
                                echo 'active';
                            } ?>" id="addreply">
                                <?php
                                if (is_admin()) {
                                    if ($ticket->assigned == 0) {
                                        set_alert('warning', _l('Atenção! O ticket atual não foi atribuído à nenhum colaborador.'));
                                    }
                                }
                                ?>
                                <hr class="no-mtop"/>
                                <?php $tags = get_tags_in($ticket->ticketid, 'ticket'); ?>
                                <?php if (count($tags) > 0) { ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php echo '<b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b><br /><br /> ' . render_tags($tags); ?>
                                            <hr/>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if (sizeof($ticket->ticket_notes) > 0) { ?>
                                    <div class="row">
                                        <div class="col-md-12 mbot15">
                                            <h4 class="bold"><?php echo _l('ticket_single_private_staff_notes'); ?></h4>
                                            <div class="ticketstaffnotes">
                                                <div class="table-responsive">
                                                    <table>
                                                        <tbody>
                                                        <?php foreach ($ticket->ticket_notes as $note) { ?>
                                                            <tr>
                                                                <td>
																<span class="bold">
																	<?php echo staff_profile_image($note['addedfrom'], array('staff-profile-xs-image')); ?>
                                                                    <a href="<?php echo admin_url('staff/profile/' . $note['addedfrom']); ?>"><?php echo _l('ticket_single_ticket_note_by', get_staff_full_name($note['addedfrom'])); ?>
																</a>
															</span>
                                                                    <?php
                                                                    if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                                                                        <div class="pull-right">
                                                                            <a href="#" class="btn custom-btn-default btn-icon"
                                                                               onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i
                                                                                        class="fa fa-pencil-square-o"></i></a>
                                                                            <a href="<?php echo admin_url('misc/delete_note/' . $note["id"]); ?>"
                                                                               class="mright10 _delete btn btn-danger btn-icon">
                                                                                <i class="fa fa-remove"></i>
                                                                            </a>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <hr/>
                                                                    <div data-note-description="<?php echo $note['id']; ?>">
                                                                        <?php echo $note['description']; ?>
                                                                    </div>
                                                                    <div data-note-edit-textarea="<?php echo $note['id']; ?>"
                                                                         class="hide inline-block full-width">
                                                                        <textarea name="description"
                                                                                  class="form-control"
                                                                                  rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
                                                                        <div class="text-right mtop15">
                                                                            <button type="button"
                                                                                    class="btn custom-btn-default"
                                                                                    onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                                                            <button type="button" class="btn btn-info"
                                                                                    onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                                                                        </div>
                                                                    </div>
                                                                    <small class="bold">
                                                                        <?php echo _l('ticket_single_note_added', _dt($note['dateadded'])); ?>
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div>
                                    <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'single-ticket-form', 'novalidate' => false)); ?>
                                    <?php echo form_hidden('ticketid', $ticket->ticketid); ?>
                                    <span class="ticket-label label label-default inline-block">
									<?php echo _l('department') . ': ' . $ticket->department_name; ?>
								</span>
                                    <?php if (!empty($ticket->priority_name)) { ?>
                                        <span class="ticket-label label label-default inline-block">
									<?php echo _l('ticket_single_priority', ticket_priority_translate($ticket->priorityid)); ?>
								</span>
                                    <?php } ?>
                                    <?php if ($ticket->lastreply !== NULL) { ?>
                                        <span class="ticket-label label label-success inline-block"
                                              data-toggle="tooltip"
                                              title="<?php echo time_ago_specific($ticket->lastreply); ?>">
									<?php echo _l('ticket_single_last_reply', time_ago($ticket->lastreply)); ?>
								</span>
                                    <?php } ?>
                                    <div class="form-group mtop25">
                                        <?php echo render_textarea('message', '', '', array(), array(), '', 'tinymce'); ?>
                                    </div>
                                    <div class="panel_s ticket-reply-tools">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <?php echo render_select('status', $statuses, array('ticketstatusid', 'name'), 'ticket_single_change_status', $ticket->status, array(), array(), '', '', false); ?>
                                                    <?php echo render_input('cc', 'CC'); ?>
                                                    <?php if ($ticket->assigned !== get_staff_user_id()){ ?>
                                                <?php if (is_admin() == true){ ?>
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="assign_to_current_user"
                                                               id="assign_to_current_user">
                                                        <?php }else{ ?>
                                                        <div class="checkbox checkbox-primary hide">
                                                            <input type="checkbox" name="assign_to_current_user"
                                                                   id="assign_to_current_user" checked>
                                                            <?php } ?>

                                                            <label for="assign_to_current_user"><?php echo _l('ticket_single_assign_to_me_on_update'); ?></label>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <?php
                                                    $use_knowledge_base = get_option('use_knowledge_base');
                                                    ?>
                                                    <div class="col-md-7 _buttons mtop20">
                                                        <?php
                                                        $use_knowledge_base = get_option('use_knowledge_base');
                                                        ?>
                                                        <select id="insert_predefined_reply" data-live-search="true"
                                                                class="selectpicker mleft10 pull-right"
                                                                data-title="<?php echo _l('ticket_single_insert_predefined_reply'); ?>">
                                                            <option value=""></option>
                                                            <?php foreach ($predefined_replies as $predefined_reply) { ?>
                                                                <option value="<?php echo $predefined_reply['id']; ?>"><?php echo $predefined_reply['name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <?php if ($use_knowledge_base == 1) { ?>
                                                            <?php $groups = get_all_knowledge_base_articles_grouped(); ?>
                                                            <select id="insert_knowledge_base_link"
                                                                    class="selectpicker pull-right"
                                                                    data-live-search="true"
                                                                    onchange="insert_ticket_knowledgebase_link(this);"
                                                                    data-title="<?php echo _l('ticket_single_insert_knowledge_base_link'); ?>">
                                                                <option value=""></option>
                                                                <?php foreach ($groups as $group) { ?>
                                                                    <?php if (count($group['articles']) > 0) { ?>
                                                                        <optgroup label="<?php echo $group['name']; ?>">
                                                                            <?php foreach ($group['articles'] as $article) { ?>
                                                                                <option value="<?php echo $article['articleid']; ?>">
                                                                                    <?php echo $article['subject']; ?>
                                                                                </option>
                                                                            <?php } ?>
                                                                        </optgroup>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </select>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="row attachments">
                                                    <div class="attachment">
                                                        <div class="col-md-5 mbot15">
                                                            <div class="form-group">
                                                                <label for="attachment" class="control-label">
                                                                    <?php echo _l('ticket_single_attachments'); ?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="file"
                                                                           extension="<?php echo str_replace('.', '', get_option('ticket_attachments_file_extensions')); ?>"
                                                                           filesize="<?php echo file_upload_max_size(); ?>"
                                                                           class="form-control" name="attachments[0]"
                                                                           accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                                                                    <span class="input-group-btn">
																<button class="btn btn-success add_more_attachments p7"
                                                                        type="button"><i
                                                                            class="fa fa-plus"></i></button>
															</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                                <?php if(is_admin() || $ticket->status != 1 || is_financeiro() || is_atendimento_recepcao() || is_partner() || ($ticket->priority > 4 && PAINEL == INORTE)){ ?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-info"
                                                                data-form="#single-ticket-form" autocomplete="off"
                                                                data-loading-text="<?php echo _l('wait_text'); ?>">
                                                            <?php echo _l('ticket_single_add_response'); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php echo form_close(); ?>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="note">
                                    <hr class="no-mtop"/>
                                    <div class="form-group">
                                        <label for="note_description"><?php echo _l('ticket_single_note_heading'); ?></label>
                                        <textarea class="form-control" name="note_description" rows="5"></textarea>
                                    </div>
                                    <a class="btn btn-info pull-right add_note_ticket"><?php echo _l('ticket_single_add_note'); ?></a>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="othertickets">
                                    <hr class="no-mtop"/>
                                    <div class="_filters _hidden_inputs hidden tickets_filters">
                                        <?php echo form_hidden('filters_ticket_id', $ticket->ticketid); ?>
                                        <?php echo form_hidden('filters_email', $ticket->email); ?>
                                        <?php echo form_hidden('filters_userid', $ticket->userid); ?>
                                    </div>
                                    <?php echo AdminTicketsTableStructure(); ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tasks">
                                    <hr class="no-mtop"/>
                                    <?php init_relation_tasks_table(array('data-new-rel-id' => $ticket->ticketid, 'data-new-rel-type' => 'ticket')); ?>
                                </div>
                                <div role="tabpanel"
                                     class="tab-pane <?php if (!$this->session->flashdata('active_tab_settings')) {
                                         echo 'active';
                                     } ?>" id="settings">
                                    <hr class="no-mtop"/>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php if(PAINEL == INORTE){echo render_input('subject', 'ticket_settings_subject', $ticket->subject);} ?>
                                            <?php if(PAINEL == QUANTUM) { ?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <?php echo render_select('service', $services, array('serviceid', 'name'), 'ticket_settings_service', $ticket->service,array(),array(),'','',false); ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 form-group">
                                                        <label for="service" class="control-label">
                                                            <?php echo _l('Serviço Nível 2'); ?>
                                                        </label>
                                                        <select name="servicenv2" id="servicenv2"
                                                                class="form-control selectpicker"
                                                                data-live-search="true">
                                                            <?php foreach ($servicenv2 as $service): ?>
                                                                <?php $result = ''; ?>
                                                                <?php if ($service["serviceid"] == $ticket->service): ?>
                                                                    <?php if ($service["secondServiceid"] == $ticket->servicenv2): ?>
                                                                        <?php $result = $result . '<option value=' . $service["secondServiceid"] . ' selected>' . $service["name"] . '</option>'; ?>
                                                                    <?php else: ?>
                                                                        <?php $result = $result . '<option value=' . $service["secondServiceid"] . '>' . $service["name"] . '</option>'; ?>
                                                                    <?php endif; ?>
                                                                    <?php echo $result; ?>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php }?>
<!--                                            -->
                                        <form> <!-- Gambirinha kakakaka -->
                                            <?php if (PAINEL == INORTE){ ?>
                                                <div class="form-group <?php echo ($client->active) ? '' : 'has-error'?>">
                                                    <label for="clientid"><?php echo "Cliente"; ?></label>
                                                    <?php if(PAINEL == QUANTUM){ ?>
                                                        <span id="backup"></span>
                                                    <?php }?>
                                                    <select name="clientid" required="true" id="clientid" class="ajax-search"
                                                            <?php echo ($client->active) ? '' : 'data-style="btn-danger"'; ?>
                                                            data-width="100%" data-live-search="true" data-msg="Por favor, Selecione um Cliente"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                        <?php
                                                        $rel_data = get_relation_data('customer', $ticket->userid);
                                                        $rel_val = get_relation_values($rel_data, 'customer');
                                                        echo '<option value="' . $rel_val['id'] . '" selected data-subtext="' . $rel_val['subtext'] . '">' . $rel_val['name'] . '</option>';
                                                        ?>
                                                    </select>
                                                    <?php if (!$client->active){ ?>
                                                        <span class="help-block">Cliente Desativado</span>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>

                                            <div class="row">
                                                <div class="col-md-<?php echo (PAINEL == INORTE) ? '10' : '12'; ?>">
                                                    <div class="form-group" id="select_contact">
                                                        <label for="contactid"><?php echo (PAINEL == INORTE) ? 'Contato' : _l('client'); ?></label>
                                                        <?php if(PAINEL == QUANTUM){ ?>
                                                            <span id="backup">
                                                                <?php if($ticket->flag_backup == 1){?>
                                                                    <span class="label inline-block pull-right" style="border:1px solid #84c529; color:#84c529">Backup <span id="nao"></span> configurado</span>
                                                                <?php } else if($ticket->flag_backup == 0){ ?>
                                                                    <span class="label inline-block pull-right" style="border:1px solid #f11327; color:#f11327">Backup <span id="nao">não</span> configurado</span>
                                                                <?php }?>
                                                            </span>
                                                        <?php }?>
                                                        <select name="contactid" id="contactid" class="ajax-search"
                                                                data-width="100%" data-live-search="true"
                                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                            <?php
                                                            $rel_data = get_relation_data('contact', $ticket->contactid);
                                                            $rel_val = get_relation_values($rel_data, 'contact');
                                                            echo '<option value="' . $rel_val['id'] . '" selected data-subtext="' . $rel_val['subtext'] . '">' . $rel_val['name'] . '</option>';
                                                            ?>
                                                        </select>
                                                        <?php echo form_hidden('userid', $ticket->userid); ?>
                                                    </div>
                                                </div>
                                                <?php if (PAINEL == INORTE){ ?>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <!--                                                <label for="buttao">&#8203;</label>-->
                                                            <label for="buttao">Adicionar</label>
                                                            <button name="buttao" id="buttao" onclick="contact($('#clientid').val()); return false;" type="button" class="btn btn-primary btn-block"> <i class="fa fa-plus fa-1x"></i> </button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </form>
                                            <?php
                                            if(PAINEL == QUANTUM){
                                                echo render_input('name_soli', 'contact', $ticket->name_soli, 'text', array('required' => 'true'));
                                            }else{
                                                ?>
<!--                                                <label for="name_soli">--><?php //echo 'Solicitante'; ?><!--</label>-->
<!--                                                <div class="form-group">-->
<!--                                                    <div class="input-group form-group">-->
<!--                                                        --><?php //echo render_select('name_soli', $ticket->solicitantes, array('id', 'name'), 'Solicitante', $ticket->name_soli, array('required' => 'false'), array(), '', 'form-group', true); ?>
<!--                                                        <div class="input-group-btn">-->
<!--                                                            <button name="add_contato" id="add_contato" class="btn btn-success p8 mleft10 mtop10"-->
<!--                                                                    type="button"><i class="fa fa-plus"></i></button>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                </div>-->
                                            <?php } ?>

                                            <?php if(PAINEL == QUANTUM){ ?>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <?php echo render_input('to', 'ticket_settings_to', $ticket->nome_cliente, 'text', array('disabled' => true)); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php //echo print_r($ticket); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php
                                                        if ($ticket->userid != 0) {
                                                            echo render_input('emailPrimary', 'ticket_settings_email', $ticket->email, 'email', array('disabled' => true));
                                                        } else {
                                                            echo render_input('emailPrimary', 'ticket_settings_email', $ticket->ticket_email, 'email', array('disabled' => true));
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php echo render_input('telefone', 'Telefone', $ticket->telCliente, 'text', array('disabled' => true)); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php $value = $ticket->nome_fantasia; ?>
                                                        <?php echo render_input('fantasia', 'Nome Fantasia', $value, 'text', array('disabled' => true)); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php $value = $ticket->telEmpresa; ?>
                                                        <?php echo render_input('telefoneEmpresa', 'Telefone Empresa', $value, 'text', array('disabled' => true)); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <!--                                                    --><?php //$value = $ticket->celEmpresa; ?>
                                                        <?php $value = $client->cellphone; ?>
                                                        <?php echo render_input('celEmpresa', 'Celular Empresa', $value, 'text', array('disabled' => true)); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php $value = get_partner_name($client->partner_id); ?>
                                                        <?php echo render_input('revenda', 'Parceiro/Revenda', $value, 'text', array('disabled' => true)); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php $value = (isset($ticket) ? $ticket->atendimento : ''); ?>
                                                        <?php echo render_input('ultimo_atendimento', 'Último Atendimento', $value, 'text', array('disabled' => true)); ?>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <?php $value = $client->address; ?>
                                                        <?php echo render_input("address", "Endereço",$value,"text",array("disabled" => true)); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php $value = $client->city; ?>
                                                        <?php echo render_input("city", "Cidade",$value,"text",array("disabled" => true)); ?>
                                                    </div>

                                                    <?php if(is_admin() || has_permission('avaliacao_atendimento', '', 'view')){ ?>
                                                    <div class="col-md-12">
                                                        <div class="form-group" style="margin-top: 30px">
                                                                <a onclick="atendimento_tecnico(); return false;" class="btn btn-info">
                                                                    <?php
                                                                        if(PAINEL == INORTE)
                                                                            echo "Avaliação do Técnico";
                                                                        else
                                                                            echo "Avaliação do Atendimento";
                                                                    ?>
                                                                </a>
                                                            <?php $label = isset($ticket->nota_atendimento) ? 'success' : 'danger'; ?>
                                                            <span class = "label label-<?php echo $label; ?>" style="font-size: 100%; background: inherit; border-width: 2px;">
                                                                <b><?php if($label == 'danger') echo "Não "; ?>Avaliado</b>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php }else{
                                            echo render_disable_ticket_fields($ticket, $client);
                                        } ?>


                                        <div class="col-md-6">
                                            <?php
                                            $subject = "";
                                            if(isset($ticket->subject)) {
                                                if (md5($ticket->subject) == md5(get_service1_name($ticket->service) . "/" . get_service2_name($ticket->servicenv2)))
                                                    $subject = "";
                                                else
                                                    $subject = $ticket->subject;
                                            }
                                            ?>
                                            <?php if(PAINEL == QUANTUM){echo render_input('subject', 'Complemento', $subject);} ?>
                                            <?php if(PAINEL == INORTE){?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <?php $value = $client->address; ?>
                                                    <?php echo render_input("address", "Endereço",$value,"text",array("disabled" => true)); ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <?php $value = $client->city; ?>
                                                    <?php echo render_input("city", "Cidade",$value,"text",array("disabled" => true)); ?>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <?php
                                                $priorities['callback_translate'] = 'ticket_priority_translate';
                                                echo render_select('priority', $priorities, array('priorityid', 'name'), 'ticket_settings_priority', $ticket->priority); ?>
                                            </div>
                                            <div class="form-group" id="scheduled_date_div">
                                                <?php echo render_datetime_input('scheduled_date', 'Data de Agendamento', $ticket->scheduled_date, [
                                                    'data-date-min-date' => 'now',
                                                    'required' => 'required'
                                                ]); ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php echo render_custom_fields('tickets', $ticket->ticketid); ?>
                                                </div>
                                            </div>
                                            <?php if(PAINEL == INORTE){?>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <?php echo render_select('service', $services, array('serviceid', 'name'), 'ticket_settings_service', $ticket->service); ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 form-group">
                                                        <label for="service" class="control-label">
                                                            <?php echo _l('Serviço Nível 2'); ?>
                                                        </label>
                                                        <select name="servicenv2" id="servicenv2"
                                                                class="form-control selectpicker"
                                                                data-live-search="true">
                                                            <?php foreach ($servicenv2 as $service): ?>
                                                                <?php $result = ''; ?>
                                                                <?php if ($service["serviceid"] == $ticket->service): ?>
                                                                    <?php if ($service["secondServiceid"] == $ticket->servicenv2): ?>
                                                                        <?php $result = $result . '<option value=' . $service["secondServiceid"] . ' selected>' . $service["name"] . '</option>'; ?>
                                                                    <?php else: ?>
                                                                        <?php $result = $result . '<option value=' . $service["secondServiceid"] . '>' . $service["name"] . '</option>'; ?>
                                                                    <?php endif; ?>
                                                                    <?php echo $result; ?>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php echo render_select('department', $departments, array('departmentid', 'name'), 'ticket_settings_departments', $ticket->department); ?>
                                                    <label for="assigned" class="control-label">
                                                        <?php echo _l('ticket_settings_assign_to'); ?>
                                                    </label>
                                                    <?php if (is_admin()): ?>
                                                    <select name="assigned" id="assigned"
                                                            class="form-control selectpicker"
                                                            data-live-search="true"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                            data-width="100%"/>
                                                        <?php else: ?>
                                                        <input type="hidden" name="assigned" id="assigned" value="<?php echo $ticket->assigned; ?>">
                                                        <select disabled name="assigned" id="assigned"
                                                                class="form-control selectpicker"
                                                                data-live-search="true"
                                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                                data-width="100%">
                                                            <?php endif; ?>
                                                            <option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
                                                            <?php
                                                                foreach ($staff as $member) {
                                                                    if(PAINEL == INORTE) {
                                                                        if ($member['role'] != 5)
                                                                            $minicontrol = true;
                                                                        else
                                                                            $minicontrol = false;
                                                                    }
                                                                    else if(PAINEL == QUANTUM)
                                                                    {
                                                                        $minicontrol = true;
                                                                    }
                                                                    if ($member['departmentid'] == 1 && $minicontrol) { ?>
                                                                        <option value="<?php echo $member['staffid']; ?>" <?php if ($ticket->assigned == $member['staffid']) {
                                                                            echo 'selected';
                                                                        } ?>>
                                                                            <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                                                        </option>
                                                                    <?php }
                                                                }
                                                            ?>
                                                        </select>
                                                        </br></br>

                                                        <?php if (PAINEL == INORTE && is_admin()) { ?>
                                                            <div class="row">
                                                                <div class="col-md-12 text-right">
                                                                    Próximo atendimento:
                                                                    <label>
                                                                        <input type="checkbox"
                                                                               name="is_next_attend" id="is_next_attend" value="1"
                                                                            <?php if($ticket->is_next_attend == 1) echo "checked"; ?>
                                                                               data-toggle="toggle" data-on="Sim" data-off="Não" data-onstyle="success" data-offstyle="danger">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        <?php } ?>

                                                        <?php if(PAINEL == QUANTUM){ ?>
                                                            <div class="form-group">
                                                                <div class="checkbox checkbox-primary">
                                                                    <input type="checkbox" name="plantao" id="plantao" value="1" <?php if($ticket->plantao == 1)echo "checked='true'"; ?>>
                                                                    <label for="plantao">Plantão</label>
                                                                </div>
                                                            </div>
                                                        <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <hr/>
                                            <a href="#" class="btn btn-info save_changes_settings_single_ticket">
                                                <?php echo _l('submit'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            </fieldset>
                    </div>
                </div>
            </div>
            <div class="panel_s mtop20">
                <fieldset <?php if(($ticket->partner_id != get_staff_partner_id()) && is_partner()) echo 'disabled="disabled"'; ?>>
                <div class="panel-body <?php if ($ticket->admin == NULL) {
                    echo 'client-reply';
                } ?>">
                    <div class="row">
                        <div class="col-md-3 border-right">
                            <p>
                                <?php if ($ticket->admin == NULL || $ticket->admin == 0){ ?>
                                <?php if ($ticket->userid != 0){ ?>
                                    <a href="<?php echo admin_url('clients/client/' . $ticket->userid . '?contactid=' . $ticket->contactid); ?>"
                                    ><?php echo $ticket->submitter; ?>
                                    </a>
                                <?php } else {
                                echo $ticket->submitter;
                                ?>
                                <br/>
                                <a href="mailto:<?php echo $ticket->ticket_email; ?>"><?php echo $ticket->ticket_email; ?></a>
                            <hr/>
                            <?php
                            if (total_rows('tblticketsspamcontrol', array('type' => 'sender', 'value' => $ticket->ticket_email)) == 0) { ?>
                                <button type="button" data-sender="<?php echo $ticket->ticket_email; ?>"
                                        class="btn btn-danger block-sender btn-xs"><?php echo _l('block_sender'); ?></button>
                                <?php
                            } else {
                                echo '<span class="label label-danger">' . _l('sender_blocked') . '</span>';
                            }
                            }
                            } else { ?>
                                <?php if(($ticket->partner_id != get_staff_partner_id()) && is_partner()){ ?>
                                    <p><?php echo $ticket->opened_by; ?></p>
                                <?php } else{ ?>
                                    <a href="<?php echo admin_url('profile/' . $ticket->admin); ?>"><?php echo $ticket->opened_by; ?></a>
                                <?php } ?>
                            <?php } ?>
                            </p>
                            <p class="text-muted">
                                <?php if ($ticket->admin !== NULL || $ticket->admin != 0) {
                                    echo _l('ticket_staff_string');
                                } else {
                                    if ($ticket->userid != 0) {
                                        echo _l('ticket_client_string');
                                    }
                                }
                                ?>
                            </p>
                            <?php if (has_permission('tasks', '', 'create')) { ?>
                                <a href="#" class="btn custom-btn-default btn-xs"
                                   onclick="convert_ticket_to_task(<?php echo $ticket->ticketid; ?>,'ticket', 'message'); return false;"><?php echo _l('convert_to_task'); ?></a>
                            <?php } ?>
                        </div>
                        <div class="col-md-9">
                            <?php if(!(($ticket->partner_id != get_staff_partner_id()) && is_partner())){ ?>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <a href=""></a>
                                    <a href="#"
                                       onclick="edit_ticket_message(<?php echo $ticket->ticketid; ?>,'ticket'); return false;"><i
                                                class="fa fa-pencil-square-o"></i></a>

                                </div>
                            </div>
                            <?php } ?>
                            <div data-ticket-id="<?php echo $ticket->ticketid; ?>" class="tc-content">
                                <?php echo check_for_links($ticket->message); ?>
                            </div>
                            <br/>
                            <p>-----------------------------</p>
                            <?php if (filter_var($ticket->ip, FILTER_VALIDATE_IP)) { ?>
                                <p>IP: <?php echo $ticket->ip; ?></p>
                            <?php } ?>

                            <?php if (count($ticket->attachments) > 0) {
                                echo '<hr />';
                                foreach ($ticket->attachments as $attachment) {

                                    $path = get_upload_path_by_type('ticket') . $ticket->ticketid . '/' . $attachment['file_name'];
                                    $is_image = is_image($path);

                                    if ($is_image) {
                                        echo '<div class="preview_image">';
                                    }
                                    ?>
                                    <a href="<?php echo site_url('download/file/ticket/' . $attachment['id']); ?>"
                                       class="display-block mbot5">
                                        <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <?php echo $attachment['file_name']; ?>
                                        <?php if ($is_image) { ?>
                                            <img class="mtop5"
                                                 src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path($path) . '&type=' . $attachment['filetype']); ?>">
                                        <?php } ?>

                                    </a>
                                    <?php if ($is_image) {
                                        echo '</div>';
                                    }
                                    echo '<hr />';
                                    ?>
                                <?php }
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <?php echo _l('ticket_posted', _dt($ticket->date)); ?>
                </div>
              </fieldset>
            </div>
            <?php //print_r($ticket_replies); ?>
            <?php foreach ($ticket_replies as $reply) { ?>
                <div class="panel_s">
                    <fieldset <?php if(($ticket->partner_id != get_staff_partner_id()) && is_partner()) echo 'disabled="disabled"'; ?>>
                    <div class="panel-body <?php if ($reply['admin'] == NULL) {
                        echo 'client-reply';
                    } ?>">
                        <div class="row">
                            <div class="col-md-3 border-right">
                                <p>
                                    <?php if ($reply['admin'] == NULL || $reply['admin'] == 0) { ?>
                                        <?php if ($reply['userid'] != 0) { ?>
                                            <a href="<?php echo admin_url('clients/client/' . $reply['userid'] . '?contactid=' . $reply['contactid']); ?>"><?php echo $reply['submitter']; ?></a>
                                        <?php } else { ?>
                                            <?php echo $reply['submitter']; ?>
                                            <br/>
                                            <a href="mailto:<?php echo $reply['reply_email']; ?>"><?php echo $reply['reply_email']; ?></a>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php if(($ticket->partner_id != get_staff_partner_id()) && is_partner()){ ?>
                                            <p><?php echo $reply['submitter']; ?></p>
                                        <?php } else{ ?>
                                            <a href="<?php echo admin_url('profile/' . $reply['admin']); ?>"><?php echo $reply['submitter']; ?></a>
                                        <?php } ?>
                                    <?php } ?>
                                </p>
                                <p class="text-muted">
                                    <?php if ($reply['admin'] !== NULL || $reply['admin'] != 0) {
                                        echo _l('ticket_staff_string');
                                        echo '<br>Status: ';
//                                        echo _l($reply['reply_status'] != null ? 'ticket_status_db_' . $reply['reply_status'] : 'Não possui status salvo');
                                        echo ticket_status_translate($reply['reply_status']);
                                    } else {
                                        if ($reply['userid'] != 0) {
                                            echo _l('ticket_client_string');
                                        }
                                    }
                                    ?>
                                </p>
                                <hr/>
                                <a href="<?php echo admin_url('tickets/delete_ticket_reply/' . $ticket->ticketid . '/' . $reply['id']); ?>"
                                   class="btn btn-danger pull-left _delete mright5 btn-xs"><?php echo _l('delete_ticket_reply'); ?></a>
                                <div class="clearfix"></div>
                                <?php if (has_permission('tasks', '', 'create')) { ?>
                                    <a href="#" class="pull-left btn custom-btn-default mtop5 btn-xs"
                                       onclick="convert_ticket_to_task(<?php echo $ticket->ticketid; ?>,'reply', <?php echo $reply['id']; ?>); return false;"><?php echo _l('convert_to_task'); ?>
                                    </a>
                                    <div class="clearfix"></div>
                                <?php } ?>
                            </div>
                            <div class="col-md-9">
                                <?php if(!(($ticket->partner_id != get_staff_partner_id()) && is_partner())){ ?>
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <a href="#"
                                           onclick="edit_ticket_message(<?php echo $reply['id']; ?>,'reply'); return false;"><i
                                                    class="fa fa-pencil-square-o"></i></a>

                                    </div>
                                </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                                <div data-reply-id="<?php echo $reply['id']; ?>" class="tc-content">
                                    <?php echo check_for_links($reply['message']); ?>
                                </div>
                                <br/>
                                <p>-----------------------------</p>
                                <?php if (filter_var($reply['ip'], FILTER_VALIDATE_IP)) { ?>
                                    <p>IP: <?php echo $reply['ip']; ?></p>
                                <?php } ?>
                                <?php if (count($reply['attachments']) > 0) {
                                    echo '<hr />';
                                    foreach ($reply['attachments'] as $attachment) {
                                        $path = get_upload_path_by_type('ticket') . $ticket->ticketid . '/' . $attachment['file_name'];
                                        $is_image = is_image($path);
//                                        echo site_url(protected_file_url_by_path($path));

                                        if ($is_image) {
                                            echo '<div class="preview_image"><a href="#" onclick=\'$("#imagem-tela-cheia")[0].src = "'.site_url(protected_file_url_by_path($path)).'"; $("#imagem").modal("show"); return false;\' ';
                                        }
                                        else{?>
                                            <a href="<?php echo site_url('download/file/ticket/' . $attachment['id']); ?>"
                                            class="display-block mbot5">
                                        <?php }?>
                                            <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <?php echo $attachment['file_name']; ?>
                                            <?php if ($is_image) { ?>
                                                <img class="mtop5"
                                                     src="<?php echo site_url(protected_file_url_by_path($path)); ?>">
                                            <?php } ?>

                                        </a>
                                        <?php if ($is_image) {
                                            echo '</div>';
                                        }
                                        echo '<hr />';
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <span><?php echo _l('ticket_posted', _dt($reply['date'])); ?></span>
                    </div>
                        </fieldset>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if (count($ticket_replies) > 1) { ?>
        <a href="#top" id="toplink">↑</a>
        <a href="#bot" id="botlink">↓</a>
    <?php } ?>
</div>

<div id="contact_data"></div>


<!-- Modal -->
<div class="modal fade" id="ticket-message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <?php echo form_open(admin_url('tickets/edit_message')); ?>
        <div class="modal-content">
            <div id="edit-ticket-message-additional"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?php echo _l('ticket_message_edit'); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo render_textarea('data', '', '', array(), array(), '', 'tinymce-ticket-edit'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn custom-btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<div class="modal fade" id="imagem" tabindex="-1" role="dialog" style="display: none;">
    <button type="button" class="close label-danger" data-dismiss="modal" aria-hidden="true" style="opacity: 1;">×</button>
    <img class="col-md-12" id="imagem-tela-cheia" src=""><!-- /.modal-dialog -->
</div>
<!-- Modal -->
<div class="modal fade" id="danger" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-danger">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h1><i class="glyphicon glyphicon-alert"></i> Alerta</h1>
            </div>
            <div class="modal-body">
                <?php $dias = $this->tickets_model->get_validation(); ?>
                <h4>Este cliente foi contatado há mais de <?php echo(($dias != null) ? $dias->validation : '') ?> dias,
                    assim, ele deverá ser colocado em prioridade alta.</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn custom-btn-default pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal -->

<!-- Modal -->
<div class="modal fade" id="atendimento" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('tickets/nota_atendimento'), array('id' => 'atendimento-form'), array('id' => $ticket->ticketid)); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">
                    Avaliação do Atendimento
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_evaluate"></div>
                            <h5><?php echo (PAINEL == QUANTUM)?"Como você avalia o nosso atendimento de modo geral?": "De 1 a ".count($nota).", quão satisfeito o cliente está com o nosso atendimento?"; ?> <span class="bold text-danger">*</span></h5>
                            <?php echo render_select('nota_atendimento', $nota, array('id', 'descricao'), '', ($ticket->nota_atendimento ?? ''), array("required"=>"true"), array(), '', '', true); ?>
                            <?php echo render_textarea('nota_atendimento_desc', 'Observação/Reclamação ou sugestão?', $observacoes[0]->nota_atend_desc ?? ''); ?>
                            <h5><?php echo (PAINEL == QUANTUM)?"Como você avalia o atendimento do técnico?":"De 1 a ".count($nota).", quão satisfeito o cliente está com o analista que prestou o atendimento?"; ?> <span class="bold text-danger">*</span></h5>
                            <?php echo render_select('nota_tecnico', $nota, array('id', 'descricao'), '', ($ticket->nota_tecnico ?? ''), array("required"=>"true"), array(), '', '', true); ?>
                            <?php echo render_textarea('nota_tecnico_desc', 'Observação/Reclamação?', $observacoes[0]->nota_tecnico_desc ?? ''); ?>
                        <h5><?php echo (PAINEL == QUANTUM)?"Como você avalia o atendimento do técnico?":"De 1 a ".count($nota).", quão satisfeito o cliente está com o sistema utilizado?"; ?> <span class="bold text-danger">*</span></h5>
                            <?php echo render_select('nota_sistema', $nota, array('id', 'descricao'), '', ($ticket->nota_sistema ?? ''), array("required"=>"true"), array(), '', '', true); ?>
                            <?php echo render_textarea('nota_sistema_desc', 'Observação/Reclamação ou sugestão?', $observacoes[0]->nota_sistema_desc ?? ''); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn custom-btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal Evento-->

<!-- Modal -->
<div class="modal fade" id="observation" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">
                    Observações
                </h3>
            </div>
            <div class="modal-body">

                <hr>
            </div>
            <div id="observation_server" class="col-md-12" style="padding-bottom:10px;">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn custom-btn-default" data-dismiss="modal"><?php echo _l('Ok'); ?></button>
                <!--                <button type="submit" class="btn btn-info">--><?php //echo _l('submit'); ?><!--</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal Evento-->
<?php if(PAINEL == QUANTUM){ ?>
<?php if(is_admin()){ ?>
    <!-- Modal -->
    <div class="modal fade" id="corrigedatam" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">
                        Corrigir datas
                    </h3>
                </div>
                <div class="modal-body">
                    <div class="panel-body">
                        <div class="row">
                            <?php echo form_open(admin_url('tickets/ticket/'.$ticket->ticketid.'/correcao')); ?>
                            <h4>Ticket</h4>
                            <?php echo render_datetime_input('date','Data de Criação',date_format(date_create($ticket->date),'d/m/Y h:i'));?>
                            <?php echo render_datetime_input('lastreply','Última Resposta',date_format(date_create($ticket->lastreply),'d/m/Y h:i'));?>
                            <hr>
                            <h4>Respostas</h4>
                            <table class="col-md-12" style="padding: 0px;">
                                <?php foreach ($ticket_replies as $reply) { ?>
                                <tr>
                                    <td class="col-md-6">
                                        <?php echo $reply['message']; ?>
                                    </td>
                                    <td class="col-md-6">
                                        <?php echo render_datetime_input('reply_'.$reply['id'],'',date_format(date_create($reply['date']),'d/m/Y h:i'));?>
                                    </td>
                                </tr>
                                <?php }?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn custom-btn-default" data-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-success"><?php echo _l('submit'); ?></button>
                    <?php echo form_close(); ?>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <?php } ?>
    <!-- MODAL INFORMAÇÕES SERVIDOR -->
    <div class="modal fade" id="InfoServer" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">
                        Informações do Servidor
                    </h3>
                </div>
                <div class="modal-body">
                    <div class="panel-body">
                        <div class="row">
                            <?php echo form_open('', array("id" => "#info_server_form")); ?>
                            <?php echo render_input('server_adress', 'Endereço do Servidor', $server_info->server_address ?? '', 'text', array()) ?>
                            <?php echo render_input('server_port', 'Porta', $server_info->port ?? '', 'number', array()) ?>
                            <?php echo render_input('server_user', 'Usuário', $server_info->username ?? '', 'text', array()) ?>
                            <?php echo render_input('server_password', 'Senha', $server_info->password ?? '', 'text', array()) ?>
                            <table class="col-md-12" style="padding: 0px;">
                                <?php //foreach ($ticket_replies as $reply) { ?>
                                <tr>
                                    <td class="col-md-6">
                                        <!-- however -->
                                    </td>
                                    <td class="col-md-6">
                                        <!-- however -->
                                    </td>
                                </tr>
                                <?php //} ?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn custom-btn-default" data-dismiss="modal">Voltar</button>
                    <button type="button" class="btn btn-success"
                            onclick="SubmitInfo(); return false;"><?php echo _l('submit'); ?></button>
                    <?php echo form_close(); ?>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<?php } ?>
<?php
    $data['sub'] = true;
    $this->load->view('admin/utilities/calendar_template', $data);
    $data['corpoemail'] = "#".$ticket->ticketid." - ".$ticket->subject."</br>\n";
    $data['corpoemail'] .= "Cliente: ".$client->company."</br>\n";
    $data['corpoemail'] .= "Telefone: ".$client->phonenumber."</br>\n";
    $data['corpoemail'] .= "Data do ticket: ".$ticket->date."</br>\n";
    $this->load->view('admin/includes/modals/send_mail', $data);
?>
<!-- /.modal -->
<script>
    var _ticket_message;
</script>
<?php init_tail(); ?>
<?php echo app_script('/assets/js', 'tickets.js'); ?>
<?php do_action('ticket_admin_single_page_loaded', $ticket); ?>
<script>
    var alerta = '<?php (isset($ticket) ? $ticket->atendimento : '');?>';
    var validation = '<?php (isset($ticket) ? $ticket->validation : '');?>';
    var observation = '<?php echo $client->observation; ?>';
    var observation_server = "<?php if (!isset($server_info->server_address)) echo '</br>As informações do servidor do cliente estão vazias.';?>";
    var status = '<?php echo(isset($ticket) ? $ticket->status : ''); ?>';
    var ticketid = '<?php echo(isset($ticket) ? $ticket->ticketid : ''); ?>';

    /*if(parseInt(alerta) >= validation){ //Removido alerta do single ticket
        $("#danger").modal();
        $("#priority").val('3').change();
    }*/
    if(PAINEL != INORTE) {
        if (observation != '' && status == 1) {
            var modal = $("#observation").modal();
            modal.find('.modal-body').html(observation);
        }
        if (observation_server != '') {
            var modal = $("#observation").modal();
            modal.find('.modal-body').html(observation);
            modal.find('#observation_server').html(observation_server);
        }
    }

    $(function mudanca() {
        $("#service").on('change', function () {
            var id = $(this).val();
            $.ajax({
                url: "../servicenv2/" + id,
                type: 'GET',
                dataType: 'html',
                data: {service: id},
                success: function (data) {
                    if (data) {
                        var opcoes = '<option value=""></option>' +
                            '<option value="2">opção 1</option>';
                        $("#servicenv2").empty();
                        $("#servicenv2").html(data);
                        $("#servicenv2").selectpicker("refresh");

                    }
                }
            });
        });
    });

    $(function () {

        var min;
        var req;
        var admin = '<?php echo is_admin(); ?>';
        if (admin) {
            min = 0;
            req = false;
        } else {
            min = 15;
            req = true;
        }
        _validate_form($('#single-ticket-form'), { //faz a verificação se é admin ou não e coloca a restrição
            message: {	                          // de caracteres
                required: req,
                minlength: min,
            },
        });
        /**Eu comentei para aceitar inserir em branco, no caso de ter errado o ticket ao salvar
         * Obs: Só aceita em branco se os dois estiverem vazios. Somente quantum
         */
        // _validate_form($('#atendimento-form'), {
        //     nota_atendimento: nota_atendimento_req,//'required',
        //     nota_tecnico: nota_tecnico_req//'required'
        // });
        init_ajax_search('contact', '#contactid.ajax-search', {tickets_contacts: true});
        if(PAINEL == INORTE){
            init_ajax_search('customer', '#clientid.ajax-search'); //colocar uma variavel 'clients_contacts' para filtrar os contatos de acordo com a empresa selecionada
        }
        init_ajax_search('project', 'select[name="project_id"]', {
            customer_id: function () {
                return $('input[name="userid"]').val();
            }
        });
        //desabilita_priority_select(admin);
        $('form').bind('submit', function () { // habilita o campo antes do submit
            $(this).find('#priority').prop('disabled', false);
        });

        $('body').on('shown.bs.modal', '#_task_modal', function () {
            if (typeof(_ticket_message) != 'undefined') {
                tinymce.activeEditor.execCommand('mceInsertContent', false, _ticket_message);
                $('body #_task_modal input[name="name"]').val($('#ticket_subject').text());
            }
        });

        $("a[href='#top']").on("click", function (e) {
            e.preventDefault();
            $("html,body").animate({scrollTop: 0}, 1000);
            e.preventDefault();
        });

        // Smooth scroll to bottom.
        $("a[href='#bot']").on("click", function (e) {
            e.preventDefault();
            $("html,body").animate({scrollTop: $(document).height()}, 1000);
            e.preventDefault();
        });

        if(parseInt(status) === SUP_ESPERA || parseInt(status) === SUP_ATENDIMENTO){
            // if(getLocalStorage('messageAfterLoad') === false){
                var data = {};
                data.service1 = $("#service").val();
                data.service2 = $("#servicenv2").val();
                data.userid = $("#clientid").val();
                data.ticketid = $('input[name=ticketid]').val();

                // alert(data.ticketid);

                if((data.service1 !== '' && data.service2 !== '' && data.userid !== '') && (data.service1 !== undefined &&
                    data.service2 !== undefined && data.userid !== undefined) && (data.service1 !== null &&
                    data.service2 !== null && data.userid !== null) && (data.service1 !== '153' && data.service2 !== '113')) {

                    $.get(admin_url + "tickets/check_services/", data).done(function (response) {
                        response = JSON.parse(response);
                        if (response.message === 'true') {
                            ticketCheckDialog(data);
                        }
                    });
                }
            // }
        }

        setLocalStorage('messageAfterLoad', 'true', 5); // ultimo parametro e o tempo em minutos
    });


    var Ticket_message_editor;
    var edit_ticket_message_additional = $('#edit-ticket-message-additional');
    var headers_tasks = $('.table-rel-tasks').find('th');
    var not_sortable_tasks = (headers_tasks.length - 1);
    init_rel_tasks_table(<?php echo $ticket->ticketid; ?>, 'ticket');

    function edit_ticket_message(id, type) {
        edit_ticket_message_additional.empty();
        if (type == 'ticket') {
            _ticket_message = $('[data-ticket-id="' + id + '"]').html();
        } else {
            _ticket_message = $('[data-reply-id="' + id + '"]').html();
        }

        // console.log(_ticket_message);
        init_ticket_edit_editor();
        tinyMCE.activeEditor.setContent(_ticket_message);
        $('#ticket-message').modal('show');
        edit_ticket_message_additional.append(hidden_input('type', type));
        edit_ticket_message_additional.append(hidden_input('id', id));
        edit_ticket_message_additional.append(hidden_input('main_ticket', $('input[name="ticketid"]').val()));
    }

    function create_event()
    {
        // admin/utilities/calendar
        $.ajax({
            type: 'POST',
            url: $("#calendar-event-form").attr("action"),
            data: $("#calendar-event-form").serialize(),
            //or your custom data either as object {foo: "bar", ...} or foo=bar&...
            success: function(response) {
                response = JSON.parse(response);
                console.log(response);
                if(response.success == true)
                {
                    var tipo = "success";
                    $('#closebntcalendar')[0].click();
                    $('#calendar-event-form')[0].reset();
                }
                else
                    var tipo = "danger";

                alert_float(tipo, response.message);
                // $('#atendimento').modal('hide');
            },
        });
        // calendar-event-form
    }

    function init_ticket_edit_editor() {
        if (typeof(Ticket_message_editor) !== 'undefined') {
            return true;
        }
        Ticket_message_editor = init_editor('.tinymce-ticket-edit');
    }

    //função modificada para pegar as respostas e manter a tarefa relacionada com o ticket
    <?php if(has_permission('tasks', '', 'create')){ ?>
    function convert_ticket_to_task(id, type, reply) {
        /*if(type == 'ticket'){
            _ticket_message = $('[data-ticket-id="'+id+'"]').html();
            alert("entrou nesse");
        } else {
            _ticket_message = $('[data-reply-id="'+id+'"]').html();
        }*/
        _ticket_message = $('[data-reply-id="' + id + '"]').html();
        new_task_from_relation(undefined, 'ticket', id, reply);
    }
    <?php } ?>
    //////////////////////////////////////////////////////////////////////////////////////

    function atendimento_tecnico(){
        $('#atendimento').modal('show');
    }

    $("#nota_atendimento").change(function () {
        verifica_required();
    });

    $("#nota_tecnico").change(function () {
        verifica_required();
    });

    function verifica_required() {
        if(PAINEL == QUANTUM)
        {
            if ($("#nota_atendimento").val() == "" && $("#nota_tecnico").val() == "")
            {
                $("#nota_atendimento").prop('required', false);
                $("#nota_tecnico").prop('required', false);
            }
            else
            {
                $("#nota_atendimento").prop('required', true);
                $("#nota_tecnico").prop('required', true);
            }
        }
    }

    $("#assigned").on("change", function () {
        $.get(admin_url+"tickets/verifyNewTicket/"+$("#assigned").val()).done(function(data) {
            data = JSON.parse(data)
            if(!data.status)
            {
                $("#assigned").val("0");
                $("#assigned").selectpicker("refresh");
                alert_float("danger","Limite de Tickets/Tempo atingido.</br>Fechar tickets aberto antes.");
            }
        });
    });

    function SubmitInfo()
    {
        var data = {};
        data.server_address = $("#server_adress").val();
        data.port = $("#server_port").val();
        data.username = $("#server_user").val();
        data.password = $("#server_password").val();
        data.description = "server_info";
        data.userid = "<?php echo $ticket->userid; ?>";
        $.post(admin_url+"tickets/SetDataServer", data, function(data){
            if(data.type == 'success')
                $('#InfoServer').modal('hide');
            alert_float(data.type, data.message);
        }, "json");
    }

    $("#priority").on('change', function () {
        let value = $(this).val();
        if (value === '6'){
            $('#scheduled_date_div').show();
            // $('#scheduled_date').val('');
        } else {
            $('#scheduled_date_div').hide();
        }
    }).trigger('change');

</script>

</body>
</html>
