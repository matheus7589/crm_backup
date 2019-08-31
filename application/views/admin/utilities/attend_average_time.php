<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 08/11/2017
 * Time: 08:43
 */

init_head(); ?>
<style>
    td > div.center { text-align: center; }

    .centered-modal.in {
           display: flex !important;
    }

    .centered-modal .modal-dialog {
          margin: auto;
    }

    .loader {
        border: 16px solid #f3f3f3; /* Light grey */
        border-top: 16px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    #modal-loader {
        width: 0 !important;
    }

</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3>
                            <b>
                                <?php echo "Tempo Médio de Atendimentos por Técnico"; ?>
                                <?php //var_dump($await_average['cont']);
//                                $tempo = date("H\h:i\m", mktime(0, ($await_average['diff_data']/$await_average['cont'])));
                                $tempoAtendimento = 0;
                                $tempo = 0;
                                if (count($await_average['cont_tickets']) > 0) {
                                    $tempo = date("H\h:i\m", mktime(0, ($await_average['diff_data'] / count($await_average['cont_tickets']))));
                                }
                                if (count($attend_average['cont_tickets']) > 0) {
                                    $tempoAtendimento = date("H\h:i\m", mktime(0, ($attend_average['diff_data'] / count($attend_average['cont_tickets']))));
                                }
                                ?>
                                <div class="pull-right">
                                    <div class="row">
                                        <div class="col-md-12" style="display: flex; justify-content: space-between;">

                                            <?php echo "Tempo de Espera Médio: " . '<a data-toggle="tooltip" data-title="Listar Piores Médias" data-placement="bottom" class="btn " onclick="open_bad_tickets(); " style="font-size: 25px; color: inherit">
                                                <span id="tempo" class = "label label-success" style="font-size: 25px; color: inherit;">'
                                                . $tempo . '</span></a>'; ?>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 15px;">
                                        <div class="col-md-12" >

                                            <?php echo "Tempo de Atendimento Médio: " . '<a data-toggle="tooltip" data-title="Listar Piores Médias" data-placement="bottom" class="btn " onclick="open_bad_attend_tickets(); " style="font-size: 25px; color: inherit">
                                                <span id="tempo-atendimento" class = "label label-success" style="font-size: 25px; color: inherit;">'
                                                . $tempoAtendimento . '</span></a>'; ?>
                                        </div>
                                    </div>

                                </div>
                            </b>
                        </h3>
                        <div class="mtop15">
                            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("relatorio-atendimentos.show") ?>'>
                                Atendimentos
                            </a>
                            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("numero-atendimentos.show") ?>'>
                                Atendimentos por Cliente
                            </a>
                            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("atendimento-por-tecnico.show") ?>'>
                                Atendimentos por Técnico
                            </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php echo render_date_input('date_from', 'Data de Início', _d($from)); ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_date_input('date_to', 'Data de Fim', _d($to)); ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <?php echo render_select('staff', $staff, array('staffid', 'firstname'), 'Técnico', '',  array(), array(), '', '', true); ?>
                                </div>
                                <div class="col-md-3" style="text-align: center; padding: 27px">
                                    <button class="btn btn-success" onclick="filter()"
                                            style="margin: auto; vertical-align: middle"><?php echo _l('Filtrar'); ?></button>
                                </div>
                            </div>
                            <?php //echo form_close(); ?>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="clearfix"></div>
                            <?php render_datatable(array(
                                //_l('client_company'),
                                array(
                                    'name' => 'Técnico',
                                    'th_attrs' => array('class' => 'col-md-5', 'style' => 'font-size: 15px; font-weight: bold'),
                                ),
                                //_l('options'),
                                array(
                                    'name' => 'Média por tícket',
                                    'th_attrs' => array('class' => 'col-md-6', 'style' => 'font-size: 15px; font-weight: bold'),
                                ),
                                array(
                                    'name' => 'Listar tickets',
                                    'th_attrs' => array('class' => 'col-md-1', 'style' => 'font-size: 15px; font-weight: bold'),
                                )
                            ), 'attend', array('info')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Inicio-Modal-->
<div class="modal fade" id="lista" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="content-modal-lista-title"></div></h4>
            </div>
            <div class="modal-body" id="content-modal-lista">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--Fim-Modal-->


<!--Inicio-Modal-->
<div class="modal fade" id="replies" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <div id="conttent-modal-replies-title"></div>
                </h4>
            </div>
            <div class="modal-body" id="conttent-modal-replies">
                <?php
                echo render_datatable(array(
                    array(
                        'name' => '#Ticket',
                        'th_attrs' => array('class' => 'col-md-2', 'style' => 'font-size: 15px; font-weight: bold'),
                    ),
                    array(
                        'name' => 'Informações',
                        'th_attrs' => array('class' => 'col-md-6', 'style' => 'font-size: 15px; font-weight: bold'),
                    ),
                    array(
                        'name' => 'Resposta',
                        'th_attrs' => array('class' => 'col-md-4', 'style' => 'font-size: 15px; font-weight: bold'),
                    ),
                ), 'tbl', array('info'));
                ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--Fim-Modal-->

<!--Inicio-Modal-->
<div class="modal fade centered-modal" id="loader" tabindex="-1" role="dialog">
        <div id="modal-loader" class="modal-dialog" role="document">
            <div class="loader"></div>
            </div>
        </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--Fim-Modal-->




<?php init_tail(); ?>
<script>

    $(function () {


        initDataTable('.table-attend', window.location.href, [], [], '', [], []);

    });

    function open_bad_tickets(){

        const OPEN = 1;
        var data = {};
        //data.bad_tickets = '<?php //echo $await_average["bad_tickets"]; ?>//';
        //data.bad_times = '<?php //echo $await_average["bad_times"]; ?>//';
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();
        data.status = OPEN;
        $.get(admin_url + "attend_average_time/list_bad_tickets", data).done(function (data ) {

            $('#content-modal-lista-title').text('Tickets com Maior Tempo de Espera');
            $('#content-modal-lista').html(data);
            $('#lista').modal('show');


        }, "json");

    }

    function open_bad_attend_tickets(){

        const ONHOLD = 2;
        var data = {};
        //data.bad_tickets = '<?php //echo $await_average["bad_tickets"]; ?>//';
        //data.bad_times = '<?php //echo $await_average["bad_times"]; ?>//';
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();
        data.status = ONHOLD;
        $.get(admin_url + "attend_average_time/list_bad_tickets", data).done(function (data ) {

            $('#content-modal-lista-title').text('Tickets com Maior Tempo de Atendimento');
            $('#content-modal-lista').html(data);
            $('#lista').modal('show');


        }, "json");

    }


    function filter() {
        var table = $('#DataTables_Table_0').DataTable();
        table.destroy();
        var serverParams = {};
        serverParams['date_from'] = '[name="date_from"]';
        serverParams['date_to'] = '[name="date_to"]';
        serverParams['attend'] = '[name="staff"]';
        initDataTable('.table-attend', window.location.href, [], [], serverParams, [], []);

        var data = {};
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();
        $.get(admin_url + "attend_average_time/average_time/", data).done(function (response ) {
            response = JSON.parse(response);
            $('#tempo').text(response.resultado);
        }, "json");

        getAttendTime(data);
    }

    function getAttendTime(data) {
        $.get(admin_url + "attend_average_time/average_time_attend/", data).done(function (response ) {
            response = JSON.parse(response);
            $('#tempo-atendimento').text(response.resultado);
        }, "json");
    }

    function list(id) {
        var jane = document.getElementById("conttent-modal-replies");
        var janeid = document.getElementById("conttent-modal-replies-title");
        // jane.innerHTML = "Carregando";
        // janeid.innerHTML = "<a href='" + admin_url + "tickets/ticket/" + id + "'># " + id + "</a>";
        // $('#loader').modal('show');
        janeid.innerHTML = "Tickets do Colaborador";

        var data = {};
        data.ids = id;
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();

        $.post(admin_url + "utilities/attend_average_time/show_formated", data).done(function (response) {
            // $('#loader').modal('hide');
            jane.innerHTML = response;
            initDataTableOffline("#table-resps", [2, 'DESC']);
            $('#replies').modal('show');
        });
    }


</script>
