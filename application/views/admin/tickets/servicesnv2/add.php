<?php init_head();
 ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'new_ticket_form')); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">


							<div class="col-md-6">
								<?php echo render_input('subject','ticket_settings_subject','','text',array('required'=>'true')); ?>
								<?php $selected = (isset($userid) ? $userid : ''); ?>
								<div class="form-group">
									<label for="contactid"><?php echo _l('contact'); ?></label>
									<select name="contactid" required="true" id="contactid" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									</select>
									<?php echo form_hidden('userid'); ?>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?php $value = (isset($userid) ? $client->firstname . ' ' . $client->lastname: ''); ?>
										<?php echo render_input('to','ticket_settings_to',$value,'text',array('disabled'=>true)); ?>
									</div>
									<div class="col-md-6">
										<?php $value = (isset($userid) ? $client->email: ''); ?>
										<?php echo render_input('email','ticket_settings_email',$value,'email',array('disabled'=>true)); ?>
									</div>
									<div class="col-md-6">
										<?php $value = (isset($userid) ? $client->phonenumber: ''); ?>
										<?php echo render_input('telefone','Telefone',$value,'number',array('disabled'=>true)); ?>
									</div>
									<div class="col-md-6">
										<?php $value = (isset($userid) ? $client->company: ''); ?>
										<?php echo render_input('fantasia','Nome Fantasia',$value,'text',array('disabled'=>true)); ?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
											<?php echo render_select('department',$departments,array('departmentid','name'),'ticket_settings_departments',(count($departments) == 1) ? $departments[0]['departmentid'] : '',array('required'=>'true')); ?>
									</div>
									<div class="col-md-6">
										<?php echo render_input('cc','CC'); ?>
									</div>
								</div>
								
								<div class="form-group">
									<label for="assigned" class="control-label">
										<?php echo _l('ticket_settings_assign_to'); ?>
									</label>
									<select name="assigned" id="assigned" class="form-control selectpicker" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-width="100%">
										<option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
										<?php foreach($staff as $member){ ?>
										<option value="<?php echo $member['staffid']; ?>" <?php if($member['staffid'] == get_staff_user_id()){echo 'selected';} ?>>
											<?php echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
										</option>
										<?php } ?>
									</select>
								</div>
								<div style="margin-top: 5px">
									<?php $priorities['callback_translate'] = 'ticket_priority_translate';
									echo render_select('priority',$priorities,array('priorityid','name'),'ticket_settings_priority',do_action('new_ticket_priority_selected',2),array('required'=>'true')); ?>
								</div>
								<label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
									<input type="text" class="tagsinput" id="tags" name="tags" data-role="tagsinput">
							</div>
							<div class="col-md-6">								
								<div class="row">
								<div class="col-md-12">
								
								<!-- render_custom_fields renderiza os campos persolaziados criados -->
									<?php echo render_custom_fields('tickets'); ?>
								<!-- /////////////////////////////////////////////////////////////  -->
									<?php if(get_option('services') == 1){ ?>
									<label for="service" class="control-label">
										<?php echo _l('Serviço Nível 1'); ?>
									</label>
										<select name="service" id="service" class="form-control selectpicker" data-live-search="true">
											<option value=""></option>
											<?php foreach($services as $service): ?>
												<option value="<?php echo $service["serviceid"]; ?>">
												<?php echo $service["name"]; 
													$aux = $service["serviceid"];
												?>
												</option>
											<?php endforeach ?>
										</select>
									<?php } ?>

									<div style="margin-top: 15px">
										<label for="service" class="control-label">
											<?php echo _l('Serviço Nível 2'); ?>
										</label>
										<select name="servicenv2" id="servicenv2" class="form-control selectpicker" data-live-search="true">
							
										</select>	
									</div>

								</div>	
								</div>

								<div class="form-group hide projects-wrapper" style="margin-top: 20px">
									<label for="project_id"><?php echo _l('project'); ?></label>
									<div id="project_ajax_search_wrapper">
										<select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-heading">
								<?php echo _l('ticket_add_body'); ?>
							</div>
							<div class="panel-body">
							<div class="row">
									<div class="col-md-12 mbot20">
									<select id="insert_predefined_reply" data-live-search="true" class="selectpicker mleft10 pull-right" data-title="<?php echo _l('ticket_single_insert_predefined_reply'); ?>">
										<option value=""></option>
										<?php foreach($predefined_replies as $predefined_reply){ ?>
										<option value="<?php echo $predefined_reply['id']; ?>"><?php echo $predefined_reply['name']; ?></option>
										<?php } ?>
									</select>
									<?php if(get_option('use_knowledge_base') == 1){ ?>
									<?php $groups = get_all_knowledge_base_articles_grouped(); ?>
									<select id="insert_knowledge_base_link" class="selectpicker pull-right" data-live-search="true" onchange="insert_ticket_knowledgebase_link(this);" data-title="<?php echo _l('ticket_single_insert_knowledge_base_link'); ?>">
										<option value=""></option>
										<?php foreach($groups as $group){ ?>
											<?php if(count($group['articles']) > 0){ ?>
											<optgroup label="<?php echo $group['name']; ?>">
												<?php foreach($group['articles'] as $article) { ?>
													<option value="<?php echo $article['articleid']; ?>">
														<?php echo $article['subject']; ?>
													</option>
												<?php } ?>
											</optgroup>
											<?php } ?>
										<?php } ?>
									</select>
									<?php } ?>
								</div>
							</div>
								<div class="clearfix"></div>
								<?php echo render_textarea('message','','',array(),array(),'','tinymce'); ?>
							</div>
							<div class="panel-footer attachments_area">
								<div class="row attachments">
									<div class="attachment">
										<div class="col-md-4 col-md-offset-4 mbot15">
										<div class="form-group">
												<label for="attachment" class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
											<div class="input-group">
												<input type="file" extension="<?php echo str_replace('.','',get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
												<span class="input-group-btn">
													<button class="btn btn-success add_more_attachments p7" type="button"><i class="fa fa-plus"></i></button>
												</span>
											</div>
										</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 text-center">
					<button type="submit" data-form="#new_ticket_form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
	<?php init_tail(); ?>
	<?php echo app_script('assets/js','tickets.js'); ?>
	<script>
		$(function(){
			init_ajax_search('contact','#contactid.ajax-search',{tickets_contacts:true});
			$('#new_ticket_form').validate();
		});
	</script>
	<script>
		$(function mudanca(){
			$("#service").on('change', function(){
				var id = $(this).val();
				$.ajax({
					url: "servicenv2",
					type: 'GET',
					dataType: 'html',
					data: {service: id},
					success: function (data) {
						if(data){
							var opcoes = '<option value=""></option>' +
							'<option value="2">opção 1</option>';
							console.log(data);
							$("#servicenv2").empty();
							$("#servicenv2").html(data);
							$("#servicenv2").selectpicker("refresh");
							
						}
				}
        	});
			});
		});
	</script>

