<?php
/**
// * Created by PhpStorm.
// * User: Desenvolvimento
// * Date: 27/12/2017
// * Time: 13:25
// */
init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <?php
                        $rel_type = '';
                        $rel_id = '';
                        $replyid = '';
                        ?>
                        <div class="row">
                            <div class="col-md-12">

                                <?php if (has_permission('fleet', '', 'create')){ ?>
                                    <a href="#" onclick="new_out()" class="btn btn-info new">Saída de frota</a>
                                <?php }?>

                                <a href="<?php echo admin_url("fleet/vehicles"); ?>" class="btn btn-info new">Veículos</a>

                                <?php if (has_permission('staff', '', 'edit')){ ?>
                                    <a href="<?php echo admin_url("staff"); ?>" class="btn btn-info new">Colaboradores</a>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
<!--                            <h3 class="text-defaulth no-margin">Veículos</h3>-->
                            <?php if(count($vehicles) > 0){ ?>
                                <div class="table-responsive">
                                    <table class="table dt-table table-striped">
                                        <thead>
                                        <th>Nome</th>
                                        <th>placa</th>
                                        <th>Status</th>
                                        <th>Opções</th>
                                        </thead>
                                        <tbody>
                                        <?php foreach($vehicles as $vehicle){ ?>
                                            <tr>
                                                <td>
<!--                                                    <a href="#">#--><?php //echo $vehicle["vehicleid"]." ".$vehicle["descricao"]; ?><!--</a>-->
                                                    <?php echo $vehicle["descricao"]; ?>
                                                </td>
                                                <td>
                                                    <?php echo $vehicle['placa']; ?>
                                                </td>
                                                <td>
                                                    <?php if($vehicle['active'] == 1) {?>
                                                        <?php if($vehicle['inuse'] == 1) {?>
                                                            <span class="inline-block label" style="color:#03A9F4;border:1px solid #03A9F4">
                                                                Em uso
                                                                <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" data-original-title="" title="" style="color: #03A9F4;border: 1px dashed #03A9F4;"></i>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if (has_permission('fleet', '', 'create')){ ?>
                                                                <a href="" onclick="edit_out(<?php echo $vehicle['vehicleid']; ?>); return false;" class="btn btn-default btn-icon">
                                                                    <i class="fa fa-arrow-right"></i>
                                                                </a>
                                                            <?php }?>
                                                        <?php }else if($vehicle['inuse'] == 0) {?>
                                                            <span class="inline-block label" style="color:#84c529;border:1px solid #84c529">
                                                                Disponível
                                                                <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" data-original-title="" title="" style="color: #84c529;border: 1px dashed #84c529;"></i>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if (has_permission('fleet', '', 'create')){ ?>
                                                                <a href="#" onclick="new_out(true,<?php echo $vehicle['vehicleid']; ?>); return false;" class="btn btn-default btn-icon">
                                                                    <i class="fa fa-arrow-right"></i>
                                                                </a>
                                                            <?php }?>
                                                        <?php }?>
                                                    <?php }else if($vehicle['active'] == 0) {?>
                                                        <span class="inline-block label" style="color:#ff2d42;border:1px solid #ff2d42">
                                                            Inativo
                                                            <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" data-original-title="" title="" style="color: #ff2d42;border: 1px dashed #ff2d42;"></i>
                                                        </span>
                                                        </td>
                                                        <td>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <p class="no-margin">Nenhum Veículo Disponível</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
<!--                            <h4 class="text-success no-margin">Saídas</h4>-->
                            <?php
                            render_datatable(array('#',
                                array('name'=>'Motivo'),
                                array('name'=>'Veículo'),
                                array('name'=>'Colaborador'),
                                array('name'=>'Distancia'),
                                array('name'=>'Tempo'),
                                array('name'=>'Data'),
                                array('name'=>'Opções')),'fleet');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Inicio-Modal-->
<div class="modal fade" id="new-fleet-out" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="conttent-modal-replies-title">Registrar Saída</div></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open(APP_BASE_URL.'admin/fleet/out',array("id"=>"form-new-vehicles"))?>
                    <div id="iniciodisable"></div>
                    <input type="hidden" name="outid" id="outid" value="">
                    <input type="hidden" name="vhid" id="vhid" value="">
                    <div class="col-md-12" style="padding: 0px;">
                        <div class="col-md-6">
                            <?php echo render_date_input('data','Data <span class="bold text-danger">*</span>',date('d/m/Y'),array("id"=>"data","required"=>"true"));?>
                            <?php echo render_select('staffid',$staff,array("staffid","name"),'Colaborador <span class="bold text-danger">*</span>',get_staff_user_id(),array("required"=>"true"));?>
<!--                                --><?php //echo render_select('vehicleid',$vehiclesact,array("vehicleid","descricao"),'Veículo','',array("required"=>"true"));?>
                            <div class="form-group">
                                <label for="Tipo" class="control-label">Veículo <span class="bold text-danger">*</span></label>
                                <select id="vehicleid" name="vehicleid" class="selectpicker" data-width="100%" data-none-selected-text="Nada selecionado" data-live-search="true" tabindex="-98" required="true">
                                    <option></option>
                                    <?php foreach ($vehicles as $vehiclesa){ ?>
                                        <option value='<?php echo $vehiclesa['vehicleid']; ?>' <?php if($vehiclesa['inuse'] || $vehiclesa['active'] == 0)echo "disabled";?>><?php echo $vehiclesa['descricao']; ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_textarea('motivo','Motivo <span class="bold text-danger">*</span>','',array("required"=>"true", "style"=>"height: 110px"));?>
                        </div>
                        <div class="col-md-6">
                            <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                            <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <option value="vendas"
                                    <?php if($rel_type == 'vendas'){echo 'selected';} ?>><?php echo _l('Vendas'); ?>
                                </option>
                                <option value="project"
                                    <?php if($rel_type == 'project'){echo 'selected';} ?>><?php echo _l('project'); ?></option>
                                <option value="invoice" <?php if($rel_type == 'invoice'){echo 'selected';} ?>>
                                    <?php echo _l('invoice'); ?>
                                </option>
                                <option value="customer"
                                    <?php if($rel_type == 'customer'){echo 'selected';} ?>>
                                    <?php echo _l('client'); ?>
                                </option>
                                <option value="estimate" <?php if($rel_type == 'estimate'){echo 'selected';} ?>>
                                    <?php echo _l('estimate'); ?>
                                </option>
                                <option value="contract" <?php if($rel_type == 'contract'){echo 'selected';} ?>>
                                    <?php echo _l('contract'); ?>
                                </option>
                                <option value="ticket" <?php if($rel_type == 'ticket'){echo 'selected';} ?>>
                                    <?php echo _l('ticket'); ?>
                                </option>
                                <option value="expense" <?php if($rel_type == 'expense'){echo 'selected';} ?>>
                                    <?php echo _l('expense'); ?>
                                </option>
                                <option value="lead" <?php if($rel_type == 'lead'){echo 'selected';} ?>>
                                    <?php echo _l('lead'); ?>
                                </option>
                                <option value="proposal" <?php if($rel_type == 'proposal'){echo 'selected';} ?>>
                                    <?php echo _l('proposal'); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding: 0px;">
                        <div class="col-md-6">

                            <?php echo render_input('km_inicial','Km Inicial <span class="bold text-danger">*</span>','','number',array("required"=>"true"));?>
                            <?php echo render_datetime_input('datetime_inicial', 'Inicio <span class="bold text-danger">*</span>','',array("required"=>"true"));?>
                            <?php echo render_input('local', 'Local'); ?>
                        </div>
                        <div id="fimdisable"></div>
                        <div class="col-md-6">

                            <div class="form-group<?php if($rel_id == ''){echo ' hide';} ?>" id="rel_id_wrapper">
                                <label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
                                <div id="rel_id_select">
                                    <select name="rel_id" id="rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
<!--                                        --><?php //if($rel_id != '' && $rel_type != ''){
//                                            $rel_data = get_relation_data($rel_type,$rel_id);
//                                            $rel_val = get_relation_values($rel_data,$rel_type);
//                                            echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
//                                        } ?>
                                    </select>
                                </div>
                            </div>

                            <?php echo render_input('km_final','Km Final <a onclick="getkmin()" id="kmfin">Copiar Km de inicio</a>','','number',array("placeholder"=>"Deixe em branco para Inicio"));?>
                            <?php echo render_datetime_input('datetime_final', 'Fim <a onclick="nowend()" id="hfin">Agora</a>','',array("placeholder"=>"Deixe em branco para Inicio"));?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_textarea('obs','Observação');?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-success" id="submitf" onclick="vsubmit(false)" id="modal-submit-bnt"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>
<!--Fim-Modal-->
<?php if (has_permission('fleet', '', 'create')){ ?>
    <!--Inicio-Modal-->
    <div class="modal fade" id="new-fleet-out-confirm" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><div id="conttent-modal-replies-title">Registrar Saída</div></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" >
                            <div id="new-fleet-out-confirm-msg">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="$('#new-fleet-out-confirm').modal('hide'); $('#new-fleet-out').modal('show'); return false;">Voltar</button>
                    <button type="button" class="btn btn-success" onclick="vsubmit(true)" id="modal-submit-bnt">Confirmar</button>
                </div>
                <?php echo form_close();?>
            </div>
        </div>
    </div>
    <!--Fim-Modal-->
<?php }?>
<?php if(has_permission('fleet','','delete')){ ?>
    <div class="modal fade" id="delete_out" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <?php echo form_open(admin_url('fleet/out/delete')); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Excluir</h4>
                </div>
                <div class="modal-body">
                    <div class="delete_id">
                        <input type="hidden" name="outiddelete" id="outiddelete" value="">
                    </div>
                    <p>O registro será excluido.</p>
                    <p>Tem certeza que deseja excluir?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-danger _delete"><?php echo _l('confirm'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php }?>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-fleet', admin_url+"utilities/fleet_report?type=manager");
        $('.table-fleet').DataTable().order()[0] = [0, "DESC"];
        $('.table-fleet').DataTable().ajax.reload();
    });
    var _rel_id = $('#rel_id'),
        _rel_type = $('#rel_type'),
        _rel_id_wrapper = $('#rel_id_wrapper');
    var staffid = <?php echo get_staff_user_id();?>;
    function delete_out(outid) {
        $("#outiddelete")[0].value = outid;
        $("#delete_out").modal("show");
    }
    function vsubmit(msg) {
        var i = 0;
        var form = $("#form-new-vehicles");
        for(i = 0; i < form[0].length; i++)
        {
            console.log("entrou for");
            if(form[0][i].required == true && form[0][i].value == "")
            {
                console.log("entrou required");
                alert_float("danger","Campo obrigatório em branco.");
                form[0][i].focus();
                return false;
            }
        }
        if($("#km_final")[0].value != "" || $("#datetime_final")[0].value != "") {
            if ($("#km_final")[0].value < $("#km_inicial")[0].value) {
                alert_float("danger", "O Km final deve ser maior que o inicial");
                return false;
            }
            if($("#km_final")[0].value == "" || $("#datetime_final")[0].value == "") {
                alert_float("danger", "Para finalizar é necessário incluir Km e a data.");
                return false;
            }
            if(msg) {
                $("#submitf").prop("disabled", true);
                form.submit();
            }
            else {
                var km_total_out = $("#km_final")[0].value - $("#km_inicial")[0].value;
                var mensagem = "<p>A distancia total é de " + km_total_out + " Km.</p>";
                if (km_total_out > <?php echo get_option('fleet_out_limit_alert');?>) {
                    mensagem += "<p><span class='text-warning'>A distancia ultrapassou o limite sem alerta de <?php echo get_option('fleet_out_limit_alert');?> Km.</span><p>";
                }
                $("#new-fleet-out-confirm-msg")[0].innerHTML = "<h4>" + mensagem + "</h4>";
                $("#new-fleet-out").modal("hide");
                $("#new-fleet-out-confirm").modal("show");
            }
        }
        else {
            $("#submitf").prop("disabled", true);
            form.submit();
        }
    }

    function getkmin() {
       $("#km_final")[0].value = $("#km_inicial")[0].value;
   }

   function new_out(vehi,vehid)
   {
       defaulth_modal();       //Setando todos os campos padrão
       if(vehi)
       {
           $("#vehicleid")[0].value = vehid;
           $("#vehicleid").selectpicker("refresh");
           km("new_out");
       }
       $("#modal-submit-bnt").show();
       $("#kmfin").show();
       $("#hfin").show();
       disabled(false,true);   //Habilitando todos os campos
       $("#conttent-modal-replies-title")[0].innerText = "Registrar Saída";
       $("#new-fleet-out").modal("show");
       $("#modal-submit-bnt")[0].innerText = "Iniciar";
   }

   function nowend() {
       $("#datetime_final")[0].value = getdate("datetime");
   }

   function edit_out(vehicleid)
   {
       $("#modal-submit-bnt").show();
       $("#kmfin").show();
       $("#hfin").show();
       defaulth_modal();
       if(PAINEL == INORTE)
           disabled(false, true);
       else
           disabled(true, false);
       // fill_fleet_out(vehicleid);
       $("#modal-submit-bnt")[0].innerText = "Finalizar";
       get_vehicle_output_info(vehicleid);
       $("#conttent-modal-replies-title")[0].innerText = "Editar Saída";
   }

   function see_out(outid)
   {
       // if(PAINEL == INORTE)
       //     disabled(false, true); //recomentei pq god is good!
       // else
       disabled(true, true);
       // fill_fleet_out(vehicleid);
       get_output_info(outid);
       $("#modal-submit-bnt").hide();
       $("#kmfin").hide();
       $("#hfin").hide();
       $("#conttent-modal-replies-title")[0].innerText = "Visualiza Saída";
       $("#new-fleet-out").modal("show");
   }
   function getdate(type)
   {
       var d = new Date();
       var min = d.getMinutes();
       var hou = d.getHours();
       if(min<10)
           min = "0"+min;
       if(hou<10)
           hou = "0"+hou;
       var mes = d.getMonth()+1;
       var day = d.getDate();
       if(mes<10)
           mes = "0"+mes;
       if(day<10)
           day = "0"+day;

       var date = day+'/'+mes+'/'+d.getFullYear();
       var hour = hou+':'+min;
       var datetime = date+' '+hour;
       if(type == "hour")
           return hour;
       else if(type == "date")
           return date;
       else if(type == "datetime")
           return datetime;

   }

   function defaulth_modal()
   {
       disabled(false,true);

       $("#data")[0].value = getdate("date");;
       $("#vehicleid")[0].value = "";
       $("#staffid")[0].value = staffid;
       $("#vehicleid").selectpicker("refresh");
       $("#staffid").selectpicker("refresh");
       $("#motivo")[0].value = "";
       $("#km_inicial")[0].value = "";
       $("#datetime_inicial")[0].value = getdate("datetime");
       $("#km_final")[0].value = "";
       $("#datetime_final")[0].value = "";
       $("#obs")[0].value = "";
       $("#outid")[0].value = "";
       $("#vhid")[0].value = "";
       $("#local")[0].value = "";
       $("#rel_type")[0].value = "";
       $("#rel_id").html("");
       $("#rel_type").selectpicker("refresh");
       $("#rel_id").selectpicker("refresh");
       _rel_id_wrapper.addClass('hide');
   }

   function get_vehicle_output_info(vehicleid)
   {
       $.get(admin_url + "fleet/get_vehicle_out/"+vehicleid).done(function(data,s) {
           fill_fleet_out(data);
           $("#new-fleet-out").modal("show");
       });
   }
   function get_output_info(outid)
   {
       $.get(admin_url + "fleet/get_out/"+outid).done(function(data,s) {
           fill_fleet_out(data);
       });
   }
   function disabled(type,all) {
       $("#data")[0].disabled = type;
       $("#staffid")[0].disabled = type;
       $("#motivo")[0].disabled = type;
       $("#km_inicial")[0].disabled = type;
       $("#datetime_inicial")[0].disabled = type;
       $("#vehicleid")[0].disabled = type;
       $("#local")[0].disabled = type;
       $("#rel_type")[0].disabled = type;
       $("#rel_id")[0].disabled = type;
       if(all)
       {
           $("#km_final")[0].disabled = type;
           $("#datetime_final")[0].disabled = type;
           $("#obs")[0].disabled = type;
       }
       else
       {
           $("#km_final")[0].disabled = !type;
           $("#datetime_final")[0].disabled = !type;
           $("#obs")[0].disabled = !type;
       }
   }

   function fill_fleet_out(voi)
   {
       // var voi = get_vehicle_output_info(vehicleid);
       voi = JSON.parse(voi);
       // console.log(voi);
       $("#data")[0].value = voi.dateform;
       $("#vehicleid")[0].value = voi.vehicleid;
       $("#staffid")[0].value = voi.staffid;
       $("#vehicleid").selectpicker("refresh");
       $("#staffid").selectpicker("refresh");
       $("#motivo")[0].value = voi.motivo;
       $("#km_inicial")[0].value = voi.km_inicial;
       $("#datetime_inicial")[0].value = voi.datetime_inicialform;
       $("#km_final")[0].value = voi.km_final;
       $("#datetime_final")[0].value = voi.datetime_finalform;
       $("#obs")[0].value = voi.obs;
       $("#outid")[0].value = voi.idsaida;
       $("#vhid")[0].value = voi.vehicleid;
       $("#local")[0].value = voi.local;
       $("#rel_type")[0].value = voi.rel_type;
       $("#rel_type").selectpicker("refresh");
       if(voi.rel_type != 'vendas' && voi.rel_type != '') {
           _rel_id_wrapper.removeClass('hide');
           $('.rel_id_label').html(_rel_type.find('option:selected').text());
           get_relation_value(voi.rel_id, voi.rel_type);
       }else{
           _rel_id_wrapper.addClass('hide');
           $("#rel_id").html("");
           $("#rel_id").selectpicker("refresh");
       }


   }

   function get_relation_value(rel_id, rel_type){
        var dado = {};
        dado.rel_id = rel_id;
        dado.rel_type = rel_type;
       $.get(admin_url + "fleet/get_relation_value/", dado).done(function(data) {
           $("#rel_id").html(data);
           $("#rel_id").selectpicker("refresh");
       });
   }

   $('#vehicleid').change(function() {
       km();
   });
   function km(type)
   {
       vehicleid = $("#vehicleid")[0].value;
       $.get(admin_url + "fleet/get_vehicle/"+vehicleid).done(function(data,s) {
           data = JSON.parse(data);
           if(type == "new_out")
               $("#km_inicial")[0].value = data.km_ultimo;
           else
               $("#km_inicial")[0].value = data.kmatual;
       });
   }

    init_ajax_search_tickets_search();

    $('.rel_id_label').html(_rel_type.find('option:selected').text());
    _rel_type.on('change', function() {
        if($(this).val() != 'vendas') {
            var clonedSelect = _rel_id.html('').clone();
            _rel_id.selectpicker('destroy').remove();
            _rel_id = clonedSelect;
            $('#rel_id_select').append(clonedSelect);
            $('.rel_id_label').html(_rel_type.find('option:selected').text());

            task_rel_select();
            if ($(this).val() != '') {
                _rel_id_wrapper.removeClass('hide');
            } else {
                _rel_id_wrapper.addClass('hide');
            }
        }
    });

    function task_rel_select(){
        var serverData = {};
        serverData.rel_id = _rel_id.val();
        data.type = _rel_type.val();
        <?php if(isset($task)){ ?>
        data.connection_type = 'task';
        data.connection_id = '<?php echo $task->id; ?>';
        <?php } ?>
        init_ajax_search(_rel_type.val(),_rel_id,serverData);
    }



</script>