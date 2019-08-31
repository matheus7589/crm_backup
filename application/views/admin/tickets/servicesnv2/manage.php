<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body _buttons">
                        <?php echo render_select('servicenv2',$services,array('serviceid','name'),'ticket_settings_service'); ?>
						<a id="teste" href="#" onclick="doconfirm(); return false;" class="btn btn-info pull-left display-block"><?php echo _l('new_service'); ?></a>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('services_dt_name'),
                            _l('serviceid'),
							_l('secondServiceid'),
							_l('options'),
							),'services'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="service" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<?php echo form_open(admin_url('tickets/edit_servicenv2')); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title"><?php echo _l('ticket_service_edit'); ?></span>
						<span class="add-title"><?php echo _l('new_service'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="additional"></div>
							<?php echo render_input('name','service_add_edit_name'); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
			</div><!-- /.modal-content -->
			<?php echo form_close(); ?>
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<?php init_tail(); ?>
	<script>
		$(function(){
			initDataTable('.table-services', window.location.href, [2], [1,2]);
			_validate_form($('form'),{name:'required'},manage_ticket_services);
			$('#service').on('hidden.bs.modal', function(event) {
				$('#additional').html('');
				$('#service input').val('');
				$('.add-title').removeClass('hide');
				$('.edit-title').removeClass('hide');
			});
		});


		$(function search_DataTable(){ 
				$('#servicenv2').on('change', function(){
					var id = $(this).val();	
					console.log( id );
						var table = $('#DataTables_Table_0').DataTable();
						table.column(1).search(id, true, false).draw();
						//table.ajax.reload();
				});
		});

		function manage_ticket_services(form) {
			var data = $(form).serialize();
			var url = form.action;
			$.post(url, data).done(function(response) {
				//window.location.reload();
				var table = $('#DataTables_Table_0').DataTable();
				$('#service').modal('hide');
				table.ajax.reload();
			});
			return false;
		}
		function new_service(id){
            $('#additional').append(hidden_input('serviceid', id));
			$('#service').modal('show');
			$('.edit-title').addClass('hide');
		}
		function edit_service(invoker,id,serviceid){
			var name = $(invoker).data('name');
			$('#additional').append(hidden_input('id',id));
            $('#additional').append(hidden_input('serviceid',serviceid));
            $('#additional').append(hidden_input('isedit', 'editar'));
			$('#service input[name="name"]').val(name);
			$('#service').modal('show');
			$('.add-title').addClass('hide');
		}

        function doconfirm() {
            if ($('#servicenv2').val() == '') {
                alert("É necessário selecionar um serviço!");
            }else{
                var id = $('#servicenv2').val();
                new_service(id);
            }
        }

        function doconfirm_edit(invoker,id) {
            if ($('#servicenv2').val() == '') {
                alert("É necessário selecionar um serviço!");
            }else{
                var serviceid = $('#servicenv2').val();
                edit_service(invoker,id,serviceid);
            }
        }

	</script>
</body>
</html>
