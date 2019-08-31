<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 01/02/2018
 * Time: 16:54
 */

init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 style="margin: 0px;">Controle de Equipamentos</h3>
                                <hr>
                                <?php if(has_permission('equipments','','create')){;?>
                                    <a onclick="entrada()" class="btn btn-info new">Registrar entrada</a>
                                    <a onclick="saida()" class="btn btn-info new">Registrar Saída</a>
                                <?php }?>
                                <a href="<?php echo admin_url("equipmens/patrimony");?>" class="btn btn-info new pull-right">Patrimonio</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s col-md-6" style="padding-left: 0px;">
                    <div class="panel-body">
                        <div class="row">
                            <h3 style="margin: 0px;">Equipamentos Dentro</h3>
                            <hr>
                            <?php render_datatable(array(
                                'Descrição',
                                'Tipo',
                                'Opções'
                            ),'equipments-in');?>
                        </div>
                    </div>
                </div>
                <div class="panel_s col-md-6" style="padding-right: 0px;">
                    <div class="panel-body">
                        <div class="row">
                            <h3 style="margin: 0px;">Equipamentos Fora</h3>
                            <hr>
                            <?php render_datatable(array(
                                'Descrição',
                                'Tipo Entrada',
                                'Tipo Saída',
                                'Opções'
                            ),'equipments-out');?>
                        </div>
                    </div>
                </div>
<!--                <div class="panel_s col-md-12" style="padding: 0px;">-->
<!--                    <div class="panel-body">-->
<!--                        <div class="row">-->
<!--                            --><?php //render_datatable(array(
//                                'Descrição',
//                                'Motivo',
//                                'Tipo',
//                            ),'equipments-history');?>
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
    </div>
</div>
<?php if(has_permission('equipments','','create')){?>
<!--Modal-Registrar-Entrada/Saída-->
<div class="modal fade" id="equipments_reg" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="equipments_reg_title"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open('',array("id"=>"equipments_reg_form")); ?>
                    <div class="col-md-6">
                        <?php echo render_datetime_input('data_from','Data Entrada',\Carbon\Carbon::now()->format("d/m/Y H:i:s"),array("required"=>"true")); ?>
                    </div>
                    <div class="col-md-6">

                    </div>
                    <hr class="col-md-12" style="padding:0px;margin-top:0px;">
                    <div class="col-md-6">
                        <?php echo render_select('staffid',$staffs,array("staffid","name"),'Funcionário',get_staff_user_id(),array("required"=>"true")); ?>
                        <div class="form-group" id="rel_type_wrapper" name="rel_type_wrapper">
                            <label for="rel_type" class="control-label">Origem</label>
                            <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required="true">
                                <option value="" disabled="true" selected="true"></option>
                                <option value="customer">
                                    <?php echo _l('client'); ?>
                                </option>
                                <option value="other">
                                    Outros
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo" class="control-label">Tipo</label>
                            <div id="tipo_select">
                                <select name="tipo" id="tipo" class="selectpicker" data-width="100%" data-live-search="true" required="true">
                                    <option value="" disabled="true" selected="true"></option>
                                    <option value="emprestimo">Emprestimo</option>
                                    <option value="manutencao">Manutenção</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group hide" id="rel_id_wrapper" name="rel_type_wrapper">
                            <label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
                            <div id="rel_id_select">
                                <select name="rel_id" id="rel_id" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required="true">
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr class="col-md-12" style="padding:0px;margin-top:0px;">
                    <div class="col-md-6">
                        <?php echo render_select('equipments', $equipments , array("id_equip_model","nome") , "Equipamento", "",array(), array("id"=>"equipments_wrapper"),"hide")?>
                        <?php echo render_input('description','Descrição do equipamento','', '',array("required"=>"true"),array("id"=>"description_wrapper"),'hide'); ?>
                        <!--Coloquei como ajax seach pq pode ter vários equipamentos e pode dar ruim-->
                        <?php echo render_select('equipment',array(),array(),'Equipamento','',array(),array("id"=>"equipment_wrapper"),'','',false);?>

                    </div>
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-12">
                        <?php echo render_textarea('observation','Observação'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-success" id="submitf" onclick="addequipin(); return false;"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close();?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!---->
<!--Modal-Adicionar-Outros-->
<div class="modal fade" id="cadastother" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="addotherf">
                        <div class="col-md-6">
                            <?php echo render_input('name','Nome <span class="bold text-danger">*</span>','', '',array("required"=>"true")); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('subtext','Observação'); ?>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_input('link','Link'); ?>
                        </div>
                    </form>
                </div>
            <div class="modal-footer">
                <button type="button" onclick="cadas_other('return')" class="btn btn-default">Voltar</button>
                <button type="button" onclick="cadastrar_other()" class="btn btn-success"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php }?>
<?php init_tail();?>
<script>
    var _rel_id = $('#rel_id');
    var _rel_type = $('#rel_type');
    var _rel_id_wrapper = $('#rel_id_wrapper');

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
                if($(this).val() == 'other')
                    $('.rel_id_label')[0].innerHTML += " <a onclick=\"cadas_other('enter'); return false;\">Adicionar</a>";
            } else {
                _rel_id_wrapper.addClass('hide');
            }
        }
    });

    function task_rel_select(){
        var serverData = {};
        var data = {};
        serverData.rel_id = _rel_id.val();
        data.type = _rel_type.val();
        <?php if(isset($task)){ ?>
        data.connection_type = 'task';
        data.connection_id = '<?php echo $task->id; ?>';
        <?php } ?>
        init_ajax_search(_rel_type.val(),_rel_id,serverData);
    }

    /**----------------------------------------**/
    $(function() {
        /** Iniciando as tabelas**/
        initDataTable('.table-equipments-in', admin_url+"equipmens?type=in");
        initDataTable('.table-equipments-out', admin_url+"equipmens?type=out");

        /** Definindo a ordenação das tabelas**/
        $(".table-equipments-in").DataTable().order(1,"DESC").draw();
        $(".table-equipments-out").DataTable().order(1,"DESC").draw();

        /** Iniciando o modo de pesquija Ajax no campo Equipamentos**/
        init_ajax_search('equipments',$('#equipment'),{});
    });
    /**----------------------------------------**/

    /**
     * Função para pegar os valores do formulário e retornar em Objeto
     * @return data Object
     */
    function define_var() {
        var data = {};

        data.data_from = $("#data_from").val();
        data.tipo = $("#tipo").val();
        data.rel_type = $("#rel_type").val();
        data.rel_id = $("#rel_id").val();
        data.staffid = $("#staffid").val();
        data.description = $("#description").val();
        data.observation = $("#observation").val();
        data.equipment = $("#equipment").val();
        data.id_equipment_model = $("#equipments").val();

        return data;
    }

    /**
     * Função para iniciar modal de entrada de equipamentos
     */
    function entrada() {
        $("#equipments_reg_title").html("Entrada de Equipamentos");
        $("#equipments_reg_form")[0].action = admin_url+"equipmens/add_equip_in";
        $("[for='data_from']").html("Data Entrada");
        $("[for='rel_type']").html("Origem");
        $("#equipment_wrapper").addClass("hide");
        $("#equipments_wrapper").removeClass("hide");
        $("#description_wrapper").removeClass("hide");
        $("[data-cv='this']").val("compra").html("Compra");
        $("[data-op-b='this']").addClass("hide");
        $("#tipo").selectpicker("refresh");
        $("#equipments_reg").modal("show");
        $("#data_from").val(getdate("datetime"));
        $("#description")[0].required = true;
    }

    /**
     * Função para iniciar modal de saída de equipamentos
     */
    function saida() {
        $("#equipments_reg_title").html("Saída de Equipamentos");
        $("#equipments_reg_form")[0].action = admin_url+"equipmens/add_equip_out";
        $("[for='data_from']").html("Data Saída");
        $("[for='rel_type']").html("Destino");
        $("#equipment_wrapper").removeClass("hide");
        $("#description_wrapper").addClass("hide");
        $("#equipments_wrapper").addClass("hide");
        $("[data-cv='this']").val("venda").html("Venda");
        $("[data-op-b='this']").removeClass("hide");
        $("#tipo").selectpicker("refresh");
        $("#equipments_reg").modal("show");
        $("#data_from").val(getdate("datetime"));
        $("#description")[0].required = false;
    }

    /**
     * Função para registrar entrada de equipamento via Ajax
     */
    function addequipin() {
        if(!verifica_requireds())
            return false;
        disabled(true);
        var data = define_var("addequipf");
        $.post($("#equipments_reg_form")[0].action, data, function(response){
            disabled(false);
            response = JSON.parse(response);
            alert_float(response.type,response.message);
            if(response.type == "success")
            {
                $("#equipments_reg_form")[0].reset();
                $("#rel_type").selectpicker("refresh");
                $("#tipo").selectpicker("refresh");
                // $('#rel_type_wrapper').addClass('hide');
                _rel_id_wrapper.addClass('hide');
                $("#equipments_reg").modal("hide");
                $("#equipment").val("");
                $("#equipment").selectpicker("refresh");

                $('.table-equipments-in').DataTable().ajax.reload().draw();
                $('.table-equipments-out').DataTable().ajax.reload().draw();
            }
        });
    }

    /**
     * Função para controlar a modal de registro de "Origens Diversas" de equipamentos
     * @param type boolean | Verifica se entra ou sai
     */
    function cadas_other(type) {
        if(type == "enter") {
            $("#equipments_reg").modal("hide");
            $('#cadastother').modal('show');
        }
        else{
            $("#equipments_reg").modal("show");
            $('#cadastother').modal('hide');
        }
    }

    /**
     * Função que registra "Origens" Diversas de equipamentos via Ajax
     */
    function cadastrar_other() {
        $("#addotherf input").prop("disabled", true);
        var data = {};
        data.name = $("#name").val();
        data.link = $("#link").val();
        data.subtext = $("#subtext").val();
        $.post(admin_url+"equipmens/addother", data, function(response){
            $("#addotherf input").prop("disabled", false);
            response = JSON.parse(response);
            alert_float(response.type,response.message);
            if(response.type == "success")
            {
                $("#addotherf")[0].reset();
                cadas_other('');
            }
        });
    }

    /**
     * Função para habilitar e desabilitar campos no formulário
     * @param type boolean
     */
    function disabled(type) {
        $("#addequipf input").prop("disabled", type);
        $("#addequipf select").prop("disabled", type);
        $("#addequipf textarea").prop("disabled", type);
        $("#submitf").prop("disabled", type);
    }

    /**
     * Retorna o equipamento para Entrada
     */
    function out_to_in(outid,equipid) {
        var data = {outid:outid,equipid:equipid};
        $.post(admin_url+"equipmens/out_to_in", data, function(response){
            response = JSON.parse(response);
            alert_float(response.type,response.message);
            if(response.type == "success")
            {
                $('.table-equipments-in').DataTable().ajax.reload().draw();
                $('.table-equipments-out').DataTable().ajax.reload().draw();
            }
        });
    }

    /**
     * Retorna o equipamento para o Além
     */
    function in_to_return(equipid)
    {
        $.post(admin_url+"equipmens/in_to_return", {equipid:equipid}, function(response){
            response = JSON.parse(response);
            alert_float(response.type,response.message);
            if(response.type == "success")
            {
                $('.table-equipments-in').DataTable().ajax.reload().draw();
                // $('.table-equipments-out').DataTable().ajax.reload().draw();
            }
        });
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

    function verifica_requireds()
    {
        var form = $("#equipments_reg_form");
        for(i = 0; i < form[0].length; i++)
        {
            if(form[0][i].required == true && form[0][i].value == "")
            {
                console.log("entrou required");
                alert_float("danger","Campo obrigatório em branco.");
                form[0][i].focus();
                return false;
            }
        }
        return true;
    }
</script>
