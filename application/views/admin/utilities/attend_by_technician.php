<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 26/10/2017
 * Time: 16:13
 */

init_head(); ?>
<style>
    td{
        font-size: medium;
    }

    tr:nth-child(even){background-color: #f2f2f2}

</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3>
                            <b>
                                Número de Atendimentos por Técnico
                            </b>
                        </h3>
                        <div class="mtop15">
                            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("relatorio-atendimentos.show") ?>'>
                                Atendimentos
                            </a>
                            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("numero-atendimentos.show") ?>'>
                                Atendimentos por Cliente
                            </a>
                            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("atendimento-tempo-medio.show") ?>'>
                                Tempo de Atendimentos
                            </a>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row">
                                <?php echo form_open(APP_BASE_URL.'admin/utilities/attend_by_technician'); ?>
                                    <div class="col-md-3">
                                        <?php echo render_date_input('date_from', 'Data de Início', _d('')); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo render_date_input('date_to', 'Data de Fim', _d('')); ?>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <?php echo render_select('status', $status, array('ticketstatusid', 'name'), 'Status', '',  array(), array(), '', '', true); ?>
                                    </div>
                                    <div class="col-md-3" style="text-align: center; padding: 27px">
<!--                                        --><?php //echo form_submit('','Filtrar'); ?>
                                        <input type="submit" class="btn btn-success" style="margin: auto; vertical-align: middle" value="Filtrar">
                                    </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body">
                            <?php if(count($assigneds) > 0){ ?>
                                <div class="table-responsive col-md-12">
                                    <table class="table dt-table atendimentostec" id="attends">
                                        <thead>
                                            <th>Nome</th>
                                            <th>Número de atendimentos</th>
                                            <th>Opções</th>
                                        </thead>
                                        <tbody>
                                        <?php foreach($assigneds as $assigned){ ?>
                                            <tr>
                                                <td>
                                                    <?php echo $assigned['name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $assigned["numatt"]; ?>
                                                </td>
                                                <td>
                                                    <a class="btn btn-success" href="<?php echo APP_BASE_URL."admin/staff/member/".$assigned['staffid']; ?>" data-nome="<?php echo $assigned["name"]; ?>" style="margin: auto; vertical-align: middle">+</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <p class="no-margin">Nenhum atendimento</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        $("#attends").DataTable().order(1).draw();
    });
</script>