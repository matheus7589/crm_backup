<?php

use Carbon\Carbon;

init_head();
?>


<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <a href='<?php echo admin_url('tarefas/') ?>' class='btn btn-primary'>
                            <i class="fa fa-tasks" aria-hidden="true"></i>
                            Tarefas
                        </a>

                        <form method="post" action="<?php echo admin_url('tarefas/parada/parar/') ?>"
                              id="parar_tarefas_form" class="form-inline pull-right">
                            <button class='btn btn-danger '>
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                Parar Agora
                            </button>
                        </form>
                    </div>
                </div>


                <div class="panel_s mtop5">
                    <div class="panel-body">
                        <h2>Horários de Paradas</h2>
                        <form method='POST' action='<?php echo ciroute("parar-tarefas.store") ?>'>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="day[]" value="1">
                                        Domingo
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="day[]" value="2">
                                        Segunda
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="day[]" value="3">
                                        Terça
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="day[]" value="4">
                                        Quarta
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="day[]" value="5">
                                        Quinta
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="day[]" value="6">
                                        Sexta
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="day[]" value="7">
                                        Sábado
                                    </label>
                                </div>
                            </div>
                            <div class='form-group row'>

                                <div class='col-md-2'>
                                    <label for="stoptimer">
                                        Horário da Parada <?php echo form_error('stoptimer'); ?></label>
                                    <input type="time" name="stoptimer" id="stoptimer"
                                           class='form-control' required value="<?php echo set_value("stoptimer") ?>">
                                </div>
                            </div>
                            <div class='form-group row'>
                                <div class='col-md-12'>
                                    <button type="submit" class='btn btn-primary'>
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                        Salvar
                                    </button>
                                </div>
                            </div>

                        </form>

                        <hr>
                        <h2>Últimos Horários Definidos</h2>

                        <table class='table table-striped table-bordered'>
                            <thead>
                            <tr>

                                <th>
                                    Dias
                                </th>
                                <th>
                                    Horário de Parada
                                </th>

                                <th>
                                    Criado em
                                </th>

                                <th>
                                    Ações
                                </th>
                            </tr>
                            </thead>

                            <tbody>


                            <?php if ($timers) {
                                foreach ($timers as $timer) { ?>

                                    <tr>

                                        <td>
                                            <?php
                                            foreach ($timer->days as $day) {
                                                echo dias_semana($day) . ", ";
                                            }

                                            ?>
                                        </td>

                                        <td><?php echo $timer->stop_timers ?></td>
                                        <td>
                                            <?php
                                            $date_insert = Carbon::parse($timer->data_insert);
                                            echo "{$date_insert->day}/{$date_insert->month}/{$date_insert->year}";
                                            echo " ás {$date_insert->hour}:{$date_insert->minute}";
                                            ?>
                                        </td>

                                        <td>
                                            <form action="<?php echo ciroute("parar-tarefas.destroy", [$timer->settimerstopid]) ?>"
                                                  method="POST">
                                                <input type="hidden" value="DELETE" name="_method">
                                                <button class="btn btn-danger" type="submit">Deletar</button>
                                            </form>
                                        </td>

                                    </tr>

                                    <?php
                                }
                            } ?>

                            </tbody>


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php init_tail(); ?>
    <script>

        $("#parar_tarefas_form").on('submit', function (event) {

            if (!confirm("Tem certeza que deseja parar todas as tarefas ?")) {
                return false
            }

        });

    </script>



