<?php
/**
 * Created by PhpStorm.
 * User: Lucas
 * Date: 30/04/2018
 * Time: 14:44
 */

init_head();

?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s _buttons">
                    <div class="panel-body">
                        <?php if (has_permission_video('movies', 'create')) { ?>
                            <a class="btn btn-success" onclick="$('#add_equipment').modal('show'); return false;">Adicionar
                                equipamento</a>
                        <?php } ?>
                    </div>
                </div>
                <div class="panel_s _buttons">
                    <div class="panel-body">
                        <table id="table_id" class="table table-striped">
                            <thead>
                            <th>#</th>
                            <th>Nome</th>
                            </thead>
                            <tbody>
                            <?php foreach ($equipments as $equips) { ?>
                                <tr>
                                    <td>
                                        <?php echo $equips["id_equip_model"]; ?>
                                    </td>

                                    <td>
                                        <?php echo $equips["nome"]; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add_equipment" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('equipmens/add_equipments')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Adicionar equipamento</h4>
            </div>
            <div class="modal-body">
                <?php echo render_input('add_equipment', 'Adicionar equipamento', get_option("add_equipment"), "text"); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-success"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php

init_tail();

?>
<script>
    $(function () {
        initDataTableOffline("#table_id");
    });
</script>