<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 27/02/2018
 * Time: 15:12
 */

$rel_type = $out->rel_type;
$rel_id = $out->rel_id;
init_head();?>
<?php if(PAINEL == INORTE){
    $disabled = array();
}else{
    $disabled = array("disabled"=>"true");
} ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <!--Cabeçario-Informações-->
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open(admin_url('fleet/get_out/'.$outid))?>
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="col-md-6">
                                <?php echo render_date_input('data','Data <span class="bold text-danger">*</span>',\Carbon\Carbon::parse($out->data)->format("d/m/Y"), array("id"=>"data"));?>
                                <?php echo render_select('staffid',$staff,array("staffid","name"),'Colaborador <span class="bold text-danger">*</span>',$out->staffid);?>
                                <!--                                --><?php //echo render_select('vehicleid',$vehiclesact,array("vehicleid","descricao"),'Veículo','',array("required"=>"true"));?>
                                <div class="form-group">
                                    <label for="Tipo" class="control-label">Veículo <span class="bold text-danger">*</span></label>
                                    <select id="vehicleid" name="vehicleid" class="selectpicker" data-width="100%" data-none-selected-text="Nada selecionado" data-live-search="true" tabindex="-98" required="true">
                                        <option></option>
                                        <?php foreach ($vehicles as $vehiclesa){ ?>
                                            <option value='<?php echo $vehiclesa['vehicleid']; ?>' <?php if($vehiclesa['vehicleid'] == $out->vehicleid)echo "selected='true'";?>><?php echo $vehiclesa['descricao']; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_textarea('motivo','Motivo <span class="bold text-danger">*</span>',$out->motivo,array("required"=>"true", "style"=>"height: 110px"));?>
                            </div>
                            <div class="col-md-6">
                                <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                                <select name="rel_type" <?php echo (PAINEL == INORTE) ? '' : 'disabled=' . $disabled['disabled']; ?>
                                        class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <option value="vendas"
                                        <?php if($rel_type == 'vendas'){echo 'selected';} ?>><?php echo _l('Vendas'); ?>
                                    </option>
                                    <option value="project"
                                        <?php if($rel_type == 'project'){echo 'selected';} ?>><?php echo _l('project'); ?></option>
                                    <option value="invoice" <?php if($rel_type == 'invoice'){echo 'selected';} ?>>
                                        <?php echo _l('invoice'); ?>
                                    </option>
                                    <option value="customer"
                                        <?php if($rel_type == 'customer'){echo 'selected';} ?>>
                                        <?php echo _l('client'); ?>
                                    </option>
                                    <option value="estimate" <?php if($rel_type == 'estimate'){echo 'selected';} ?>>
                                        <?php echo _l('estimate'); ?>
                                    </option>
                                    <option value="contract" <?php if($rel_type == 'contract'){echo 'selected';} ?>>
                                        <?php echo _l('contract'); ?>
                                    </option>
                                    <option value="ticket" <?php if($rel_type == 'ticket'){echo 'selected';} ?>>
                                        <?php echo _l('ticket'); ?>
                                    </option>
                                    <option value="expense" <?php if($rel_type == 'expense'){echo 'selected';} ?>>
                                        <?php echo _l('expense'); ?>
                                    </option>
                                    <option value="lead" <?php if($rel_type == 'lead'){echo 'selected';} ?>>
                                        <?php echo _l('lead'); ?>
                                    </option>
                                    <option value="proposal" <?php if($rel_type == 'proposal'){echo 'selected';} ?>>
                                        <?php echo _l('proposal'); ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="col-md-6">

                                <?php echo render_input('km_inicial','Km Inicial <span class="bold text-danger">*</span>',$out->km_inicial,'number');?>
                                <?php echo render_datetime_input('datetime_inicial', 'Inicio <span class="bold text-danger">*</span>',\Carbon\Carbon::parse($out->datetime_inicial)->format("d/m/Y H:i"));?>
                                <?php echo render_input('local', 'Local',$out->local); ?>
                            </div>
                            <div id="fimdisable"></div>
                            <div class="col-md-6">

                                <div class="form-group<?php if(!isset($rel_id)){echo ' hide';} ?>" id="rel_id_wrapper">
                                    <label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
                                    <div id="rel_id_select">
                                        <select name="rel_id" id="rel_id"
                                                class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex');?>" disabled>
                                                                                    <?php if($rel_id != '' && $rel_type != ''){
                                                                                        $rel_data = get_relation_data($rel_type,$rel_id);
                                                                                        $rel_val = get_relation_values($rel_data,$rel_type);
                                                                                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                                                                                    } ?>
                                        </select>
                                    </div>
                                </div>

                                <?php echo render_input('km_final','Km Final',$out->km_final,'number');?>
                                <?php echo render_datetime_input('datetime_final', 'Fim',\Carbon\Carbon::parse($out->datetime_final)->format("d/m/Y H:i"));?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <?php echo render_textarea('obs','Observação',$out->obs);?>
                        </div>
                        <div class="col-md-12">
                            <input type="submit" value="Salvar" class="btn btn-success pull-right">
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>