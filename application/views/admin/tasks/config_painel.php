<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 style="margin-top: 0px;">Configurações do Painel</h3>
                        <hr>
                        <form method="POST" action="<?php echo ciroute("tasks.insere"); ?>">
                            <hr>
                            <h3>Espera</h3>
                            <div class="col-md-12" style="padding: 0px;">
                                <div class="col-md-6">
                                    <?php echo render_input('task_waiting_alert_time','<span class="text-warning">Tempo para alerta I (Minutos)</span>',get_option("task_waiting_alert_time"), "number"); ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="task_waiting_alert_sound" id="task_waiting_alert_sound" value="1" <?php if(get_option('task_waiting_alert_sound')=="1"){echo "checked='true'";}?>>
                                        <label data-toggle="tooltip" title="" for="task_waiting_alert_sound" data-original-title="Notificação sonora quanto o task entrar na alerta">Notificação sonora</label>
                                    </div>
                                    <?php
                                    echo render_select('task_waiting_alert_sound_type',array(
                                        array("name"=>"Única vez","type"=>"1"),
                                        array("name"=>"A cada 1 Minuto","type"=>"2"),
                                        array("name"=>"Constante","type"=>"3")
                                    ),array("type","name"),'Tipo da notificação sonora',get_option("task_waiting_alert_sound_type"),array(),array("id"=>"typesound1_wrapper"),'','',false);
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('task_waiting_limit_time','<span class="text-danger">Tempo para alerta II (Minutos)</span>',get_option("task_waiting_limit_time"), "number"); ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="task_waiting_limit_sound" id="task_waiting_limit_sound" value="1" <?php if(get_option('task_waiting_limit_sound')=="1"){echo "checked='true'";}?>>
                                        <label data-toggle="tooltip" title="" for="task_waiting_limit_sound" data-original-title="Notificação sonora quanto a tarefa entrar na alerta">Notificação sonora</label>
                                    </div>
                                    <?php echo render_select('task_waiting_limit_sound_type',array(
                                        array("name"=>"Única vez","type"=>"1"),
                                        array("name"=>"A cada 1 Minuto","type"=>"2"),
                                        array("name"=>"Constante","type"=>"3")
                                    ),array("type","name"),'Tipo da notificação sonora',get_option("task_waiting_limit_sound_type"),array(),array("id"=>"typesound2_wrapper"),'','',false);
                                    ?>
                                </div>
                                <h3>Progresso</h3>
                                <div class="col-md-12" style="padding: 0px;">
                                    <div class="col-md-6">
                                        <?php echo render_input('task_waiting_alert_time_attendance','<span class="text-warning">Tempo para alerta I (Minutos)</span>',get_option("task_waiting_alert_time_attendance"), "number"); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('task_waiting_limit_time_attendance','<span class="text-danger">Tempo para alerta II (Minutos)</span>',get_option("task_waiting_limit_time_attendance"), "number"); ?>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="pertubacao_notify" id="pertubacao_notify" value="1" <?php if(get_option('pertubacao_notify')=="1"){echo "checked='true'";}?>>
                                            <label data-toggle="tooltip" title="" for="pertubacao_notify" data-original-title="Notificação sonora painel de 2 em 2 minutos">Notificação Sonora</label>
                                        </div>
                                    </div>
                                </div>
                                <h3>Cores</h3>
                                <div class="col-md-12" style="padding: 0px;">
                                    <div class="col-md-6">
                                        <?php echo render_color_picker('task_main_color','Tema Principal', get_option("task_main_color")); ?>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding: 0px;">
                                    <h3>Atualização</h3>
                                    <div class="col-md-6">
                                        <?php echo render_input('painel_task_refresh_time','Tempo para atualizar (Segundos)', get_option("painel_task_refresh_time")); ?>
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
            $("#task_waiting_alert_sound").change(function() {
                check();
            });
            $("#task_waiting_limit_sound").change(function() {
                check();
            });
            $("#bloqueio_tasks").change(function() {
                check();
            });
            function check() {
                if($("#task_waiting_limit_sound")[0].checked)
                    $("#typesound2_wrapper").removeClass("hide");
                else
                    $("#typesound2_wrapper").addClass("hide");

                if($("#task_waiting_alert_sound")[0].checked)
                    $("#typesound1_wrapper").removeClass("hide");
                else
                    $("#typesound1_wrapper").addClass("hide");
                if($("#bloqueio_tasks")[0].checked)
                    $("#bloqueio_wrapper").removeClass("hide");
                else
                    $("#bloqueio_wrapper").addClass("hide");
            }
            $(function () {
                check();
            });
        </script>
