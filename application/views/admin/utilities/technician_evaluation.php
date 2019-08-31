<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 14/12/2017
 * Time: 16:36
 */

init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s _buttons">
                    <div class="panel-body">
                        <a href="<?php echo admin_url("utilities/attend_evaluation");?>" class="btn btn-info">
                            Avaliação dos Atendimentos
                        </a>
                        <hr>
                        <h3>
                            Atendente em Destaque:
                            <?php if(isset($featured)){ ?>
                                <span class = "label label-success" id="nomemelhor" style="font-size: 100%; background: inherit;">
                                    <?php echo $featured['firstname']; ?>
                                </span>
                            <?php } else{
                                echo "Erro ao recuperar dados";
                            }?>

<!--                            --><?php //var_dump($featured); ?>
                        </h3>
                        <hr>
                        <div class="col-md-3" style="padding-left: 0px;">
                            <?php echo render_date_input('date_from', 'Data de Início', _d('')); ?>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_date_input('date_to', 'Data Final', _d('')); ?>
                        </div>
                        <div class="col-md-3" style="text-align: center; padding: 27px">
                            <button class="btn btn-success" onclick="filter()" style="margin: auto; vertical-align: middle">Filtrar</button>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            array(
                                'name' => "#",
                                'th_attrs' => array('class' => 'col-md-1', 'style' => 'font-size: 15px; font-weight: bold'),
                            ),
                            array(
                                'name' => 'Atendente',
                                'th_attrs' => array('class' => 'col-md-3', 'style' => 'font-size: 15px; font-weight: bold'),
                            ),
                            array(
                                'name' => 'Média das Notas do Atendente',
                                'th_attrs' => array('class' => 'col-md-4', 'style' => 'font-size: 15px; font-weight: bold'),
                            ),
                            array(
                                'name' => 'Número de Atendimentos',
                                'th_attrs' => array('class' => 'col-md-2', 'style' => 'font-size: 15px; font-weight: bold'),
                            ),
                            array(
                                'name' => 'Detalhes',
                                'th_attrs' => array('class' => 'col-md-1', 'style' => 'font-size: 15px; font-weight: bold'),
                            ),
                        ), 'media'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="detalhes" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" <?php if(!is_mobile()){echo 'style="width: 70%;"';}?>>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="conttent-modal-replies-title">Detalhes das Avaliações</div></h4>
            </div>
            <div class="modal-body" id="conttent-modal-replies">
                <div class="row" id="conteudodetalhes">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>

<script>

    $(function () {

        initDataTable('.table-media', window.location.href, [], [], 'undefined', [], []);

    });

    function filter() {
        var comando = verify_filter();
        if(comando != false){
            $('.table-media').DataTable().context[0].ajax.url = admin_url+"utilities/technician_evaluation"+comando;
            $.get(window.location.href + comando + "&type=destaque", function (data) {
                data = JSON.parse(data);
                if(data != false)
                    $("#nomemelhor").html(data.firstname);
                else
                    $("#nomemelhor").html("Sem Registros");
                console.log(data.firstname)
            });
            $('.table-media').DataTable().ajax.reload();
        }
    }
    function detalhes(id) {
        var comando = verify_filter();
        if(comando == false)
            comando = "?";
        else
            comando += "&";
        $.get( admin_url+"utilities/technician_evaluation"+comando+"staff="+id, function(data){
            $("#conteudodetalhes").html(data);
            initDataTableOffline("#subconsulta-table");
            $("#detalhes").modal("show");
        });
    }

    function verify_filter() {
        var comando = "";
        if(($("#date_from").val() != "" && $("#date_from").val() != null) || ($("#date_to").val() != "" && $("#date_to").val() != null)) {
            comando += "?date=true";
            if ($("#date_from").val() != "" && $("#date_from").val() != null)
                comando += "&date_from=" + $("#date_from").val();
            else
                comando += "&date_from=false";
            if ($("#date_to").val() != "" && $("#date_to").val() != null)
                comando += "&date_to=" + $("#date_to").val();
            else
                comando += "&date_to=false";
            return comando;
        }
        else
            return false;
    }
</script>