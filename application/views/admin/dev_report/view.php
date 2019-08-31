<?php
/**
 * Created by PhpStorm.
 * User: matheus
 * Date: 13/07/2018
 * Time: 15:16
 */

init_head(); ?>
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">-->
<style>

    td > div.center { text-align: center; }

    /*.card {*/
        /*box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);*/
        /*transition: 0.3s;*/
        /*border-radius: 5px; !* 5px rounded corners *!*/
        /*!*max-width: 18rem;*!*/
    /*}*/

    /*.fa-clock:before {*/
        /*content: "\f017";*/
    /*}*/

    .fa-stopwatch:before {
        content: "\f2f2";
    }

    /*.text-dark {*/
        /*color: #343a40!important;*/
    /*}*/

    .card-body {
        -webkit-box-flex: 1;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        padding: 1.25rem;
        max-height: 150px;
        min-height: 150px;
    }

    .card-header:first-child {
        border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
    }

    .card-text:last-child {
        margin-bottom: 0;
    }

    .card-title {
        margin-bottom: .75rem;
    }

    .card-header {
        padding: .75rem 1.25rem;
        margin-bottom: 0;
        background-color: rgba(0,0,0,.03);
        border-bottom: 1px solid rgba(0,0,0,.125);
        font-size: larger;
    }

    .border-dark {
        border-color: #343a40!important;
    }

    .card {
        position: relative;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: .55rem;
        cursor: pointer;
    }


    /* On mouse-over, add a deeper shadow */
    .card:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    /*!* Add some padding inside the card container *!*/
    /*.container {*/
        /*padding: 2px 16px;*/
    /*}*/

    /*!* Add rounded corners to the top left and the top right corner of the image *!*/
    /*img {*/
        /*border-radius: 5px 5px 0 0;*/
    /*}*/

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
<?php
const DEV_ESPERA = 26;
const DEV_ATENDIDO = 32;
const DEV_PRODUCAO = 33;
const DEV_TESTE = 24;
?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <p>Dev Report</p>
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
<!--                                <div class="form-group col-md-3">-->
<!--                                    --><?php //echo render_select('status', $status_dev, array('ticketstatusid', 'name'), 'Filtrar por Status', '',  array(), array(), '', '', true); ?>
<!--                                </div>-->
                                <div class="col-md-3" style="text-align: center; padding: 27px">
                                    <button class="btn btn-success" onclick="filter()"
                                            style="margin: auto; vertical-align: middle"><?php echo _l('Filtrar'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="panel-body">-->
<!--                        <div class="col-md-12 mbot30">-->
<!--                            <div class="row mtop45">-->
<!--                                <div class="col-md-6">-->
<!--                                    <div class="col-md-8 col-md-offset-3">-->
<!--                                        --><?php //$indice = array_search(32, array_column($status_dev, 'ticketstatusid')); ?>
<!--                                        <div class="card border-dark" href="#" onclick="open_table(--><?php //echo DEV_ESPERA; ?><!--//, <?php //echo DEV_ATENDIDO; ?><!--, this); return false;">
                    <!--                                           <div class="card-header">Tempo médio de DEV - Espera para DEV - Concluído</div>
//                                            <div class="card-body text-dark" style="color: <?php //echo $status_dev[$indice]['statuscolor']; ?><!--">-->
<!--                                                <h3 class="card-title mtop10"><i class="fa fa-hourglass mright20"></i>--><?php //echo $espera_to_concluido; ?><!--</h3>-->
<!--                                                <p class="card-text">Cálculo do tempo médio das solicitações desde o status Em - Espera até o status DEV - Atendido.-->
<!--                                                    Não levando em consideração status intermediários durante esse processo.</p>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-md-6">-->
<!--                                    <div class="col-md-8">-->
<!--                                        --><?php //$indice = array_search(26, array_column($status_dev, 'ticketstatusid')); ?>
<!--                                        <div class="card border-dark" href="#" onclick="open_table(--><?php //echo DEV_ESPERA; ?><!--//, <?php //echo DEV_PRODUCAO; ?><!--, this); return false;">
                        <!--                                            <div class="card-header">Tempo médio de DEV - Espera para DEV - Produção</div>
//                                            <div class="card-body text-dark" style="color: <?php //echo $status_dev[$indice]['statuscolor']; ?><!--">-->
<!--                                                <h3 class="card-title mtop10"><i class="fa fa-clock-o mright20"></i>--><?php //echo $espera_to_producao; ?><!--</h3>-->
<!--                                                <p class="card-text">Cálculo do tempo médio das solicitações desde o status Em - Espera até o status Em - Produção.-->
<!--                                                    Não levando em consideração status intermediários durante esse processo.</p>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="row mtop30">-->
<!--                                <div class="col-md-6">-->
<!--                                    <div class="col-md-8 col-md-offset-3">-->
<!--                                        --><?php //$indice = array_search(33, array_column($status_dev, 'ticketstatusid')); ?>
<!--                                        <div class="card border-dark" href="#" onclick='open_table(--><?php //echo DEV_PRODUCAO; ?><!--//, <?php //echo $aux_producao; ?>
                    <!--, this); return false;'>
                            <!--                                            <div class="card-header">Tempo médio em DEV - Produção</div>
//                                            <div class="card-body text-dark" style="color: <?php //echo $status_dev[$indice]['statuscolor']; ?><!--">-->
<!--                                                <h3 class="card-title mtop10"><i class="fa fa-hourglass-half mright20"></i>--><?php //echo $em_producao; ?><!--</h3>-->
<!--                                                <p class="card-text">Tempo médio das solicitações em status de Produção.</p>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-md-6">-->
<!--                                    <div class="col-md-8">-->
<!--                                        --><?php //$indice = array_search(24, array_column($status_dev, 'ticketstatusid')); ?>
<!--                                        <div class="card border-dark" href="#" onclick='open_table(--><?php //echo DEV_TESTE; ?><!--//, <?php //echo $aux_teste; ?>
                    <!--, this); return false;'>
                            <!--                                            <div class="card-header">Tempo médio em DEV - Teste</div>
//                                            <div class="card-body text-dark" style="color: green">
//                                                <h3 class="card-title mtop10"><i class="fa fa-hourglass-start mright20"></i><?php //echo $em_teste; ?><!--</h3>-->
<!--                                                <p class="card-text">Tempo médio das solicitações em status de Teste.</p>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="clearfix"></div>
                            <?php render_datatable(array(
                                //_l('client_company'),
                                array(
                                    'name' => 'Status',
                                    'th_attrs' => array('class' => 'col-md-4', 'style' => 'font-size: 15px; font-weight: bold'),
                                ),
                                //_l('options'),
                                array(
                                    'name' => 'Tempo',
                                    'th_attrs' => array('class' => 'col-md-8', 'style' => 'font-size: 15px; font-weight: bold'),
                                ),
                                array(
                                    'name' => 'Listar tickets',
                                    'th_attrs' => array('class' => 'col-md-2', 'style' => 'font-size: 15px; font-weight: bold'),
                                )
                            ), 'dev', array('info')); ?>

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
        initDataTable('.table-dev', window.location.href, [], [], '', [], []);
        // initDataTableOffline('.table-dev', [2, 'DESC']);
    });
    
    function filter() {
        var data = {};
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();

        if(data.from != '' && data.to != '') {
            var table = $('#DataTables_Table_0').DataTable();
            table.destroy();
            var serverParams = {};
            serverParams['from'] = '[name="date_from"]';
            serverParams['to'] = '[name="date_to"]';
            initDataTable('.table-dev', window.location.href, [], [], serverParams, [], []);
        }else{
            alert('Por favor, informar as datas!');
        }

        // console.log(data);
    }
    
    function open_table(status_from, status_to, name) {

        // var name = $(element).find('div:first').html();
        var jane = document.getElementById("conttent-modal-replies");
        var janeid = document.getElementById("conttent-modal-replies-title");
        //$('#loader').modal('show');
        janeid.innerHTML = name;

        var data = {};
        data.status_from = status_from;
        data.status_to = status_to;
        data.from = $('[name="date_from"]').val();
        data.to = $('[name="date_to"]').val();

        $.post(admin_url + "dev_report/open_table", data).done(function (response) {
            //$('#loader').modal('hide');
            jane.innerHTML = response;
            initDataTableOffline("#table-resps", [2, 'DESC']);
            $('#replies').modal('show');
        });
    }
</script>

