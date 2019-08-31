<?php
/**
 * Created by PhpStorm.
 * User: mateus.machado
 * Date: 13/02/2018
 * Time: 10:15
 */
?>
<div class="mbot30">
    <div>
        <h3>
            <b>
                <?php echo "Relatório Atendimentos"; ?>
            </b>
        </h3>
        <div>
            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("numero-atendimentos.show") ?>'>
                Atendimentos por Cliente
            </a>
            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("atendimento-por-tecnico.show") ?>'>
                Atendimentos por Técnico
            </a>
            <a class='btn btn-primary  mleft10 pull-left' href='<?php echo ciroute("atendimento-tempo-medio.show") ?>'>
                Tempo de Atendimentos
            </a>
        </div>
    </div>
    <div class="col-md-12 mtop35">
        <div class="row">
            <div class="col-md-3">
                <?php echo render_date_input('date_from', 'Data de Início(abertura)', _d($begin_open)); ?>
            </div>
            <div class="col-md-3">
                <?php echo render_date_input('date_to', 'Data de Fim(abertura)', _d($end_open)); ?>
            </div>
            <div class="form-group col-md-3">
                <label for="attend"><?php echo _l('contact'); ?></label>
                <select name="attend" id="attend" class="ajax-search" data-width="100%"
                        data-live-search="true"
                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                </select>
                <?php echo form_hidden('userid'); ?>
            </div>
            <div class="col-md-3" style="text-align: center; padding: 27px">
                <button class="btn btn-success" onclick="filter()"
                        style="margin: auto; vertical-align: middle"><?php echo _l('Filtrar'); ?></button>
            </div>
        </div>
        <div class="row">
            <?php if(PAINEL == INORTE){ ?>
            <div class="col-md-3">
                <?php echo render_date_input('date_from_status', 'Data de Início(status)', _d($begin_status)); ?>
            </div>
            <div class="col-md-3">
                <?php echo render_date_input('date_to_status', 'Data de Fim(status)', _d($end_status)); ?>
            </div>
            <div class="col-md-3">
                <?php echo render_select('status', $statuses, array('ticketstatusid', 'name'), 'Status', $status ); ?>
            </div>
            <?php }?>
        </div>
    </div>
</div>
<script>

</script>