<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 05/03/2018
 * Time: 13:37
 */

init_head(); ?>
<style>
    .min-altura{
        min-height: 150px;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="<?php echo admin_url("utilities/changelog");?>" class="btn btn-success new">Voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body changelog-container">
                        <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                            <?php $a = 0; foreach ($modules as $module){?>
                                <li class="nav-item<?php if($a == 0){echo " active";}?>">
                                    <a class="nav-link<?php if($a == 0){echo " active";} $a++;?>" id="pills<?php echo $module['moduleid'];?>-tab" data-toggle="pill" href="#pills<?php echo $module['moduleid'];?>-home" role="tab" aria-controls="pills<?php echo $module['moduleid'];?>-home" aria-selected="true">
                                        <?php echo $module['name'];?>
                                    </a>
                                </li>
                            <?php }?>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <?php $a = 0; foreach ($modules as $module){?>
                                <div class="tab-pane fade<?php if($a == 0){echo " active in";} $a++;?>" id="pills<?php echo $module['moduleid'];?>-home" role="tabpanel" aria-labelledby="pills<?php echo $module['moduleid'];?>-home-tab">
                                    <div id="hc-changelog" class="row">
                                        <div class="changelog-header">
                                            <div class="col-md-12 form-group mbot20" style="margin-left: -20px">
                                                <div class="col-md-4">
                                                    <?php echo render_input('module_name' . $module['moduleid'],'Módulo', $module['name'], 'text'); ?>
                                                </div>
                                                <div class="col-md-1 mtop30">
                                                    <a onclick='edit_module(<?php echo $module['moduleid']; ?>); return false;' class='btn btn-success btn-icon'><i class='fa fa-pencil-square-o'></i></a>
                                                    <a class='btn btn-danger btn-icon' onclick='delet_module(<?php echo $module['moduleid']; ?>); return false;'><i class='fa fa-remove'></i></a>
                                                </div>
                                            </div>

                                            <div class="clearfix"></div>
                                            <div class="col-md-12 mbot15">
                                                <?php
                                                echo render_textarea('observation' . $module['moduleid'], 'Observação', $module['observation'], array(), array(), '', 'tinymce min-altura');
                                                ?>
                                            </div>

                                        </div>
                                        <a href="#" data-toggle="modal" data-module="<?php echo $module['moduleid']; ?>" data-target="#changelog_bulk_actions"
                                           class="btn btn-info mbot15"><?php echo _l('bulk_actions'); ?></a>
                                        <div class="clearfix"></div>
                                        <div class="changelog-container">
                                            <table class="table table-changelog<?php echo $module['moduleid']; ?>" id="table-changelog<?php echo $module['moduleid']; ?>">
                                                <thead>
                                                    <tr>
                                                        <th><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="changelog<?php echo $module['moduleid'] ?>"><label></label></div></th>
                                                        <th>#</th>
                                                        <th>Autor</th>
                                                        <th>Data</th>
                                                        <th>Titulo</th>
                                                        <th>Tipo</th>
                                                        <th>Versão</th>
                                                        <?php if(PAINEL == INORTE){ ?>
                                                            <th>Tipo de Link</th>
                                                            <th>Link</th>
                                                        <?php } else { ?>
                                                            <th>Base de Conhecimento</th>
                                                        <?php } ?>
                                                        <th>Módulo</th>
                                                        <th>Visível</th>
                                                        <th>Opções</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        foreach ($versions as $row)
                                                        {
                                                            if($row->moduleid_fk == $module['moduleid'])
                                                            {
                                                                $alteracoes = $this->db->query("SELECT * FROM changelog WHERE Version = '".$row->Version."' AND moduleid_fk = '" . $module['moduleid'] . "' ORDER BY Version DESC, Tipo DESC, Data DESC, change_id DESC")->result();
                                                                foreach ($alteracoes as $alt)
                                                                {
                                                                    $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="Definir visibilidade deste registro">
                                                                        <input type="checkbox" data-switch-url="' . admin_url('changelog/change_log_status') . '" name="onoffswitch" class="onoffswitch-checkbox" id="switch' . $alt->change_id . '" data-id="' . $alt->change_id . '" ' . ($alt->visible == 1 ? 'checked' : '') . '>
                                                                        <label class="onoffswitch-label" for="switch' . $alt->change_id . '"></label>
                                                                    </div>';
                                                                    echo "<tr>
                                                                        <td>
                                                                            <div class='checkbox'><input type='checkbox' value='" . $alt->change_id . "'><label></label></div>
                                                                        </td>
                                                                        <td>
                                                                            #" . $alt->change_id . "
                                                                        </td>
                                                                        <td>
                                                                            ".render_input('Autor'.$alt->change_id,'',$alt->Autor, '', array("style"=>"max-width:150px;"))."
                                                                        </td>
                                                                        <td>
                                                                            ".render_date_input('Data'.$alt->change_id,'',\Carbon\Carbon::parse($alt->Data)->format("d/m/Y"),array("style"=>"max-width:120px;"))."
                                                                        </td>
                                                                        <td>
                                                                            ".render_input('Titulo'.$alt->change_id,'',$alt->Titulo)."
                                                                        </td>
                                                                        <td>
                                                                            ".render_select('Tipo'.$alt->change_id,array(array("tipo"=>"Implementação","value"=>"new"),array("tipo"=>"Correção","value"=>"bug"),array("tipo"=>"Notas","value"=>"note")),array("value","tipo"),'',$alt->Tipo)."
                                                                        </td>
                                                                        <td>
                                                                            ".render_input('Version'.$alt->change_id,'',$alt->Version,'text',array("style"=>"max-width:90px;"),array())."
                                                                        </td>";
                                                                        if (PAINEL == INORTE) {
                                                                            if($alt->rel_type == ''){
                                                                                echo "<td>
                                                                                    " . render_select('rel_type' . $alt->change_id, array(array("id" => "ticket", "value" => "Solicitação"), array("id" => "knowledge_base", "value" => "Base Conhecimento"), array("id" => "external_link", "value" => "Link Externo")), array("id", "value"), '', 'knowledge_base', array("onchange" => "change_td_table(" . $alt->change_id . ")"), array("style" => "min-width:160px;")) . "
                                                                                </td>";
                                                                            }else{
                                                                                echo "<td>
                                                                                    " . render_select('rel_type' . $alt->change_id, array(array("id" => "ticket", "value" => "Solicitação"), array("id" => "knowledge_base", "value" => "Base Conhecimento"), array("id" => "external_link", "value" => "Link Externo")), array("id", "value"), '', $alt->rel_type, array("onchange" => "change_td_table(" . $alt->change_id . ")"), array("style" => "min-width:160px;")) . "
                                                                                </td>";
                                                                            }
                                                                        }

                                                                        echo "<td id='rel_id_select" . $alt->change_id . "' style='min-width: 210px; max-width: 210px;'>";

                                                                        if ($alt->rel_type == 'external_link') {
                                                                            echo '<input style="min-width: 220px; max-width: 220px;" type="text" id="link' . $alt->change_id . '" name="link' . $alt->change_id . '" class="form-control" value="' . $alt->slug . '">';
                                                                        }else if($alt->rel_type != '' && $alt->rel_id != 0){
                                                                            $rel_data = get_relation_data($alt->rel_type, $alt->rel_id);
                                                                            $rel_val = get_relation_values($rel_data, $alt->rel_type);
                                                                            echo '<select name="link' . $alt->change_id . '" 
                                                                                    id="link' . $alt->change_id . '" class="ajax-search"
                                                                                     data-live-search="true"
                                                                                    data-none-selected-text="Nada selecionado">';
                                                                            // colocar isso data-width="100%" pra mudar width
                                                                                echo '<option value="' . $rel_val['id'] . '" selected data-subtext="' . $rel_val['subtext'] . '">' . $rel_val['name'] . '</option>';
                                                                            echo '</select>';
//                                                                            echo "<a target='_blank' href='".$rel_val['link']."'>";
                                                                        }elseif(isset($alt->slug) && ($alt->slug != null)) {
                                                                            $id_by_slug = $this->db->query("SELECT articleid FROM tblknowledgebase WHERE slug = '" . $alt->slug . "'")->result();
//                                                                            var_dump($id_by_slug);
                                                                            if($id_by_slug != null){
                                                                                $rel_data = get_relation_data('knowledge_base', $id_by_slug[0]->articleid);
                                                                                $rel_val = get_relation_values($rel_data, 'knowledge_base');
                                                                                echo '<select name="link' . $alt->change_id . '" 
                                                                                    id="link' . $alt->change_id . '" class="ajax-search"
                                                                                     data-live-search="true"
                                                                                    data-none-selected-text="Nada selecionado">';
                                                                                // colocar isso data-width="100%" pra mudar width
                                                                                echo '<option value="' . $rel_val['id'] . '" selected data-subtext="' . $rel_val['subtext'] . '">' . $rel_val['name'] . '</option>';
                                                                                echo '</select>';
                                                                            }else{
                                                                                echo '<select name="link' . $alt->change_id . '" 
                                                                                    id="link' . $alt->change_id . '" class="ajax-search"
                                                                                     data-live-search="true"
                                                                                    data-none-selected-text="Nada selecionado">';
                                                                                echo '</select>';
                                                                            }
//                                                                            echo "<a target='_blank' href='" . APP_BASE_URL . "admin/knowledge_base/view/" . $alt->slug . "'>";
                                                                        }else{
                                                                            echo '<select name="link' . $alt->change_id . '" 
                                                                                    id="link' . $alt->change_id . '" class="ajax-search"
                                                                                     data-live-search="true"
                                                                                    data-none-selected-text="Nada selecionado">';
                                                                            echo '</select>';
                                                                        }

                                                                        echo "</td>";

//                                                                        echo "<td>
//                                                                            ".render_select('slug'.$alt->change_id, $knowledgebase,array('slug','subject'),'',$alt->slug,array(),array("style"=>"max-width:136px;"))."
//                                                                        </td>";
                                                                        echo "<td>
                                                                            ".render_select('moduleid_fk'.$alt->change_id,$modules, array('moduleid','name'),'',$alt->moduleid_fk,array(),array(),'','',false)."
                                                                        </td>
                                                                        <td>
                                                                            " . $toggleActive . "
                                                                        </td>
                                                                        <td>
                                                                            <a onclick='edit(".$alt->change_id."); return false;' class='btn btn-success btn-icon'><i class='fa fa-pencil-square-o'></i>
                                                                            </a><a class='btn btn-danger btn-icon' onclick='delet(".$alt->change_id."); return false;'><i class='fa fa-remove'></i></a>
                                                                        </td>
                                                                        </tr>";
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade bulk_actions" id="changelog_bulk_actions" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <?php
                $rel_type = '';
                $rel_id = '';
                ?>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="hidden">
                    <input name="moduleid-modal">
                </div>
                    <div class="checkbox checkbox-danger">
                        <input type="checkbox" name="mass_delete" id="mass_delete">
                        <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                    </div>
                    <hr class="mass_delete_separator"/>
                <div id="bulk_change">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <?php echo render_input('Autor_bulk', 'Autor', ''); ?>
                            </div>
<!--                            <div class="col-md-6">-->
<!--                                --><?php //echo render_date_input('Data_bulk','Data', '',array("class"=>"col-md-6 offset-md-6")); ?>
<!--                            </div>-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?php echo render_select('Tipo_bulk', array(array("tipo"=>"Implementação", "value"=>"new"), array("tipo"=>"Correção", "value"=>"bug"), array("tipo"=>"Notas", "value"=>"note")), array("value","tipo"),'Tipo', ''); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input('Version_bulk', 'Versão', ''); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?php if(PAINEL == QUANTUM){ ?>
                                    <?php echo render_select('slug_bulk', $knowledgebase, array('slug','subject'),'Base Conhecimento', array()); ?>
                                <?php } else{
                                    echo render_date_input('Data_bulk','Data', '',array("class"=>"col-md-6 offset-md-6"));
                                }?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_select('moduleid_fk_bulk', $modules, array('moduleid','name'),'Módulo','',array(),array(),'',''); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6" style="float: left;">
                                    <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                                    <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <option value="ticket" <?php if($rel_type == 'ticket'){echo 'selected';} ?>>
                                            <?php echo _l('Solicitação'); ?>
                                        </option>
                                        <option value="knowledge_base" <?php if($rel_type == 'knowledge_base'){echo 'selected';} ?>>
                                            <?php echo _l('Base de Conhecimento'); ?>
                                        </option>
                                        <option value="external_link" <?php if($rel_type == 'external_link'){echo 'selected';} ?>>
                                            <?php echo _l('Link Externo'); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6" style="float: right; padding-left: unset">
                                    <div class="form-group<?php if($rel_id == ''){echo ' hide';} ?>" id="rel_id_wrapper">
                                        <label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
                                        <div id="rel_id_select">
                                            <!--                                        <select name="rel_id" id="rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="--><?php //echo _l('dropdown_non_selected_tex'); ?><!--">-->
                                            <!--                                        </select>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="label label-default mtop5 mleft15" data-toggle="tooltip" data-title="Clique para definir a visibilidade dos registros" href="#" onclick="show_switch(); return false;">Visibilidade</a>
                    <div class="onoffswitch mtop15 mleft15 hidden" data-toggle="tooltip" data-title="Definir visibilidade de todos os registros" id="switch_div">
                        <input type="checkbox" data-switch-url="" name="onoffswitch" class="onoffswitch-checkbox" id="switch_bulk" data-id="switch_bulk">
                        <label class="onoffswitch-label" for="switch_bulk"></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo _l('close'); ?></button>
                <a href="#" class="btn btn-info"
                   onclick="changelog_bulk_actions(this); return false;"><?php echo _l('confirm'); ?></a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php init_tail(); ?>
<script>
    $(function () {
        // Mantem a ultima tab ativa ---------------------------------- //
        $('a[data-toggle="pill"]').on('show.bs.tab', function(e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#pills-tab a[href="' + activeTab + '"]').tab('show');
        }
        // ----------------------------------------------------------- //

        var modules = '<?php echo addslashes(json_encode($modules)); ?>';
        modules = JSON.parse(modules);
        // console.log(modules);

        var alteracoes = '<?php echo addslashes(json_encode($this->db->query("SELECT * FROM changelog ORDER BY Version DESC, Tipo DESC, Data DESC")->result())); ?>';
        alteracoes = JSON.parse(alteracoes);

        if(alteracoes !== undefined){
            for (var alt in alteracoes) {
                if (alteracoes.hasOwnProperty(alt)) {
                    if(alteracoes[alt].rel_type !== 'external_link'){
                        task_rel_select($('#link'+alteracoes[alt].change_id), alteracoes[alt].rel_type);
                        // console.log(alteracoes[alt]);
                    }
                }
            }
        }

        if(modules !== undefined) {
            for (var module in modules) {
                if (modules.hasOwnProperty(module)) {
                    initDataTableOffline("#table-changelog" + modules[module].moduleid, undefined, undefined, [0]);
                    // console.log(modules[module].moduleid);
                }
            }
        }



        $('#changelog_bulk_actions').on('show.bs.modal', function(e) { // pega o id do modulo no momento que abre o modal
            var moduleid = $(e.relatedTarget).data('module');
            $(e.currentTarget).find('input[name="moduleid-modal"]').val(moduleid);
        });

    });

    function edit(id) {
        var data = {};
        data.Autor = $("#Autor"+id).val();
        data.Data = $("#Data"+id).val();
        data.Titulo = $("#Titulo"+id).val();
        data.Tipo = $("#Tipo"+id).val();
        data.Version = $("#Version"+id).val();
        data.slug = $("#slug"+id).val();
        // data.visible = $('#switch'+id).val();
        data.moduleid_fk = $("#moduleid_fk"+id).val();
        data.rel_type = $("#rel_type"+id).val();
        data.rel_id = $("#link"+id).val();

        data.id = id;
        $.post(admin_url+"utilities/changelog/edit?tipo=edit",data,function (response) {
            response = JSON.parse(response);
            alert_float(response.tipo,response.msg);
        });
        // console.log(data);
    }
    function delet(id) {
        if (confirm('Tem certeza que deseja excluir?')) {
            var data = {};
            data.id = id;
            $.post(admin_url+"utilities/changelog/edit?tipo=delete",data,function (response) {
                response = JSON.parse(response);
                if(response.tipo == "success")
                    window.location.href = window.location.href;
                else
                    alert_float(response.tipo,response.msg);
            });
        }
    }

    function edit_module(id){
        var data = {};
        data.name = $("#module_name" + id).val();
        tinyMCE.triggerSave(); // precisa disso pra pegar o valor atual no campo de texto
        data.observation = $("#observation" + id).val();
        data.moduleid = id;

        $.post(admin_url+"utilities/changelog/edit?tipo=edit_module",data,function (response) {
            response = JSON.parse(response);
            alert_float(response.tipo,response.msg);
        });
    }

    function delet_module(id){
        var name = $("#module_name" + id).val();
        confirmDialogModule(name, id);
    }

    function confirmDialogModule(module_name, id){
        $.confirm({
            columnClass: 'col-md-6 col-md-offset-3',
            title: 'Atenção!',
            content: 'Deletar o módulo apagará todos os itens de mudança do sistema juntamente à ele. Deseja realmente apagar o módulo "' + module_name + '" ?',
            draggable: true,
            type: 'red',
            typeAnimated: true,
            buttons: {
                Sim: function () {
                    var data = {};
                    data.moduleid = id;

                    $.post(admin_url+"utilities/changelog/edit?tipo=delete_module",data,function (response) {
                        response = JSON.parse(response);
                        if(response.tipo == "success")
                            window.location.href = window.location.href;
                        else
                            alert_float(response.tipo,response.msg);
                    });
                },
                Nao: function () {
                    $.alert('Cancelado!');
                }
            }
        });
    }

    function changelog_bulk_actions(event) {
        var moduleid = $('input[name="moduleid-modal"').val(); //pega o id do modulo

        var r = confirm(appLang.confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var mass_delete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {};
            if (mass_delete == false || typeof(mass_delete) == 'undefined') {
                data.autor = $('#Autor_bulk').val();
                data.data = $('#Data_bulk').val();
                data.tipo= $('#Tipo_bulk').val();
                data.version = $('#Version_bulk').val();
                data.slug = $('#slug_bulk').val();
                data.moduleid_fk = $('#moduleid_fk_bulk').val();
                data.rel_type = $('#rel_type').val();
                data.rel_id = $('#rel_id').val();

                if($('#switch_div').is(":visible")){
                    data.visible = switch_changelog('#switch_bulk');
                }else{
                    data.visible = 'undefined'; // teve que ser texto pra passar no PHP
                }

                // console.log(data.visible);

                if (data.author == '' && data.data == '' && data.tipo == '' && data.version == '' && data.slug == '' &&
                    data.moduleid_fk == '' && data.visible == '' && data.rel_type == '' && data.rel_id == '') {
                    return;
                }
            } else {
                data.mass_delete = true;
            }
            var rows = $('.table-changelog' + moduleid).find('tbody tr');
            $.each(rows, function() {

                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            console.log(data.rel_id);
            $(event).addClass('disabled');
            setTimeout(function() {
                $.post(admin_url + 'changelog/bulk_action', data).done(function() {
                    window.location.reload();
                });
            }, 50);
        }
    }

    function switch_changelog(field) {
        var status;
        status = 0;
        if ($(field).prop('checked') === true) {
            status = 1;
        }
        return status;
    }

    function show_switch(){
        if($('#switch_div').hasClass('hidden')){
            $('#switch_div').removeClass('hidden');
        }else{
            $('#switch_div').addClass('hidden');
        }
    }

    function task_rel_select(_rel_id, _rel_type){
        if(_rel_type === ''){
            _rel_type = 'knowledge_base';
        }
        var serverData = {};
        serverData.rel_id = _rel_id.val();
        init_ajax_search(_rel_type, _rel_id, serverData);
    }


    // function get_relation_value(rel_id, rel_type){
    //     var dado = {};
    //     dado.rel_id = rel_id;
    //     dado.rel_type = rel_type;
    //     $.get(admin_url + "changelog/get_relation_value/", dado).done(function(data) {
    //         $("#rel_id").html(data);
    //         $("#rel_id").selectpicker("refresh");
    //     });
    // }


    var _rel_id = $('#rel_id'),
        _rel_type = $('#rel_type'),
        _rel_id_wrapper = $('#rel_id_wrapper');
    $('.rel_id_label').html(_rel_type.find('option:selected').text());

    _rel_type.on('change', function() {
        if(_rel_type.val() === 'external_link') {
            if($('#rel_id').hasClass('ajax-search')){
                $('#rel_id').selectpicker('destroy').remove(); //destroi o elemento antigo
            }else{
                $('#rel_id').remove(); //destroi o elemento antigo
            }

            $('#rel_id_select').append('<input type="text" id="rel_id" name="rel_id" class="form-control" value="">'); //adiciona o novo elemento que no caso é o input

            $('.rel_id_label').html(_rel_type.find('option:selected').text()); // altera a label acima do elemento

        }else{
            if($('#rel_id').hasClass('ajax-search')){
                $('#rel_id').selectpicker('destroy').remove(); //destroi o elemento antigo
            }else{
                $('#rel_id').remove(); //destroi o elemento antigo
            }
            // adiciona um novo select de live-search
            $('#rel_id_select').append('<select name="rel_id" id="rel_id" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="Nada selecionado"></select>');

            _rel_id = $('#rel_id');
            var clonedSelect = _rel_id.html('').clone();
            _rel_id.selectpicker('destroy').remove();
            _rel_id = clonedSelect;

            $('#rel_id_select').append(clonedSelect);
            $('.rel_id_label').html(_rel_type.find('option:selected').text());

            // console.log($('#rel_id').val());
            task_rel_select($('#rel_id'), _rel_type.val()); // inicia o live-search
        }

        if ($(this).val() != '') {
            _rel_id_wrapper.removeClass('hide');
        } else {
            _rel_id_wrapper.addClass('hide');
        }

    });


    function change_td_table(change_id) {
        var rel_type_auxiliar = $('#rel_type'+change_id);
        var linkid = $('#link' + change_id);
        if(rel_type_auxiliar.val() !== ''){
            if(rel_type_auxiliar.val() === 'external_link') {
                if(linkid.hasClass('ajax-search')){
                    linkid.selectpicker('destroy').remove(); //destroi o elemento antigo
                }else{
                    linkid.remove(); //destroi o elemento antigo
                }

                $('#rel_id_select' + change_id).append('<input style="min-width: 220px; max-width: 220px" type="text" id="link' + change_id + '" name="link' + change_id + '" class="form-control" value="">'); //adiciona o novo elemento que no caso é o input

            }else{
                if($('#link' + change_id).hasClass('ajax-search')){
                    $('#link' + change_id).selectpicker('destroy').remove(); //destroi o elemento antigo
                }else{
                    $('#link' + change_id).remove(); //destroi o elemento antigo
                }
                // adiciona um novo select de live-search
                $('#rel_id_select' + change_id).append('<select style="min-width: 210px !important; max-width: 210px !important;" name="link' + change_id + '" id="link' + change_id + '" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="Nada selecionado"></select>');

                _rel_id = $('#link' + change_id);
                var clonedSelect = _rel_id.html('').clone();
                _rel_id.selectpicker('destroy').remove();
                _rel_id = clonedSelect;

                $('#rel_id_select' + change_id).append(clonedSelect);

                task_rel_select(_rel_id, rel_type_auxiliar.val());
            }
        }
    }


</script>
