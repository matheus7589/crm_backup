<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 24/10/2017
 * Time: 10:30
 */

init_head(); ?>
<style>
    td{
        font-size: small !important;
    }

    .label{
        font-size: smaller !important;
    }

    .check{
        border-style: double;
        border-width: 1px;
        border-color: #97a8be;
        border-radius: 5%;
        padding-left: 50px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    tr:nth-child(even){background-color: #f2f2f2}

    .remodal-bg.with-red-theme.remodal-is-opening,
    .remodal-bg.with-red-theme.remodal-is-opened {
        filter: none;
    }

    .remodal-overlay.with-red-theme {
        background-color: #f44336;
    }

    .remodal.with-red-theme {
        background: #fff;
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
                                <?php echo "Número de Atendimentos por Cliente"; ?>
                            </b>
                        </h3>
                        <div class="mtop15">
                            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("relatorio-atendimentos.show") ?>'>
                                Atendimentos
                            </a>
                            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("atendimento-por-tecnico.show") ?>'>
                                Atendimentos por Técnico
                            </a>
                            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("atendimento-tempo-medio.show") ?>'>
                                Tempo de Atendimentos
                            </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <?php //echo form_open('relatorio-atendimentos.date_filter'); ?>
                            <div class="row">
                                <div class="col-md-2">
                                    <?php echo render_date_input('date_from', 'Data de Início', _d(substr($date_from, 0, 10))); ?>
                                </div>
                                <div class="col-md-2">
                                    <?php echo render_date_input('date_to', 'Data de Fim', _d(substr($date_to, 0, 10))); ?>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="attend"><?php echo _l('contact'); ?></label>
                                    <select name="attend" id="attend" class="ajax-search" data-width="100%"
                                            data-live-search="true"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    </select>
                                    <?php echo form_hidden('userid'); ?>
                                </div>

                                <div class="col-md-1 mleft15" style="padding: 10px;">
                                    <div class="checkbox mtop15"><input name="atendido" class="mtop15" type="checkbox" id="atendido"><label>Concluídos</label></div>
                                </div>
                                <div class="col-md-1" style="padding: 10px;">
                                    <div class="checkbox mtop15"><input name="total" class="mtop15 mleft15" type="checkbox" id="total" checked><label>Total</label></div>
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
                                    'name' => 'Empresa',
                                    'th_attrs' => array('class' => 'col-md-5', 'style' => 'font-weight: bold'),
                                ),
                                //_l('options'),
                                array(
                                    'name' => 'Telefone',
                                    'th_attrs' => array('class' => 'col-md-6', 'style' => 'font-weight: bold'),
                                ),
                                array(
                                    'name' => 'Número de Atendimentos',
                                    'th_attrs' => array('class' => 'col-md-1', 'style' => 'font-weight: bold'),
                                ),
                                array(
                                    'name' => 'Opções',
                                    'th_attrs' => array('class' => 'col-md-1', 'style' => 'font-weight: bold'),
                                ),
                            ), 'attend', array('info')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Inicio-Modal-->
<div class="modal fade" id="att" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="conttent-modal-replies-title"></div></h4>
            </div>
            <div class="modal-body" id="conttent-modal-att">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--Fim-Modal-->

<?php init_tail(); ?>
<script>

    $(function () {

        init_ajax_search('contact', '#attend.ajax-search', {tickets_contacts: true});
        $('#new_ticket_form').validate();

        initDataTable('.table-attend', window.location.href, [], [], '', [1, 'DESC'], []);

    });

    $('#atendido').change(function() {
        if ($(this).prop("checked")) {
            $("#total").prop("checked", false);
        } else {
            $("#total").prop("checked", true);
        }
    });

    $('#total').change(function() {
        if ($(this).prop("checked")) {
            $("#atendido").prop("checked", false);
        } else {
            $("#atendido").prop("checked", true);
        }
    });



    function filter() {


        if( $('#DataTables_Table_0').length )         // use this if you are using id to check
        {
            var table = $('#DataTables_Table_0').DataTable();
            table.destroy();
        }

        var serverParams = {};
        serverParams['date_from'] = '[name="date_from"]';
        serverParams['date_to'] = '[name="date_to"]';
        serverParams['attend'] = '[name="attend"]';
        if($("#atendido").prop("checked")){
            serverParams['atendido'] = '[name="atendido"]:checked';
        }
        if($("#total").prop("checked")){
            serverParams['total'] = '[name="total"]:checked';
        }

        initDataTable('#DataTables_Table_0', window.location.href, [], [], serverParams, [1, 'DESC'], []);

    }

    // $('#att').on('hidden.bs.modal', function () {
    //
    // });


    var ultimo = "";
    function openresp(ticketid)
    {
        var tr = $('#tr_' + ticketid);
        var row = $('#tb1').DataTable().row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            var data = {};
            data.ticketid = ticketid;

            $.get(admin_url + "utilities/number_attends/sub_table/", data).done(function(response)
            {
                // console.log(response);
                row.child(response).show();
                initDataTableOffline('#tb2', 'not-order');
                tr.addClass('shown');
            });


        }
    }

    function surelato(id)
    {
        var jane = document.getElementById("conttent-modal-att");
        jane.innerHTML = "Carregando";
        var janeid = document.getElementById("conttent-modal-replies-title");
        janeid.innerHTML = "<a href='"+admin_url+"clients/client/"+id+"'># "+id+"</a>";
        var data = {};
        data.ids = id;
        data.atendido = false;
        if($("#atendido").prop("checked")){
            data.atendido = true;
        }

        $.post(admin_url + "utilities/number_attends/get_ticket_data", data).done(function(data, textStatus)
        {
            jane.innerHTML = data;
            initDataTableOffline('#tb1');
            // initDataTableOffline('#tb2');
        }, "json");

        $('#att').modal('show');
    }


</script>