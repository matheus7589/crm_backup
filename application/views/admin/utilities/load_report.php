<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 14/10/2017
 * Time: 10:11
 */

init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3>
                            <b>
                                <?php echo "Quadro de distribuição de tarefas pendentes"; ?>
                            </b>
                        </h3>
                        <div class="col-md-4" data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
                            <?php echo render_input('search','','','search',array('data-name'=>'search','onkeyup'=>'tasks_kanban_report();','placeholder'=>_l('search_tasks')),array(),'no-margin') ?>
                        </div>
                        <?php echo form_hidden('sort_type'); ?>
                        <?php echo form_hidden('sort'); ?>
                        <?php echo form_hidden('is_load_report'); ?>
                        <div class="col-md-2 col-xs-6 border-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary">Filtro</button>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
<!--                                    <span class="sr-only">Toggle Dropdown</span>-->
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a onclick="tasks_kanban_sort('priority'); return false" href="#">Prioridade</a></li>
                                    <li><a onclick="tasks_kanban_sort('dateadded'); return false" href="#">Data</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                            <div class="row">
                                <div class="container-fluid" style="min-width: <?php echo count($staff) * 360; ?>px">
                                    <div id="kan-ban">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>

    tasks_kanban_report();

    function tasks_kanban_sort(type) {
        var sort_type = $('input[name="sort_type"]');
        var sort = $('input[name="sort"]');
        var is_load_report = $('input[name="is_load_report"]');
        sort_type.val(type);
        is_load_report.val(true);
        if (sort.val() == 'ASC') {
            sort.val('DESC');
        } else if (sort.val() == 'DESC') {
            sort.val('ASC');
        } else {
            sort.val('ASC');
        }

        tasks_kanban_report();
    }

    </script>
