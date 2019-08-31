<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 25/11/2017
 * Time: 11:03
 */

 foreach ($staff as $object) { ?>
    <ul class="kan-ban-col tasks-kanban" data-col-status-id="1" data-total-pages="1">
        <li class="kan-ban-col-wrapper">
            <div class="border-right panel_s">
                <div class="panel-heading-bg"
                     style="background:#989898;border-color:#989898;color:#fff;"
                     data-status-id="1">
                    <div class="kan-ban-step-indicator"></div>
                    <?php $current = $this->load_report_model->get_user_tasks_current($object['staffid']) ?>
                    <span class="heading"><?php echo $object['firstname'] . '(' . count($current) . ')'; ?>
                                                            </span>
                    <a href="#" onclick="return false;"
                       class="pull-right color-white">
                    </a>
                </div>
            </div>
            <div class="kan-ban-content-wrapper" style="max-height: 526px;">
                <div class="kan-ban-content" style="min-height: 526px;">
                    <ul class="status tasks-status sortable relative ui-sortable"
                        data-task-status-id="1">
                        <?php
                        $total_tasks = count($report);
                        foreach ($report as $task) {
                            $assignees = $this->load_report_model->get_stafftaskassignees($task['id']);
                            foreach ($assignees as $ass) {
                                if ($ass['staffid'] == $object['staffid']) {
                                    $this->load->view('admin/tasks/_kan_ban_card', array('task' => $task, 'status' => $task['status'], 'report' => true));
                                }
                            }
                        } ?>
                        <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_tasks > 0){echo ' hide';} ?>">
                            <h4 class="text-muted">
                                <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                                <?php echo _l('no_tasks_found'); ?></h4>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
    </ul>
<?php } ?>

