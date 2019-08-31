<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 19/02/2018
 * Time: 18:12
 */

init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="col-md-12">
                            <a class="btn btn-info" href="<?php echo admin_url("utilities/fleet_report");?>" style="margin: auto; vertical-align: middle">Frota</a>
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
                                    <?php echo render_select('vehicle',$vehicles,array('vehicleid','descricao'),'Veículo'); ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_select('posto',$postos,array('posto','posto'),'Posto'); ?>
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
                        <h3>
                            Média do desempenho =
                            <span class="label label-<?php echo $desempenho->label?>" style="font-size: 100%; background: inherit;" id="desempenho">
                                <?php echo $desempenho->km;?>
                            </span>
                        </h3>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                            echo render_datatable(array(
                                '#',
                                'Veículo',
                                'Posto',
                                'Litros',
                                'Valor Total',
                                'Preço/Litro',
                                'Data',
                                'Km',
                            ),'supply')
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="alerta_supply" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="conttent-modal-replies-title">Atenção</div></h4>
            </div>
            <div class="modal-body" id="conttent-modal-replies">
                <h3 class="text-default">
                    O desempenho apresentado pelos abastecimentos de um determinado posto pode não ser preciso.
                    </br>
                    </br>
                    Para ter melhor precisão no desempenho por posto, abasteça pelo menos 3 vezes seguidas no mesmo posto e utilize o filtro de data para escolher o periodo do abastecimento.
                </h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail();?>
<script>
    function filter() {
        comando = "";
        if($("#vehicle").val() != "" && $("#vehicle").val() != null)
            comando += "&vehicle="+$("#vehicle").val();
        if($("#posto").val() != "" && $("#posto").val() != null)
            comando += "&posto="+$("#posto").val();
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

        $('.table-supply').DataTable().context[0].ajax.url = window.location.href+"?type=table"+comando;
        $('.table-supply').DataTable().order([6, 'asc']).ajax.reload();

        $.get( window.location.href+"?type=desempenho"+comando, function( data ) {
            data = JSON.parse(data);
            $("#desempenho").html(data.km);
            $("#desempenho")[0].className = "label label-"+data.label;
        });
    }
    $(function () {
        initDataTable(".table-supply",window.location.href+"?type=table");
        $('.table-supply').DataTable().order([6, 'asc']).draw();
    });

    $( "#posto" ).change(function() {
        $("#alerta_supply").modal("show");
    });
</script>
