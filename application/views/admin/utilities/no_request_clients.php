<?php
/**
 * Created by PhpStorm.
 * User: matheus.machado
 * Date: 26/03/2018
 * Time: 10:58
 */

init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3>
                            Clientes Que Não Solicitaram Suporte
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php echo render_input('dias', 'Filtro em Dias', ''); ?>
                                </div>
                                <div class="col-md-3" style="text-align: center; padding: 27px">
                                    <button class="btn btn-success" onclick="filter()"
                                            style="margin: auto; vertical-align: middle"><?php echo _l('Filtrar'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="clearfix"></div>
                            <?php render_datatable(array(
                                array(
                                    'name' => 'Id Cliente',
                                    'th_attrs' => array('class' => 'col-md-2', 'style' => 'font-size: 15px; font-weight: bold'),
                                ),
                                array(
                                    'name' => 'Cliente',
                                    'th_attrs' => array('class' => 'col-md-3', 'style' => 'font-size: 15px; font-weight: bold'),
                                ),
                                array(
                                    'name' => 'Telefone',
                                    'th_attrs' => array('class' => 'col-md-3', 'style' => 'font-size: 15px; font-weight: bold'),
                                ),
                                array(
                                    'name' => 'Data Contato',
                                    'th_attrs' => array('class' => 'col-md-2', 'style' => 'font-size: 15px; font-weight: bold'),
                                ),
                                array(
                                    'name' => 'Dias sem Contato',
                                    'th_attrs' => array('class' => 'col-md-2', 'style' => 'font-size: 15px; font-weight: bold'),
                                ),
                            ), 'norequest', array('info')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>

    $(function () {

        initDataTable('.table-norequest', window.location.href, [2], [], '', [], []);
        init_ajax_search('contact', '#attend.ajax-search', {tickets_contacts: true});

    });
    
    function filter() {
        var table = $('#DataTables_Table_0').DataTable();
        table.destroy();
        var serverParams = {};
        serverParams['dias'] = '[name="dias"]';

        initDataTable('.table-norequest', window.location.href, [2], [], serverParams, [], []);
    }

</script>
