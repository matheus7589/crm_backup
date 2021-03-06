<div class="modal fade" id="kb_group_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('knowledge_base/group'),array('id'=>'kb_group_form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_kb_group'); ?></span>
                    <span class="add-title"><?php echo _l('new_group'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name','kb_group_add_edit_name'); ?>
                        <?php echo render_color_picker('color',_l('kb_group_color')); ?>
                        <?php echo render_textarea('description','kb_group_add_edit_description'); ?>
                        <div class="form-group">
                            <label for="description" class="control-label">Departamentos</label>
                            <select id="departments" name="departments[]" class="selectpicker" multiple="true" data-width="100%" data-none-selected-text="Nada selecionado" data-live-search="true" tabindex="-98">
                                <?php
                                    foreach ($departments as $department)
                                    {
                                        echo "<option value='".$department['departmentid']."'>".$department['name']."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <?php echo render_input('group_order','kb_group_order',total_rows('tblknowledgebasegroups') + 1,'number'); ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="disabled" id="disabled">
                            <label for="disabled"><?php echo _l('kb_group_add_edit_disabled'); ?></label>
                        </div>
                        <p class="text-muted"><?php echo _l('kb_group_add_edit_note'); ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
