<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body _buttons">
                        <?php if(is_admin()) { ?>
                            <a href="<?php echo admin_url('announcements/announcement'); ?>" class="btn btn-info"><?php echo _l('new_announcement'); ?></a>
                            <a href="#" onClick="$('#new_notification').modal('show'); return false;" class="btn btn-info">Nova Notificação</a>
                        <?php } else { echo '<h4 class="no-margin bold">'._l('announcements').'</h4>';} ?>
                    </div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
						<div class="clearfix"></div>
						<?php render_datatable(array(_l('name'),_l('announcement_date_list'),_l('options')),'announcements'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="new_notification" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="conttent-modal-replies-title">Criar Notificação</div></h4>
            </div>
            <div class="modal-body" id="conttent-modal-replies">
                <?php echo form_open(admin_url('announcements'));?>
                <div class="form-group">
                    <label for="Tipo" class="control-label">Tipo</label>
                    <select id="master" name="master" class="selectpicker" data-width="100%" data-none-selected-text="Nada selecionado" data-live-search="true" tabindex="-98">
                        <option value="1" selected>Tela Cheia</option>
                        <option value="0">Barra de tarefas</option>
                    </select>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="show_name" id="show_name" checked>
                    <label data-toggle="tooltip" title="" for="show_name" data-original-title="Mostrar meu nome">Mostrar meu nome</label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="send_all" id="send_all" onclick="hidees()">
                    <label data-toggle="tooltip" title="" for="send_all" data-original-title="Enviar notificação para todos os usuários">Enviar para todos</label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="send_department" id="send_department" onclick="change_to_department()">
                    <label data-toggle="tooltip" title="" for="send_department" data-original-title="Enviar notificação para usuários dos departamentos">Enviar para departamentos</label>
                </div>
                <div id="tohide">
                    <?php echo render_select("to",$staff,array("staffid","name"),'Para usuário:','',array("id"=>"to"));?>
                </div>
                <?php echo render_textarea("description","Descrição");?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <input type="submit" class="btn btn-success" value="Enviar"/>
            </div>
            <?php echo form_close();?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
    function hidees() {
        if($('#send_all')[0].checked)
            $("#tohide")[0].className = "hide";
        else
            $("#tohide")[0].className = "";
    }
    
    function change_to_department() {
        if($('#send_department')[0].checked) {
            $.get(admin_url + 'announcements/change_to_department/').done(function (response) {
                $('#to').empty();
                $('#to').html(response);
                $('#to').selectpicker("refresh");
                $('label[for=to]').text("Para departamento: ");
            });
        }else{
            $.get(admin_url + 'announcements/change_to_staff/').done(function (response) {
                $('#to').empty();
                $('#to').html(response);
                $('#to').selectpicker("refresh");
                $('label[for=to]').text("Para usuário: ");
            });
        }
    }
    
</script>
