<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 27/12/2017
 * Time: 13:31
 */
init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!--Reparar Permições-->
                        <div class="_buttons">
                            <a href="<?php echo admin_url("fleet"); ?>" class="btn btn-info"><i
                                        class="fa fa-angle-double-left fa-1x" aria-hidden="true"></i></a>
                            <?php if (has_permission('fleet', '', 'create')) { ?>
                                <a href="#" onClick="new_vehicle(); return false;" class="btn btn-info">
                                    Registrar Veículo
                                </a>
                            <?php } ?>
                        </div>
                        <br>
                        <hr class="hr-panel-heading">
                        <div class="row mbot15">
                            <div class="col-md-12">
                                <h3 class="text-success no-margin">Resumo dos Veículos</h3>
                            </div>
                            <div class="col-md-2 col-xs-6 border-right">
                                <a href="#" onclick="dt_custom_view('#DataTables_Table_0', 4, '1');">
                                    <h3 class="bold"><?php echo total_rows('tblfleetvehicles', ('active=1')); ?></h3>
                                    <span class="text-success">Veículos Ativos</span>
                                </a>
                            </div>
                            <div class="col-md-2 col-xs-6 border-right">
                                <a href="#" onclick="dt_custom_view('#DataTables_Table_0', 4, '0');">
                                    <h3 class="bold"><?php echo total_rows('tblfleetvehicles', ('active=0')); ?></h3>
                                    <span class="text-danger">Veículos Inativos</span>
                                </a>
                            </div>
                            <!--                            <div class="col-md-2 col-xs-6 border-right">-->
                            <!--                                <a href="#" onclick="dt_custom_view_partner('.table-partner', 2, '0');">-->
                            <!--                                    <h3 class="bold">0</h3>-->
                            <!--                                    <span class="text-warning">Veículos em Conserto</span>-->
                            <!--                                </a>-->
                            <!--                            </div>-->
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($vehicles) > 0) { ?>
                            <!--                            SELECT descricao,active,placa,tipo FROM `tblfleetvehicles`-->
                            <div class="table-responsive">
                                <!--                                table table-striped table-attend info no-footer dtr-inline dataTable-->
                                <table class="table dt-table table-striped">
                                    <thead>
                                    <th>#</th>
                                    <th>Descrição</th>
                                    <th>Tipo</th>
                                    <th>Placa</th>
                                    <th>Ativo</th>
                                    <th>Opções</th>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($vehicles as $vehicle) { ?>
                                        <tr>
                                            <td>
                                                <?php echo $vehicle['vehicleid']; ?>
                                            </td>
                                            <td>
                                                <?php echo $vehicle["descricao"]; ?>
                                            </td>
                                            <td>
                                                <?php echo $vehicle['tipo']; ?>
                                            </td>
                                            <td>
                                                <?php echo $vehicle['placa']; ?>
                                            </td>
                                            <td>
                                                <div class="onoffswitch">
                                                    <input type="checkbox"
                                                           data-switch-url="<?php echo admin_url('fleet/vehicles/change_vehicle_status'); ?>"
                                                           name="onoffswitch" class="onoffswitch-checkbox"
                                                           id="v_<?php echo $vehicle['vehicleid']; ?>"
                                                           data-id="<?php echo $vehicle['vehicleid']; ?>" <?php if (!has_permission('fleet', '', 'edit')) echo "disabled='true' "; ?> <?php if ($vehicle['active'] == 1) echo "checked"; ?>>
                                                    <label class="onoffswitch-label"
                                                           for="v_<?php echo $vehicle['vehicleid']; ?>"></label>
                                                </div>
                                                <div class="hide"><?php echo $vehicle['active']; ?></div>
                                            </td>
                                            <td>
                                                <?php if (has_permission('fleet', '', 'edit')) { ?>
                                                    <a href="<?php echo APP_BASE_URL . "admin/fleet/vehicles/" . $vehicle['vehicleid']; ?>"
                                                       class="btn btn-default btn-icon">
                                                        <i class="fa fa-pencil-square-o"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if (has_permission('fleet', '', 'create')) { ?>
                                                    <a class="btn btn-info btn-icon"
                                                       onclick="supply(<?php echo $vehicle['vehicleid']; ?>); return false;">
                                                        <i class="fa fa-fire"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if (has_permission('fleet', '', 'delete')) { ?>
                                                    <a class="btn btn-danger btn-icon"
                                                       onclick="delet(<?php echo $vehicle['vehicleid']; ?>); return false;">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="no-margin">Nenhum veículo cadastrado</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (has_permission('fleet', '', 'create')) { ?>
    <!--Inicio-Modal-->
    <div class="modal fade" id="new-vehicles" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <div id="conttent-modal-replies-title">Cadastrar Veiculo</div>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php echo form_open(admin_url('fleet/vehicles/add_vehicle'), array("id" => "form-new-vehicles")) ?>
                        <div class="col-md-6">
                            <?php echo render_input('vehicleid', 'Código do Veículo', '', '', array("disabled" => "true")); ?>
                            <?php echo render_input('finalplaca', 'Final Placa <span class="bold text-danger">*</span>', '', '', array("required" => "true", "maxlength" => "4")); ?>
                            <?php echo render_input('medelo', 'Modelo <span class="bold text-danger">*</span>', '', '', array("required" => "true")); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('placa', 'Placa <span class="bold text-danger">*</span>', '', '', array("required" => "true", "maxlength" => "8")); ?>
                            <?php echo render_select('marca', array(array("tipo" => "AGRALE"),
                                array("tipo" => "ALFA ROMEO"),
                                array("tipo" => "AM GENERAL"),
                                array("tipo" => "ASIA"),
                                array("tipo" => "ASTON MARTIN"),
                                array("tipo" => "AUDI"),
                                array("tipo" => "BENTLEY"),
                                array("tipo" => "BMW"),
                                array("tipo" => "CHANA"),
                                array("tipo" => "CHERY"),
                                array("tipo" => "CHEVROLET"),
                                array("tipo" => "CHRYSLER"),
                                array("tipo" => "CITROEN"),
                                array("tipo" => "CROSS LANDER"),
                                array("tipo" => "DAEWOO"),
                                array("tipo" => "DAIHATSU"),
                                array("tipo" => "DODGE"),
                                array("tipo" => "DS"),
                                array("tipo" => "EFFA HAFEI"),
                                array("tipo" => "FERRARI"),
                                array("tipo" => "FIAT"),
                                array("tipo" => "FORD"),
                                array("tipo" => "GEELY"),
                                array("tipo" => "HAFEI"),
                                array("tipo" => "HONDA"),
                                array("tipo" => "HYUNDAI"),
                                array("tipo" => "IVECO"),
                                array("tipo" => "JAC"),
                                array("tipo" => "JAGUAR"),
                                array("tipo" => "JEEP"),
                                array("tipo" => "JINBEI"),
                                array("tipo" => "JPX"),
                                array("tipo" => "KIA"),
                                array("tipo" => "LADA"),
                                array("tipo" => "LAMBORGHINI"),
                                array("tipo" => "LAND ROVER"),
                                array("tipo" => "LEXUS"),
                                array("tipo" => "LIFAN"),
                                array("tipo" => "LOTUS"),
                                array("tipo" => "MAHINDRA"),
                                array("tipo" => "MASERATI"),
                                array("tipo" => "MAZDA"),
                                array("tipo" => "MERCEDES"),
                                array("tipo" => "MINI"),
                                array("tipo" => "MITSUBISHI"),
                                array("tipo" => "NISSAN"),
                                array("tipo" => "PAGANI"),
                                array("tipo" => "PEUGEOT"),
                                array("tipo" => "PORSCHE"),
                                array("tipo" => "RAM"),
                                array("tipo" => "RELY"),
                                array("tipo" => "RENAULT"),
                                array("tipo" => "ROLLS ROYCE"),
                                array("tipo" => "SANTANA"),
                                array("tipo" => "SEAT"),
                                array("tipo" => "SHINERAY"),
                                array("tipo" => "SMART"),
                                array("tipo" => "SPYKER"),
                                array("tipo" => "SSANGYONG"),
                                array("tipo" => "SUBARU"),
                                array("tipo" => "SUZUKI"),
                                array("tipo" => "TAC"),
                                array("tipo" => "TOYOTA"),
                                array("tipo" => "TROLLER"),
                                array("tipo" => "VOLKSWAGEN"),
                                array("tipo" => "VOLVO")), array("tipo", "tipo"), 'Marca'); ?>
                            <?php echo render_select('tipo', array(
                                array("tipo" => "Automóvel"),
                                array("tipo" => "Bicicleta"),
                                array("tipo" => "Bonde"),
                                array("tipo" => "Caminhonete"),
                                array("tipo" => "Caminhão"),
                                array("tipo" => "Camioneta"),
                                array("tipo" => "Carroça"),
                                array("tipo" => "Carro de mão"),
                                array("tipo" => "Charrete"),
                                array("tipo" => "Ciclomotor"),
                                array("tipo" => "Microônibus"),
                                array("tipo" => "Motocicleta"),
                                array("tipo" => "Motoneta"),
                                array("tipo" => "Motoneta"),
                                array("tipo" => "Quadricíclo"),
                                array("tipo" => "Reboque"),
                                array("tipo" => "Trator"),
                                array("tipo" => "Triciclo"),
                                array("tipo" => "Ônibus")), array("tipo", "tipo"), 'Tipo de Veículo'); ?>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_textarea('descricao', 'Descrição <span class="bold text-danger">*</span>', '', array("required" => "true")); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('codinternemp', 'Cód Interno Empresa'); ?>
                            <?php echo render_input('kmatual', 'Km Atual <span class="bold text-danger">*</span>', '', 'number', array("required" => "true")); ?>
                            <?php echo render_input('chassi', 'Chassi'); ?>
                            <?php echo render_input('eixos', 'Eixos'); ?>
                            <?php echo render_select('proprietario', $staff, array("staffid", "name"), 'Proprietário/Arredatário <span class="bold text-danger">*</span>', '', array("required" => "true")); ?>
                        </div>
                        <div class="col-md-6">
                            <?php $anos = array();
                            for ($i = Carbon\Carbon::now()->year; $i > 1970; $i--) {
                                array_push($anos, array("ano" => $i));
                            } ?>
                            <?php echo render_select('ano', $anos, array("ano", "ano"), 'Ano <span class="bold text-danger">*</span>', '', array("required" => "true")); ?>
                            <?php echo render_select('categoria', array(), array(), 'Categoria'); ?>
                            <?php echo render_input('renavan', 'Renavam'); ?>
                            <?php echo render_input('cor', 'Cor'); ?>
                            <?php echo render_date_input('venclicenci', 'Venc Licenciamento <span class="bold text-danger">*</span>', '', array("required" => "true")); ?>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_input('alenado', 'Alienado(Banco)'); ?>
                            <?php echo render_input('locallicenci', 'Local Licenciamento'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('valorveic', 'Valor Veículo'); ?>
                            <?php echo render_date_input('datainicicontr', 'Data Inicio Contrato <span class="bold text-danger">*</span>', '', array("required" => "true")); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('numcontrato', 'Número Contrato'); ?>
                            <?php echo render_date_input('datafimcontr', 'Data Fim Contrato <span class="bold text-danger">*</span>', '', array("required" => "true")); ?>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_textarea('observacao', 'Observação'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-success"><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <!--Fim-Modal-->
    <div class="modal fade" id="supply" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <?php echo form_open(admin_url('fleet/vehicles/supply')); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Abastecimento</h4>
                </div>
                <div class="modal-body">
                    <div class="delete_id">
                        <input type="hidden" name="vehicleid" id="supplyvehicleid" value="">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo render_input('posto', 'Posto <span class="bold text-danger">*</span>', '', 'text', array("required" => "true")); ?>
                            <?php echo render_input('litro', 'Litros <span class="bold text-danger">*</span>', '', 'number', array("required" => "true", "step" => "0.0001")); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('valortotal', 'Valor Total <span class="bold text-danger">*</span>', '', 'number', array("required" => "true", "step" => "0.0001")); ?>
                            <?php echo render_input('precoporlitro', 'Preço por Litro', '', 'number', array("step" => "0.0001")); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_date_input('data', 'Data <span class="bold text-danger">*</span><a onclick="nowend()" id="hfin"> Agora</a>', '', array("required" => "true")); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('kilometragem', 'Kilometragem(km)', '', 'number', array("required" => "false")); ?>
                        </div>
                        <!--                <p>Todos os registros deste veículo será excluido.</p>-->
                        <!--                <p>Tem certeza que deseja excluir o Veículo?</p>-->
                    </div>
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
<?php } ?>
<?php if (has_permission('fleet', '', 'delete')) { ?>
    <div class="modal fade" id="delete_vehicle" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <?php echo form_open(admin_url('fleet/vehicles/delete', array('delete_partner_form'))); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Excluir Veículo</h4>
                </div>
                <div class="modal-body">
                    <div class="delete_id">
                        <input type="hidden" name="vehicleid" id="deletevehicleid" value="">
                    </div>
                    <p>Todos os registros deste veículo será excluido.</p>
                    <p>Tem certeza que deseja excluir o Veículo?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-danger _delete"><?php echo _l('confirm'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php } ?>
<?php init_tail(); ?>
<script>
    function new_vehicle() {
        $("#new-vehicles").modal("show");
    }

    function delet(vehicleid) {
        $("#deletevehicleid")[0].value = vehicleid;
        $("#delete_vehicle").modal().show();
    }

    function supply(vehicleid) {
        $("#supplyvehicleid")[0].value = vehicleid;
        $("#supply").modal().show();
    }

    function dt_custom_view(table, column, val) {
        var tableApi = $(table).DataTable();
        tableApi.column(column).search(val).draw();
    }

    setTimeout(timer, 5000);

    function timer() {
        dt_custom_view('#DataTables_Table_0', 4, '1');
    }

    function nowend() {
        $("#data")[0].value = getdate("date");
    }

    function getdate(type)
    {
        var d = new Date();
        var min = d.getMinutes();
        var hou = d.getHours();
        if(min<10)
            min = "0"+min;
        if(hou<10)
            hou = "0"+hou;
        var mes = d.getMonth()+1;
        var day = d.getDate();
        if(mes<10)
            mes = "0"+mes;
        if(day<10)
            day = "0"+day;

        var date = day+'/'+mes+'/'+d.getFullYear();
        var hour = hou+':'+min;
        var datetime = date+' '+hour;
        if(type == "hour")
            return hour;
        else if(type == "date")
            return date;
        else if(type == "datetime")
            return datetime;

    }

</script>