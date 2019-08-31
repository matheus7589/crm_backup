<?php init_head();
?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'new_ticket_form')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php if(PAINEL == INORTE){
                                        echo render_input('subject', 'ticket_settings_subject', '', 'text', array('required' => 'true'));
                                    }
                                    else if(PAINEL == QUANTUM){?>

                                    <div class="form-group">
                                        <label for="service" class="control-label">
                                            Serviço Nível 1
                                        </label>

                                        <select name="service" id="service" class="form-control selectpicker"
                                                data-live-search="true" required>

                                            <?php foreach ($services as $service){ ?>
                                                <option value="<?php echo $service["serviceid"]; ?>">
                                                    <?php echo $service["name"];
                                                    $aux = $service["serviceid"];
                                                    ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
<!--                                --><?php //}?>
                                    <div class="form-group">
                                        <label for="service" class="control-label">
                                            Serviço Nível 2
                                        </label>
                                        <select name="servicenv2" id="servicenv2" class="form-control selectpicker"
                                                data-live-search="true" required>
                                            <option value="113">ND</option>
                                        </select>
                                    </div>
                                <?php }?>
                                <?php $selected = (isset($userid) ? $userid : ''); ?>

                            <?php if (PAINEL == INORTE){ ?>
                                <div class="form-group">
                                    <label for="clientid"><?php echo "Cliente"; ?></label>
                                    <?php if(PAINEL == QUANTUM){ ?>
                                        <span id="backup"></span>
                                    <?php }?>
                                    <select name="clientid" required="true" id="clientid" class="ajax-search"
                                            data-width="100%" data-live-search="true" data-msg="Por favor, selecione um cliente"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    </select>
                                </div>
                            <?php } ?>


                                    <div class="row">
                                        <div class="col-md-<?php echo (PAINEL == INORTE) ? '10' : '12'; ?>">
                                            <div class="form-group" id="select_contact">
                                                <label for="contactid"><?php echo (PAINEL == INORTE) ? "Contato" : _l('client'); ?></label>
                                                <?php if(PAINEL == QUANTUM){ ?>
                                                    <span id="backup"></span>
                                                <?php }?>
                                                <select name="contactid" required="true" id="contactid" class="ajax-search"
                                                        data-width="100%" data-live-search="true"
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                </select>
                                                <?php echo form_hidden('userid'); ?>
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


                                <?php
                                if(PAINEL == QUANTUM) {
                                    echo render_input('name_soli', 'contact', '', 'text', array('required' => 'true'));
                                }else{
                                    ?>
<!--                                    <label for="name_soli">--><?php //echo 'Solicitante'; ?><!--</label>-->
<!--                                    <div class="form-group">-->
<!--                                        <div class="input-group form-group">-->
<!--                                            <select name="name_soli" id="name_soli" class="form-control selectpicker"-->
<!--                                                    data-live-search="true" data-none-selected-text="--><?php //echo _l('dropdown_non_selected_tex'); ?><!--">-->
<!--                                            </select>-->
<!--                                            --><?php //echo render_select('department', $departments, array('departmentid', 'name'), 'Solicitante', '', array('required' => 'false'), array(), '', '', false); ?>
<!--                                            <div class="input-group-btn">-->
<!--                                                <button name="add_contato" id="add_contato" class="btn btn-success p8 mleft10"-->
<!--                                                        type="button"><i class="fa fa-plus"></i></button>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                <?php } ?>
<!--                                TODO OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO-->
<!--                                COLOCAR O TELEFONE DA EMPRESA PRIMEIRO, OS CONTATOS DEPOIS-->


                                <?php if(PAINEL == QUANTUM){ ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->firstname . ' ' . $client->lastname: ''); ?>
                                            <?php echo render_input('to', 'ticket_settings_to', '', 'text', array('disabled' => true)); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->email: ''); ?>
                                            <?php echo render_input('emailPrimary', 'ticket_settings_email', '', 'email', array('disabled' => true)); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->phonenumber: '63981129813'); ?>
                                            <?php echo render_input('telefone', 'Telefone', '', 'text', array('disabled' => true)); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->company: ''); ?>
                                            <?php echo render_input('fantasia', 'Nome Fantasia', '', 'text', array('disabled' => true)); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->company: ''); ?>
                                            <?php echo render_input('telefoneEmpresa', 'Telefone Empresa', '', 'text', array('disabled' => true)); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->company: ''); ?>
                                            <?php echo render_input('celEmpresa', 'Celular Empresa', '', 'text', array('disabled' => true)); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->company: ''); ?>
                                            <?php echo render_input('revenda', 'Parceiro/Revenda', '', 'text', array('disabled' => true)); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->company: ''); ?>
                                            <?php echo render_input('ultimo_atendimento', 'Último Atendimento', '', 'text', array('disabled' => true)); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->company: ''); ?>
                                            <?php echo render_input("address", "Endereço","","text",array("disabled" => true)); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->company: ''); ?>
                                            <?php echo render_input("city", "Cidade","","text",array("disabled" => true)); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php }else{
                                echo render_disable_ticket_fields();
                            } ?>
<!--                            </div>-->
                            <div class="col-md-6">
                                <?php if(PAINEL == INORTE){ ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->company: ''); ?>
                                            <?php echo render_input("address", "Endereço","","text",array("disabled" => true)); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php //$value = (isset($userid) ? $client->company: ''); ?>
                                            <?php echo render_input("city", "Cidade","","text",array("disabled" => true)); ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                    <?php if(PAINEL == QUANTUM){echo render_input('subject', 'Complemento');}?>
<!--                                    <div class="col-md-12">-->
                                        <?php $priorities['callback_translate'] = 'ticket_priority_translate';
                                        echo render_select('priority', $priorities, array('priorityid', 'name'), 'ticket_settings_priority', do_action('new_ticket_priority_selected', 2), array('required' => 'true')); ?>
                                        <div class="form-group" id="scheduled_date_div" hidden>
                                            <?php echo render_datetime_input('scheduled_date', 'Data de Agendamento', ''); ?>
                                        </div>
                                        <!-- render_custom_fields renderiza os campos personalizados criados -->
                                        <?php echo render_custom_fields('tickets'); ?>
                                        <!-- /////////////////////////////////////////////////////////////  -->
                                        <?php if(PAINEL == INORTE){ ?>
                                            <div class="form-group" style="padding-top: 2px;">
                                                <label for="service" >
                                                    Serviço Nível 1
                                                </label>

                                                <select name="service" id="service" class="form-control selectpicker"
                                                        data-live-search="true" required>

                                                    <?php foreach ($services as $service): ?>
                                                        <option value="<?php echo $service["serviceid"]; ?>">
                                                            <?php echo $service["name"];
                                                            $aux = $service["serviceid"];
                                                            ?>
                                                        </option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div class="form-group" style="padding-top: 2px;">
                                                <label for="service" >
                                                    Serviço Nível 2
                                                </label>
                                                <select name="servicenv2" id="servicenv2" class="form-control selectpicker"
                                                        data-live-search="true" required>
                                                    <option value="113">ND</option>
                                                </select>
                                            </div>
                                        <?php }?>
<!--                                    </div>-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php echo render_select('department', $departments, array('departmentid', 'name'), 'ticket_settings_departments', (count($departments) == 1) ? $departments[0]['departmentid'] : '', array('required' => 'true'), array(), '', '', false); ?>
                                    </div>
                                    <div class="col-md-6 hide">
                                        <?php echo render_input('cc', 'CC'); ?>
                                    </div>
                                </div>
                                <div class="form-group" style="padding-top: 2px;">
                                    <label for="assigned" class="control-label">
                                        <?php echo _l('ticket_settings_assign_to'); ?>
                                    </label>
                                    <?php if(is_admin()) { ?>
                                        <select name="assigned" id="assigned" class="form-control selectpicker"
                                                data-live-search="true"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                data-width="100%">
<!--                                            --><?php //if(PAINEL == 1){ ?>
                                                <option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
<!--                                            --><?php //} ?>
                                            <?php foreach ($staff as $member) { ?>
                                                    <option value="<?php echo $member['staffid']; ?>" <?php if ($member['staffid'] == get_staff_user_id()) { echo "selected";} ?>>
                                                        <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                                    </option>
                                            <?php } ?>
                                        </select>
                                    <?php }else{ ?>
                                        <select name="assigned" id="assigned" class="form-control selectpicker"
                                                data-live-search="true"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                data-width="100%">
                                            <?php foreach ($staff as $member) { ?>
                                                <?php if ($member['staffid'] == get_staff_user_id()) { ?>
                                                    <option value="<?php echo $member['staffid']; ?>" selected>
                                                        <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                                    </option>
                                                <?php }else{ ?>
                                                    <option disabled="" value="<?php echo $member['staffid']; ?>">
                                                        <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                                    </option>
                                                <?php } ?>

                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
<!--                                <div class="form-group">-->
<!--                                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> --><?php //echo _l('tags'); ?>
<!--                                    </label>-->
<!--                                    <input type="text" class="tagsinput" id="tags" name="tags" data-role="tagsinput">-->
<!--                                </div>-->
                                <?php if(PAINEL == QUANTUM){ ?>
                                    <div class="form-group">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="plantao" id="plantao">
                                            <label for="plantao">Plantão</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="observation">
                                            <div class="observation-title">
                                                Observação do Cliente
                                            </div>
                                            <div class="observation-content">
                                            </div>
                                        </div>

                                    </div>
                                <?php } ?>

                                <!--<div class=" hide projects-wrapper" style="margin-top: 20px">
									<label for="project_id"><?php //echo _l('project'); ?></label>
									<div id="project_ajax_search_wrapper">
										<select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php //echo _l('dropdown_non_selected_tex'); ?>">
										</select>
									</div>
								</div>-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-heading">
                                <?php echo _l('ticket_add_body'); ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12 mbot20">
                                        <select id="insert_predefined_reply" data-live-search="true"
                                                class="selectpicker mleft10 pull-right"
                                                data-title="<?php echo _l('ticket_single_insert_predefined_reply'); ?>">
                                            <option value=""></option>
                                            <?php foreach ($predefined_replies as $predefined_reply) { ?>
                                                <option value="<?php echo $predefined_reply['id']; ?>"><?php echo $predefined_reply['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php if (get_option('use_knowledge_base') == 1) { ?>
                                            <?php $groups = get_all_knowledge_base_articles_grouped(); ?>
                                            <select id="insert_knowledge_base_link" class="selectpicker pull-right"
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
                                <div class="clearfix"></div>
                                <?php echo render_textarea('message', '', '', array(), array(), '', 'tinymce'); ?>
                            </div>
                            <div class="panel-footer attachments_area">
                                <div class="row attachments">
                                    <div class="attachment">
                                        <div class="col-md-4 col-md-offset-4 mbot15">
                                            <div class="form-group">
                                                <label for="attachment"
                                                       class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
                                                <div class="input-group">
                                                    <input type="file"
                                                           extension="<?php echo str_replace('.', '', get_option('ticket_attachments_file_extensions')); ?>"
                                                           filesize="<?php echo file_upload_max_size(); ?>"
                                                           class="form-control" name="attachments[0]"
                                                           accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                                                    <span class="input-group-btn">
													<button class="btn btn-success add_more_attachments p7"
                                                            type="button"><i class="fa fa-plus"></i></button>
												</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" id="submite" data-form="#new_ticket_form"
                            data-loading-text="<?php echo _l('wait_text'); ?>"
                            class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div id="contact_data"></div>

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
                    <h4>Este cliente foi contatado há mais de <?php echo(($dias != null) ? $dias->validation : '') ?>
                        dias, assim, ele será colocado em prioridade alta.</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Modal -->

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

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('Ok'); ?></button>
<!--                <button type="submit" class="btn btn-info">--><?php //echo _l('submit'); ?><!--</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal Evento-->


<!-- Modal -->
<div class="modal fade" id="add_contato_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">
                    Adicionar Solicitante
                </h3>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('Ok'); ?></button>
                <!--                <button type="submit" class="btn btn-info">--><?php //echo _l('submit'); ?><!--</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal Evento-->


<!-- Modal -->
<div class="modal fade" id="alert_analist" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">
                    Atenção
                </h3>
            </div>
            <div class="modal-body">
                <p>TEM CERTEZA ABOSLUTA QUE DESEJA ADICIONAR UM TICKET SEM VINCULAR UM ANALISTA?</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="okay" class="btn btn-default" data-dismiss="modal"><?php echo _l('Ok'); ?></button>
                <button type="button" id="cancel" class="btn btn-default" data-dismiss="modal"><?php echo _l('Cancel'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal Evento-->

    <?php init_tail(); ?>
    <?php echo app_script('assets/js', 'tickets.js'); ?>
    <script>
        $(function () {
            var admin = '<?php echo is_admin(); ?>';
            init_ajax_search('contact', '#contactid.ajax-search', {tickets_contacts: true});
            if(PAINEL == INORTE){
                init_ajax_search('customer', '#clientid.ajax-search'); //colocar uma variavel 'clients_contacts' para filtrar os contatos de acordo com a empresa selecionada
            }

            $('form').on('submit', function (e) { // habilita o campo antes do submit
                $(this).find('#priority').prop('disabled', false);
                e.preventDefault();
                var form = this;
                if($(this).valid()){
                    if($(this).find('#assigned').val() === ''){
                        confirmDialog(form);
                    }else{
                        form.submit();
                    }
                }
            });
            // $('#new_ticket_form').validate();


        });

        function confirmDialog(form){
            $.confirm({
                columnClass: 'col-md-6 col-md-offset-3',
                title: 'Atenção!',
                content: 'TEM CERTEZA ABSOLUTA QUE DESEJA ADICIONAR UM TICKET SEM VINCULAR UM ANALISTA?',
                draggable: true,
                type: 'red',
                typeAnimated: true,
                buttons: {
                    Sim: function () {
                        form.submit();
                    },
                    Nao: function () {
                        var btn = $("#submite");
                        btn.button('reset');
                        $.alert('Cancelado!');
                    }
                }
            });
        }



        $(function mudanca() {
            $("#service").on('change', function () {
                var id = $(this).val();
                $.ajax({
                    url: "servicenv2/" + id,
                    type: 'GET',
                    dataType: 'html',
                    data: {service: id},
                    success: function (data) {
                        if (data) {
                            var opcoes = '<option value=""></option>' +
                                '<option value="2">opção 1</option>';
                            // console.log(data);
                            $("#servicenv2").empty();
                            $("#servicenv2").html(data);
                            $("#servicenv2").selectpicker("refresh");

                        }
                    }
                });
            });
        });

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

        $("#priority").on('change', function () {
            let value = $(this).val();
            if (value === '6'){
                $('#scheduled_date_div').show();
            } else {
                $('#scheduled_date_div').hide();
            }
        }).trigger('change');


    </script>
<!--    </body>-->
<!--    </html>-->
