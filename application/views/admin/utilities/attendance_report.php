<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 18/10/2017
 * Time: 08:36
 */

init_head(); ?>
<style>
    td {
        font-size: medium;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2
    }
    .table>tbody>tr>td{
        padding: 2px;
        vertical-align: middle;
    }
</style>
<script>
    function openresp(ticketid) {
        var respatu = document.getElementById('resp' + ticketid);

        if (ultimo == respatu) {

            respatu.className = "hide respt";
            ultimo = "";
        }
        else {
            ultimo.className = "hide respt";
            respatu.className = "respt";
            ultimo = respatu;
        }
    }

    function surelato(id) {
        var jane = document.getElementById("conttent-modal-replies");
        var janeid = document.getElementById("conttent-modal-replies-title");
        // jane.innerHTML = "Carregando";
        janeid.innerHTML = "<a href='" + admin_url + "tickets/ticket/" + id + "'># " + id + "</a>";

        var data = {};
        data.ids = id;

        $.post(admin_url + "utilities/attendance_report/show_formated", data).done(function (response, textStatus) {
            jane.innerHTML = response;
            // initDataTableOffline("#tb1");
            // var serverParams = {};
            // var name = "ticket_" + id;
            // serverParams['ids'] = '[name="'+ name +'"]';
            // $('.table-tbl').DataTable().destroy();
            // initDataTable('.table-tbl', window.location.href + '/show_formated' , [], [], serverParams, '', []);
            initDataTableOffline("#table-resps");
            $('#replies').modal('show');
        });
    }
</script>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php $this->load->view('admin/includes/reports_top_filters'); ?>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php render_datatable(array(
                            //_l('client_company'),
                            array(
                                'name' => 'Empresa',
                                'th_attrs' => array('class' => 'col-md-5', 'style' => 'font-size: 15px; font-weight: bold'),
                            ),
                            //_l('options'),
                            array(
                                'name' => ' ',
                                'th_attrs' => array('class' => 'col-md-6', 'style' => 'font-size: 15px; font-weight: bold'),
                            ),
//                            array(
//                                'name' => 'ID do Usuário',
//                                'th_attrs' => array('class' => 'col-md-1', 'style' => 'font-size: 15px; font-weight: bold'),
//                            ),
                            array(
                                'name' => 'Opções',
                                'th_attrs' => array('class' => 'col-md-1', 'style' => 'font-size: 15px; font-weight: bold'),
                            ),
                        ), 'attend', array('info')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
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
                        'name' => 'Informações',
                        'th_attrs' => array('class' => 'col-md-5', 'style' => 'font-size: 15px; font-weight: bold'),
                    ),
                    array(
                        'name' => 'Resposta',
                        'th_attrs' => array('class' => 'col-md-6', 'style' => 'font-size: 15px; font-weight: bold'),
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

<?php init_tail(); ?>
<script>
    var userid = '';

    $(function () {

        init_ajax_search('contact', '#attend.ajax-search', {tickets_contacts: true});

        initDataTable('.table-attend', window.location.href, [], [], '', '', []);

    });

    function filter() {
        // $('.table-attend').empty();
        $('.table-attend').DataTable().destroy();
        var serverParams = {};
        serverParams['date_from'] = '[name="date_from"]';
        serverParams['date_to'] = '[name="date_to"]';
        serverParams['date_from_status'] = '[name="date_from_status"]';
        serverParams['date_to_status'] = '[name="date_to_status"]';
        serverParams['status'] = '[name="status"]';
        serverParams['attend'] = '[name="attend"]';
        initDataTable('.table-attend', window.location.href, [], [], serverParams, '', []);
    }
</script>