<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <h3 style="margin-top: 0px;">Configurações do Painel</h3>
                <hr>
                <form method="POST" action="<?php echo ciroute("tickets.insere"); ?>">
                    <h3>Prioridade</h3>
                    <div class="col-md-12" style="padding: 0px;">
                        <div class="col-md-6">
                            <?php echo render_input('numero','Tempo máximo para prioridade (Dias)',(isset($valida) ? $valida->validation: 0), "number"); ?>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                    <hr>
                    <h3>Espera</h3>
                    <div class="col-md-12" style="padding: 0px;">
                        <div class="col-md-6">
                            <?php echo render_input('ticket_waiting_alert_time','<span class="text-warning">Tempo para alerta I (Minutos)</span>',get_option("ticket_waiting_alert_time"), "number"); ?>
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="ticket_waiting_alert_sound" id="ticket_waiting_alert_sound" value="1" <?php if(get_option('ticket_waiting_alert_sound')=="1"){echo "checked='true'";}?>>
                                <label data-toggle="tooltip" title="" for="ticket_waiting_alert_sound" data-original-title="Notificação sonora quanto o ticket entrar na alerta">Notificação sonora</label>
                            </div>
                            <?php
                                echo render_select('ticket_waiting_alert_sound_type',array(
                                    array("name"=>"Única vez","type"=>"1"),
                                    array("name"=>"A cada 1 Minuto","type"=>"2"),
                                    array("name"=>"Constante","type"=>"3")
                                    ),array("type","name"),'Tipo da notificação sonora',get_option("ticket_waiting_alert_sound_type"),array(),array("id"=>"typesound1_wrapper"),'','',false);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('ticket_waiting_limit_time','<span class="text-danger">Tempo para alerta II (Minutos)</span>',get_option("ticket_waiting_limit_time"), "number"); ?>
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="ticket_waiting_limit_sound" id="ticket_waiting_limit_sound" value="1" <?php if(get_option('ticket_waiting_limit_sound')=="1"){echo "checked='true'";}?>>
                                <label data-toggle="tooltip" title="" for="ticket_waiting_limit_sound" data-original-title="Notificação sonora quanto o ticket entrar na alerta">Notificação sonora</label>
                            </div>
                            <?php echo render_select('ticket_waiting_limit_sound_type',array(
                                array("name"=>"Única vez","type"=>"1"),
                                array("name"=>"A cada 1 Minuto","type"=>"2"),
                                array("name"=>"Constante","type"=>"3")
                                ),array("type","name"),'Tipo da notificação sonora',get_option("ticket_waiting_limit_sound_type"),array(),array("id"=>"typesound2_wrapper"),'','',false);
                            ?>
                        </div>
                        <h3>Atendimentos</h3>
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="col-md-6">
                                <?php echo render_input('ticket_waiting_alert_time_attendance','<span class="text-warning">Tempo para alerta I (Minutos)</span>',get_option("ticket_waiting_alert_time_attendance"), "number"); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input('ticket_waiting_limit_time_attendance','<span class="text-danger">Tempo para alerta II (Minutos)</span>',get_option("ticket_waiting_limit_time_attendance"), "number"); ?>
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="pertubacao_notify" id="pertubacao_notify" value="1" <?php if(get_option('pertubacao_notify')=="1"){echo "checked='true'";}?>>
                                    <label data-toggle="tooltip" title="" for="pertubacao_notify" data-original-title="Notificação sonora painel de 2 em 2 minutos">Notificação Sonora</label>
                                </div>
                            </div>
                        </div>
                        <h3>Bloqueio</h3>
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="col-md-12">
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="bloqueio_tickets" id="bloqueio_tickets" value="1" <?php if(get_option('bloqueio_tickets')=="1"){echo "checked='true'";}?>>
                                    <label data-toggle="tooltip" title="" for="bloqueio_tickets" data-original-title="Habilitar timer de tickets">Bloquear tickets</label>
                                </div>
                            </div>
                            <div id="bloqueio_wrapper">
                                <div class="col-md-6">
                                    <?php echo render_input('ticket_block_limit_number_attendance','Número máximo tickets',get_option("ticket_block_limit_number_attendance"), "number"); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('ticket_block_limit_time_attendance','Tempo máximo atendimento (Horas)',get_option("ticket_block_limit_time_attendance"), "number"); ?>
                                </div>
                            </div>
                        </div>
                        <h3>Cores</h3>
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="col-md-6">
                                <?php echo render_color_picker('ticket_main_color','Tema Principal', get_option("ticket_main_color")); ?>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding: 0px;">
                            <h3>Atualização</h3>
                            <div class="col-md-6">
                                <?php echo render_input('painel_refresh_time','Tempo para atualizar (Segundos)', get_option("painel_refresh_time")); ?>
                            </div>
                                <div class="col-md-6">
                                    <?php echo render_select('assigned',$staff,array("staffid","firstname"),'Técnico Padrão para ticket externo',get_option("clients_predefined_assign"),array(),array(),'','',false);
                                    ?>
                                </div>
                        </div>

                    <hr>

                    <button type="submit" class="btn btn-primary pull-right">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                        Salvar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $("#ticket_waiting_alert_sound").change(function() {
        check();
    });
    $("#ticket_waiting_limit_sound").change(function() {
        check();
    });
    $("#bloqueio_tickets").change(function() {
        check();
    });
    function check() {
        if($("#ticket_waiting_limit_sound")[0].checked)
            $("#typesound2_wrapper").removeClass("hide");
        else
            $("#typesound2_wrapper").addClass("hide");

        if($("#ticket_waiting_alert_sound")[0].checked)
            $("#typesound1_wrapper").removeClass("hide");
        else
            $("#typesound1_wrapper").addClass("hide");
        if($("#bloqueio_tickets")[0].checked)
            $("#bloqueio_wrapper").removeClass("hide");
        else
            $("#bloqueio_wrapper").addClass("hide");
    }
    $(function () {
        check();
    });
</script>
