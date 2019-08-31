<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento 04
 * Date: 27/11/2017
 * Time: 12:13
 */
init_head(); ?>
<style>
    .changelog-container {
        position: relative;
        padding-left: 60px;
    }

    .changelog-container:before {
        content: ' ';
        position: absolute;
        left: 28px;
        height: 100%;
        width: 4px;
        background-color: #57B95F;
    }

    .changelog-container>p {
        position: relative;
        font-weight: 700;
        color: #57B95F;
        font-size: 20px;
    }

    .changelog-container li
    {
        font-size: 15px;
    }

    .changelog-container>p:before {
        content: ' ';
        position: absolute;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: #57B95F;
        left: -42px;
    }
    .changelog-container .panel-heading .panel-title {
        font-size: 14px;
        text-transform: none;
    }

    .changelog-container .panel-heading .panel-title a {
        font-weight: 700;
    }

</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
<!--            <div class="col-md-12">-->
            <?php if(has_permission('utilities', '', 'edit') && has_permission('utilities', '', 'delete')){ ?>
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <?php
                        $rel_type = '';
                        $rel_id = '';
                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if(is_admin() || has_permission('utilities', '', 'create')){ ?>
                                    <a href="#" onclick="new_change(); return false;" class="btn btn-success new">Novo</a>
                                    <a href="#" onclick="$('#add-module').modal('show'); return false;" class="btn btn-success new">Novo Módulo</a>
                                <?php } ?>
                                <?php if(PAINEL != INORTE){ ?>
                                    <a href="<?php echo APP_TEST_URL;?>admin" class="btn btn-info new">Ambiente de testes</a>
                                <?php } ?>
                                <a href="<?php echo admin_url("utilities/changelog/edit");?>" class="btn btn-default btn-icon pull-right">
                                    <i class="fa fa-pencil-square-o"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
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
                                        <div class="col-md-12">
                                        <?php if(!empty($module['observation'])){ ?>
                                            <h4 class="bold no-margin">Observações: </h4>
                                            <hr>
                                            <div class="panel-body tc-content">
                                                <?php echo $module['observation']; ?>
                                            </div>
                                            <hr class="hr-panel-heading">
                                        <?php } ?>
                                        </div>
                                        <div class="changelog-header">
                                            <h2>&nbsp;&nbsp;&nbsp;<?php echo $module['name'];?>
                                            </h2>
                                        </div>
                                        <div class="changelog-container">
                                            <?php
                                                foreach ($versions as $row)
                                                {
                                                    if($row->moduleid_fk == $module['moduleid'])
                                                    {
                                                        $version_visible = $this->db->query("SELECT COUNT(visible) as cont FROM changelog WHERE Version='".$row->Version."' AND moduleid_fk = '" . $module['moduleid'] . "' AND visible = 1")->row("cont");
                                                        if($version_visible > 0 || has_permission('utilities', '', 'edit')){
                                                        echo "<p>".$row->Version."</p>";
                                                        $alteracoes = $this->db->query("SELECT * FROM changelog WHERE Version = '".$row->Version."' AND moduleid_fk = '" . $module['moduleid'] . "' ORDER BY Version DESC, Tipo DESC, Data DESC, change_id DESC")->result();
                                                        foreach ($alteracoes as $alt)
                                                        {
                                                            if($alt->visible == 0 && !has_permission('utilities', '', 'edit'))
                                                                continue;
                                                            $rel_type = $alt->rel_type;
                                                            $rel_id = $alt->rel_id;
                                                            if($alt->Tipo == "new")
                                                            {
                                                                echo "<li><i class='fa fa-info-circle' data-toggle='tooltip' data-title='".$alt->Autor."' data-original-title='".$alt->Autor."' title='".$alt->Autor."'></i>&nbsp;";
                                                                if ($rel_type == 'external_link') {
                                                                    echo "<a target='_blank' href='" . $alt->slug . "'>";
                                                                }else if($rel_type != '' && $rel_id != 0){
                                                                    $rel_data = get_relation_data($rel_type,$rel_id);
                                                                    $rel_val = get_relation_values($rel_data,$rel_type);
                                                                    echo "<a target='_blank' href='".$rel_val['link']."'>";
                                                                }elseif(isset($alt->slug) && ($alt->slug != null)) {
                                                                    echo "<a target='_blank' href='" . APP_BASE_URL . "admin/knowledge_base/view/" . $alt->slug . "'>";
                                                                }

                                                                echo $alt->Titulo;
                                                                if(isset($alt->slug) || ($rel_type != '' && $rel_id != 0))
                                                                    echo "</a>";

                                                                $date = date_create($alt->Data);
                                                                echo " - ".date_format($date, 'd/m/Y');
                                                                if($alt->visible == 0)
                                                                    echo '  &#10153;  <span class="badge badge-warning mbot5" style="background-color: yellow; color: #0a0a0a;">DESATIVADO</span>';

                                                                echo "</li>";
                                                            }
                                                        }
                                                ?>
                                            <div id="accordion" class="panel-group">
                                                <div class="panel panel-default">
                                                    <?php $notas_total = $this->db->query("SELECT COUNT(Tipo) as cont FROM changelog WHERE Version='".$row->Version."' AND moduleid_fk = '" . $module['moduleid'] . "' AND Tipo = 'note'")->row("cont"); ?>
                                                    <?php $notas_visible = $this->db->query("SELECT COUNT(Tipo) as cont FROM changelog WHERE Version='".$row->Version."' AND moduleid_fk = '" . $module['moduleid'] . "' AND visible = 1 AND Tipo = 'note'")->row("cont"); ?>
                                                    <?php if(($notas_total > 0 && $notas_visible > 0) || (has_permission('utilities', '', 'edit') && $notas_total > 0)){?>
                                                    <div id="heading-<?php echo $module['moduleid'] . '-' . str_replace(".","-",$row->Version); ?>-upgrade-notes" class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a href="#<?php echo $module['moduleid'] . '-' . str_replace(".","-",$row->Version); ?>-upgrade-notes" data-toggle="collapse" data-parent="#accordion" class="collapsed">Notas da Versão</a>
                                                        </h4>
                                                    </div>
                                                    <div id="<?php echo $module['moduleid'] . '-' . str_replace(".","-",$row->Version); ?>-upgrade-notes" class="panel-collapse collapse" style="height: 0px;">
                                                        <div class="panel-body">
                                                            <ul>
                                                                <?php
                                                                    foreach ($alteracoes as $alt)
                                                                    {
                                                                        if($alt->visible == 0 && !has_permission('utilities', '', 'edit'))
                                                                            continue;
                                                                        $rel_type = $alt->rel_type;
                                                                        $rel_id = $alt->rel_id;
                                                                        if($alt->Tipo == "note")
                                                                        {
                                                                            echo "<li><i class='fa fa-info-circle' data-toggle='tooltip' data-title='".$alt->Autor."' data-original-title='".$alt->Autor."' title='".$alt->Autor."'></i>&nbsp;";
                                                                            if ($rel_type == 'external_link') {
                                                                                echo "<a target='_blank' href='" . $alt->slug . "'>";
                                                                            }else if($rel_type != '' && $rel_id != 0){
                                                                                $rel_data = get_relation_data($rel_type,$rel_id);
                                                                                $rel_val = get_relation_values($rel_data,$rel_type);
                                                                                echo "<a target='_blank' href='".$rel_val['link']."'>";
                                                                            }elseif(isset($alt->slug) && ($alt->slug != null)) {
                                                                                echo "<a target='_blank' href='" . APP_BASE_URL . "admin/knowledge_base/view/" . $alt->slug . "'>";
                                                                            }

                                                                            echo $alt->Titulo;
                                                                            if(isset($alt->slug) || ($rel_type != '' && $rel_id != 0))
                                                                                echo "</a>";

                                                                            $date = date_create($alt->Data);
                                                                            echo " - ".date_format($date, 'd/m/Y');
                                                                            if($alt->visible == 0)
                                                                                echo '  &#10153;  <span class="badge badge-warning mbot5" style="background-color: yellow; color: #0a0a0a;">DESATIVADO</span>';

                                                                            echo "</li>";
                                                                        }
//                                                                        if ($alt->Tipo == "note") {
//
//                                                                            if(isset($alt->slug))
//                                                                                echo "<li style='list-style-type: disc;'><i class='fa fa-info-circle' data-toggle='tooltip' data-title='".$alt->Autor."' data-original-title='".$alt->Autor."' title='".$alt->Autor."'></i>&nbsp;<a target='_blank' href='".APP_BASE_URL."admin/knowledge_base/view/".$alt->slug."'>".$alt->Titulo."</a></li>";
//                                                                            else
//                                                                                echo "<li style='list-style-type: disc;'><i class='fa fa-info-circle' data-toggle='tooltip' data-title='".$alt->Autor."' data-original-title='".$alt->Autor."' title='".$alt->Autor."'></i>&nbsp;".$alt->Titulo."</li>";
//                                                                        }
                                                                    }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <?php }
                                                    $bugs_total = $this->db->query("SELECT COUNT(Tipo) as cont FROM changelog WHERE Version='".$row->Version."' AND moduleid_fk = '" . $module['moduleid'] . "' AND Tipo = 'bug'")->row("cont");
                                                    $bugs_visible = $this->db->query("SELECT COUNT(Tipo) as cont FROM changelog WHERE Version='".$row->Version."' AND moduleid_fk = '" . $module['moduleid'] . "' AND visible = 1 AND Tipo = 'bug'")->row("cont");
                                                    if(($bugs_total > 0 && $bugs_visible > 0) || (has_permission('utilities', '', 'edit') && $bugs_total > 0)){?>
                                                    <div id="heading-<?php echo $module['moduleid'] . '-' . str_replace(".","-",$row->Version); ?>-bug-fixes" class="panel-heading">
                                                        <h4 class="panel-title"><a href="#<?php echo $module['moduleid'] . '-' . str_replace(".","-",$row->Version); ?>-bug-fixes" data-toggle="collapse" data-parent="#accordion" class="collapsed"> Correção de Problemas </a></h4>
                                                    </div>
                                                    <div id="<?php echo $module['moduleid'] . '-' . str_replace(".","-",$row->Version); ?>-bug-fixes" class="panel-collapse collapse" style="height: 0px;">
                                                        <div class="panel-body">
                                                            <ul>
                                                              <?php
                                                                    foreach ($alteracoes as $alt)
                                                                    {

                                                                        if($alt->visible == 0 && !has_permission('utilities', '', 'edit'))
                                                                            continue;
                                                                        $rel_type = $alt->rel_type;
                                                                        $rel_id = $alt->rel_id;
                                                                        if($alt->Tipo == "bug")
                                                                        {
                                                                            echo "<li><i class='fa fa-info-circle' data-toggle='tooltip' data-title='".$alt->Autor."' data-original-title='".$alt->Autor."' title='".$alt->Autor."'></i>&nbsp;";
                                                                            if ($rel_type == 'external_link') {
                                                                                echo "<a target='_blank' href='" . $alt->slug . "'>";
                                                                            }else if($rel_type != '' && $rel_id != 0){
                                                                                $rel_data = get_relation_data($rel_type,$rel_id);
                                                                                $rel_val = get_relation_values($rel_data,$rel_type);
                                                                                echo "<a target='_blank' href='".$rel_val['link']."'>";
                                                                            }elseif(isset($alt->slug) && ($alt->slug != null)) {
                                                                                echo "<a target='_blank' href='" . APP_BASE_URL . "admin/knowledge_base/view/" . $alt->slug . "'>";
                                                                            }

                                                                            echo $alt->Titulo;
                                                                            if(isset($alt->slug) || ($rel_type != '' && $rel_id != 0))
                                                                                echo "</a>";

                                                                            $date = date_create($alt->Data);
                                                                            echo " - ".date_format($date, 'd/m/Y');
                                                                            if($alt->visible == 0)
                                                                                echo '  &#10153;  <span class="badge badge-warning mbot5" style="background-color: yellow; color: #0a0a0a;">DESATIVADO</span>';

                                                                            echo "</li>";
                                                                        }

//                                                                        if ($alt->Tipo == "bug") {
//                                                                            echo "<li style='list-style-type: disc;'><i class='fa fa-info-circle' data-toggle='tooltip' data-title='".$alt->Autor."' data-original-title='".$alt->Autor."' title='".$alt->Autor."'></i>&nbsp;".$alt->Titulo."</li>";
//                                                                            }
                                                                    }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <?php }?>
                                                </div>
                                            </div>
                                            <?php }}}?>
                                        </div>
                                    </div>
                                </div>
                            <?php }?>
                        </div>
                    </div>
<!--                    <label for="date_from" class="control-label">Data de Início</label>-->
                </div>
<!--            </div>-->
        </div>
    </div>
</div>

<!--Inicio-Modal-->
<div class="modal fade" id="new" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="conttent-modal-replies-title">Registrar nova mudança</div></h4>
            </div>
            <?php
            $rel_type = '';
            $rel_id = '';
            ?>
            <div class="modal-body" id="conttent-modal-replies">
                <?php echo form_open(APP_BASE_URL."admin/utilities/changelog", array("id"=>"changelogform")); ?>
                <?php echo render_select('moduleid_fk',$modules, array('moduleid','name'),'Módulo'); ?>
                <?php echo render_input('Autor','Autor', get_staff_full_name()); ?>
<!--                --><?php //$hnow = \Carbon\Carbon::now();?>
                <?php echo render_date_input('Data','Data', date('d/m/Y')); ?>
                <?php echo render_input('Titulo','Titulo'); ?>
<!--                --><?php //echo render_input('Tipo','Tipo'); ?>
                <div class="form-group">
                    <label for="Tipo" class="control-label">Tipo</label>
                    <select id="Tipo" name="Tipo" class="selectpicker" data-width="100%" data-none-selected-text="Nada selecionado" data-live-search="true" tabindex="-98">
                        <option value="new">Implementação</option>
                        <option value="bug">Correção</option>
                        <option value="note">Notas</option>
                    </select>
                </div>
                <?php echo render_input('Version', 'Versão'); ?>
                <?php if(PAINEL == QUANTUM){ ?>
                <?php echo render_select('slug',$knowledgebase,array('slug','subject'),'Base de Conhecimento'); ?>
                <?php }else{ ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6" style="float: left; padding-left: unset">
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
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--Fim-Modal-->
<div class="modal fade" id="add-module" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="conttent-modal-replies-title">Cadastrar Módulo</div></h4>
            </div>
            <div class="modal-body" id="conttent-modal-replies">
                <?php echo form_open(admin_url("utilities/changelog/cadas_module"), array("id"=>"changelogform")); ?>
                <?php echo render_input('name','Nome'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--Fim-Modal-->
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
    });

    var _rel_id = $('#rel_id'),
        _rel_type = $('#rel_type'),
        _rel_id_wrapper = $('#rel_id_wrapper');

    function new_change()
    {
        $('#changelogform input')[2].value = "";
        $('#changelogform input')[3].value = "";
        $('#changelogform input')[4].value = "";
        // $('#changelogform input')[2].value = "";
        $('#new').modal('show');
    }


    function get_relation_value(rel_id, rel_type){
        var dado = {};
        dado.rel_id = rel_id;
        dado.rel_type = rel_type;
        $.get(admin_url + "changelog/get_relation_value/", dado).done(function(data) {
            $("#rel_id").html(data);
            $("#rel_id").selectpicker("refresh");
        });
    }

    // init_ajax_search_tickets_search();

    $('.rel_id_label').html(_rel_type.find('option:selected').text());
    _rel_type.on('change', function() {
        if($(this).val() === 'external_link') {
            if($('#rel_id').hasClass('ajax-search')){
                $('#rel_id').selectpicker('destroy').remove(); //destroi o elemento antigo
            }else{
                $('#rel_id').remove(); //destroi o elemento antigo
            }

            $('#rel_id_select').append('<input type="text" id="rel_id" name="rel_id" class="form-control" value="">'); //adiciona o novo elemento que no caso é o input

            $('.rel_id_label').html(_rel_type.find('option:selected').text()); // altera a label acima do elemento

            // if ($(this).val() != '') {
            //     _rel_id_wrapper.removeClass('hide');
            // } else {
            //     _rel_id_wrapper.addClass('hide');
            // }

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

            task_rel_select(); // inicia o live-search
        }
        if ($(this).val() != '') {
            _rel_id_wrapper.removeClass('hide');
        } else {
            _rel_id_wrapper.addClass('hide');
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


</script>
