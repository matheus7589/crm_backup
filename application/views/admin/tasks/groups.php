<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 19/12/2017
 * Time: 18:25
 */
init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="row">
                            <h4 class="task-info-heading mbot15"><i class="fa fa-users" aria-hidden="true"></i> <?php echo _l('task_single_assignees'); ?></h4>
                            <select data-width="100%" data-live-search-placeholder="<?php echo _l('search_project_members'); ?>" id="add_task_assignees_default" class="text-muted mbot10 task-action-select selectpicker" name="select-assignees-default" data-live-search="true" title='<?php echo _l('task_single_assignees_select_title'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <?php
                                    $options = '';
                                    $ultimo = '';
                                    foreach ($staff as $assignee)
                                    {
                                        if (total_rows('tblstafftaskassignees', array('staffid' => $assignee['staffid'],'taskid' => $task->id)) == 0)
                                        {
                                            if($assignee['staffid'] != $ultimo)
                                            {
                                                $options .= '<option value="' . $assignee['staffid'] . '">' . get_staff_full_name($assignee['staffid']) . '</option>';
                                                $ultimo = $assignee['staffid'];
                                            }
                                        }
                                    }
                                    echo $options;
                                ?>
                            </select>
                            <div class="task_users_wrapper">
                                <?php foreach ($default_assi as $assigned){?>
                                    <div class="task-user" data-toggle="tooltip" data-title="<?php echo get_staff_full_name($assigned['staffid'])?>" data-original-title="" title="">
                                        <a href="javascript:excluir(<?php echo $assigned['staffid']; ?>,0)" target="_blank">
                                            <img src="<?php echo APP_BASE_URL."uploads/staff_profile_images/".$assigned['staffid']."/small_".$assigned['profile_image']?>" class="staff-profile-image-small" alt="<?php echo get_staff_full_name($assigned['staffid'])?>"></a>  <a href="#" class="remove-task-user text-danger" onclick="remove_assignee(<?php echo $assigned['id']; ?>, <?php echo $assigned['taskid']; ?>); return false;">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="row">
                            <h4 class="task-info-heading mbot15"><i class="fa fa-users" aria-hidden="true"></i> <?php echo _l('task_single_followers'); ?></h4>
                            <select data-width="100%" data-live-search-placeholder="<?php echo _l('search_project_members'); ?>" id="add_task_follower_default" class="text-muted mbot10 task-action-select selectpicker" name="select-assignees-default" data-live-search="true" title='<?php echo _l('task_single_followers_select_title'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <?php
                                $options = '';
                                $ultimo = '';
                                foreach ($staff as $assignee)
                                {
                                    if (total_rows('tblstafftasksfollowers', array('staffid' => $assignee['staffid'],'taskid' => $task->id)) == 0)
                                    {
                                        if($assignee['staffid'] != $ultimo)
                                        {
                                            $options .= '<option value="' . $assignee['staffid'] . '">' . get_staff_full_name($assignee['staffid']) . '</option>';
                                            $ultimo = $assignee['staffid'];
                                        }
                                    }
                                }
                                echo $options;
                                ?>
                            </select>
                            <div class="task_users_wrapper">
                                <?php foreach ($default_foll as $follower){?>
                                    <div class="task-user" data-toggle="tooltip" data-title="<?php echo get_staff_full_name($follower['staffid'])?>" data-original-title="" title="">
                                        <a href="javascript:excluir(<?php echo $follower['staffid']; ?>,1)" target="_blank">
                                        <img src="<?php echo APP_BASE_URL."uploads/staff_profile_images/".$follower['staffid']."/small_".$follower['profile_image']?>" class="staff-profile-image-small" alt="<?php echo get_staff_full_name($follower['staffid'])?>"></a>  <a href="#" class="remove-task-user text-danger" onclick="remove_follower(<?php echo $follower['id']; ?>, <?php echo $follower['taskid']; ?>); return false;">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                    </div>
                                <?php }?>
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
    // type 0 = assigned
    // type 1 = follower
    $( "#add_task_assignees_default").change(function() {

        var value = $( "#add_task_assignees_default")[0].value;
        var data = {staffid:value, type:0, mastertype:1};

        $.post(admin_url + 'tasks/addgp', data).done(function (response) {
            window.location.href = window.location.href;
        });
    });

    $( "#add_task_follower_default").change(function() {

        var value = $( "#add_task_follower_default")[0].value;
        var data = {staffid:value, type:1, mastertype:1};

        $.post(admin_url + 'tasks/addgp', data).done(function (response) {
            window.location.href = window.location.href;
        });
    });

    function excluir(staffid,typeexv)
    {
        var data = {staffidex:staffid, typeex:typeexv, mastertype:0};
        $.post(admin_url + 'tasks/addgp', data).done(function (response) {
            window.location.href = window.location.href;
        });
    }
</script>
