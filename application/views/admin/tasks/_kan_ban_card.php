<li data-task-id="<?php echo $task['id']; ?>">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 task-name">
                <a href="#" onclick="init_task_modal(<?php echo $task['id']; ?>);return false;">
                    <span class="inline-block full-width mtop10 mbot10"><?php echo $task['name']; ?></span>
                </a>
            </div>
            <div class="col-md-6 text-muted">
                <?php
                $assignees = $this->tasks_model->get_task_assignees($task['id']);
                foreach ($assignees as $assignee) {
                    echo '<a href="' . admin_url('profile/' . $assignee['assigneeid']) . '">' . staff_profile_image($assignee['assigneeid'], array(
                            'staff-profile-image-xs mright5'
                        ), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => $assignee['firstname'] . ' ' . $assignee['lastname']
                        )) . '</a>';
                } ?>
            </div>
            <div class="col-md-6 text-right text-muted">

                <?php if (total_rows('tbltaskchecklists') > 0) { ?>
                    <span class="mright5 inline-block text-muted" data-toggle="tooltip"
                          data-title="<?php echo _l('task_checklist_items'); ?>">
            <i class="fa fa-check-square-o" aria-hidden="true"></i> <?php echo total_rows('tbltaskchecklists', array(
                            'taskid' => $task['id'],
                            'finished' => 1,
                        ));; ?>
                        <?php echo total_rows('tbltaskchecklists', array(
                            'taskid' => $task['id'],
                        ));; ?>
              </span>
                <?php } ?>
                <span class="mright5 inline-block text-muted" data-toggle="tooltip"
                      data-title="<?php echo _l('task_comments'); ?>">
                <i class="fa fa-comments"></i> <?php echo total_rows('tblstafftaskcomments', array(
                        'taskid' => $task['id'],
                    ));; ?>
                </span>
                <?php $total_attachments = total_rows('tblfiles', array(
                    'rel_id' => $task['id'],
                    'rel_type' => 'task',
                )); ?>
                <span class="inline-block text-muted" data-toggle="tooltip"
                      data-title="<?php echo _l('task_view_attachments'); ?>">
                   <i class="fa fa-paperclip"></i>
                    <?php echo $total_attachments; ?>
                 </span>
            </div>
            <?php $tags = get_tags_in($task['id'], 'task');
            if (count($tags) > 0) { ?>
                <div class="col-md-12">
                    <div class="mtop5 kanban-tags">
                        <?php echo render_tags($tags); ?>
                    </div>
                </div>
            <?php } ?>
            <?php if (isset($report)) { ?>
                <div class="col-md-12" style="margin-top: 20px; font-size: small">
                    <?php echo "<b>Prioridade: </b>"; ?>


                    <?php $priorities = [
                        '#F0FFFF',        //Default
                        '#337ab7', //Baixo
                        '#5cb85c', //Medio
                        '#f0ad4e', //Alto
                        '#d9534f', //Urgente
                        '#5bc0de', //Atualizacao
                        '#777'     //Agendado
                    ]; ?>
                    <?php $color = $priorities[$task['priority']]; ?>
                    <span class="label inline-block pull-right"
                          style="font-size: small;border:1px solid <?php echo $color; ?>; color: <?php echo $color; ?> "><?php echo ticket_priority_translate($task['priority']) ?></span>

                </div>
            <?php } ?>

        </div>
    </div>
</li>
