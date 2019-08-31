<?php /**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 29/01/2018
 * Time: 14:23
 */

init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="col-md-12">
                            <a class="btn btn-info" href="<?php echo admin_url("utilities/supply");?>" style="margin: auto; vertical-align: middle">Abastecimentos</a>
                            <hr>
                            <h4>Filtrar por: </h4>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <?php echo render_date_input('date_from', 'Data de Início', _d('')); ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_date_input('date_to', 'Data Final', _d('')); ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_select('staff',$staffs,array('staffid','staffname'),'Funcionário'); ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_select('vehicle',$vehicles,array('vehicleid','descricao'),'Veículo'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                                    <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <option value="vendas"><?php echo _l('Vendas'); ?>
                                        </option>
                                        <option value="project"><?php echo _l('project'); ?></option>
                                        <option value="invoice">
                                            <?php echo _l('invoice'); ?>
                                        </option>
                                        <option value="customer">
                                            <?php echo _l('client'); ?>
                                        </option>
                                        <option value="estimate">
                                            <?php echo _l('estimate'); ?>
                                        </option>
                                        <option value="contract">
                                            <?php echo _l('contract'); ?>
                                        </option>
                                        <option value="ticket">
                                            <?php echo _l('ticket'); ?>
                                        </option>
                                        <option value="expense">
                                            <?php echo _l('expense'); ?>
                                        </option>
                                        <option value="lead">
                                            <?php echo _l('lead'); ?>
                                        </option>
                                        <option value="proposal">
                                            <?php echo _l('proposal'); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group hide" id="rel_id_wrapper">
                                    <label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
                                    <div id="rel_id_select">
                                        <select name="rel_id" id="rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <option value="" selected></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" style="text-align: center; padding: 27px">
                                    <button class="btn btn-success" onclick="filter()"
                                            style="margin: auto; vertical-align: middle"><?php echo _l('Filtrar'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                            render_datatable(array(
                                    array(
                                        'name' => 'ID',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Data',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Funcionário',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Relacionado ao',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Motivo',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Local',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Veículo',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold; min-width: 100px;'),
                                    ),
                                    array(
                                        'name' => 'Km Inicial',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Km Final',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Hora Saída',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Hora Chegada',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Distancia',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                                    array(
                                        'name' => 'Tempo',
                                        'th_attrs' => array('style' => 'font-size: 15px; font-weight: bold'),
                                    ),
                            ),'fleet');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-fleet', window.location.href+"?type=report");
        $('.table-fleet').DataTable().order()[0] = [0, "DESC"];
        $('.table-fleet').DataTable().ajax.reload();
    });

    function filter()
    {
        var comando = "?type=report";

        if($("#staff").val() != "" && $("#staff").val() != null)
            comando += "&staff=" + $("#staff").val();
        if($("#vehicle").val() != "" && $("#vehicle").val() != null)
            comando += "&vehicle="+$("#vehicle").val();
        if($("#rel_type").val() != "" && $("#rel_type").val() != null)
            comando += "&rel_type="+$("#rel_type").val();
        if($("#rel_id").val() != "" && $("#rel_id").val() != null)
            comando += "&rel_id="+$("#rel_id").val();
        if(($("#date_from").val() != "" && $("#date_from").val() != null) || ($("#date_to").val() != "" && $("#date_to").val() != null)) {
            comando += "&date=true";
            if($("#date_from").val() != "" && $("#date_from").val() != null)
                comando += "&date_from=" + $("#date_from").val();
            else
                comando += "&date_from=false";
            if ($("#date_to").val() != "" && $("#date_to").val() != null)
                comando += "&date_to=" + $("#date_to").val();
            else
                comando += "&date_to=false";
        }


        $('.table-fleet').DataTable().context[0].ajax.url = window.location.href+comando;
        $('.table-fleet').DataTable().ajax.reload();
    }

    //Relações---------------------
    var _rel_id = $('#rel_id'),
        _rel_type = $('#rel_type'),
        _rel_id_wrapper = $('#rel_id_wrapper');

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
