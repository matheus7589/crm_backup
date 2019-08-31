<?php include_once(APPPATH.'views/admin/includes/helpers_bottom.php'); ?>
<?php do_action('before_js_scripts_render'); ?>
<script src="<?php echo base_url('assets/plugins/app-build/vendor.js?v='.get_app_version()); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jquery/jquery-migrate.'.(ENVIRONMENT === 'production' ? 'min.' : '').'js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables/datatables.min.js?v='.get_app_version()); ?>"></script>
<script src="<?php echo base_url('assets/plugins/app-build/moment.min.js'); ?>"></script>
<!--<script src="--><?php //echo base_url('node_modules/inputmask/dist/jquery.inputmask.bundle.js'); ?><!--"></script>-->
<!--<script src="--><?php //echo base_url('node_modules/inputmask/dist/inputmask/phone-codes/phone.js'); ?><!--"></script>-->
<!--<script src="--><?php //echo base_url('node_modules/inputmask/dist/inputmask/phone-codes/phone-be.js'); ?><!--"></script>-->
<!--<script src="--><?php //echo base_url('node_modules/inputmask/dist/inputmask/phone-codes/phone-ru.js'); ?><!--"></script>-->
<?php app_select_plugin_js($locale); ?>
<script src="<?php echo base_url('assets/plugins/tinymce/tinymce.min.js?v='.get_app_version()); ?>"></script>
<?php app_jquery_validation_plugin_js($locale); ?>
<?php if(get_option('dropbox_app_key') != ''){ ?>
<script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="<?php echo get_option('dropbox_app_key'); ?>"></script>
<?php } ?>
<?php if(isset($lightbox_assets)){ ?>
<script id="lightbox-js" src="<?php echo base_url('assets/plugins/lightbox/js/lightbox.min.js'); ?>"></script>
<?php } ?>
<?php if(isset($form_builder_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/form-builder/form-builder.js'); ?>"></script>
<?php } ?>
<?php if(isset($media_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/elFinder/js/elfinder.min.js'); ?>"></script>
<?php if(file_exists(FCPATH.'assets/plugins/elFinder/js/i18n/elfinder.'.get_media_locale($locale).'.js') && get_media_locale($locale) != 'en'){ ?>
<script src="<?php echo base_url('assets/plugins/elFinder/js/i18n/elfinder.'.get_media_locale($locale).'.js'); ?>"></script>
<?php } ?>
<?php } ?>
<?php if(isset($projects_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/jquery-comments/js/jquery-comments.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/gantt/js/jquery.fn.gantt.min.js'); ?>"></script>
<?php } ?>
<?php if(isset($circle_progress_asset)){ ?>
<script src="<?php echo base_url('assets/plugins/jquery-circle-progress/circle-progress.min.js'); ?>"></script>
<?php } ?>
<?php if(isset($accounting_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/accounting.js/accounting.min.js'); ?>"></script>
<?php } ?>
<?php if(isset($calendar_assets)){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/fullcalendar.min.js?v='.get_app_version()); ?>"></script>
<?php if(get_option('google_api_key') != ''){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/gcal.min.js'); ?>"></script>
<?php } ?>
<?php if(file_exists(FCPATH.'assets/plugins/fullcalendar/locale/'.$locale.'.js') && $locale != 'en'){ ?>
<script src="<?php echo base_url('assets/plugins/fullcalendar/locale/'.$locale.'.js'); ?>"></script>
<?php } ?>
<?php } ?>
<?php echo app_script('assets/js','main.js'); ?>
<?php echo app_script('assets/plugins/','maskedinput.min.js'); ?>
<?php echo app_script('assets/plugins/','jquery.cookie.js'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<?php echo app_script('node_modules/bootstrap-toggle/js','bootstrap-toggle.min.js'); ?>
<?php

/**
 * Global function for custom field of type hyperlink
 */
echo get_custom_fields_hyperlink_js_function(); ?>
<?php
/**
 * Outputs function global for ajax search
 */
app_admin_ajax_search_function();
?>
<?php
/**
 * Check for any alerts stored in session
 */
app_js_alerts();
?>
<?php
/**
 * Check pusher real time notifications
 */
if(get_option('pusher_realtime_notifications') == 1){ ?>
<script src="https://js.pusher.com/4.1.0/pusher.min.js"></script>
<script type="text/javascript">
   // Enable pusher logging - don't include this in production
   // Pusher.logToConsole = true;
   <?php $pusher_options = do_action('pusher_options',array());
   if(!isset($pusher_options['cluster']) && get_option('pusher_cluster') != ''){
     $pusher_options['cluster'] = get_option('pusher_cluster');
   } ?>
   var pusher_options = <?php echo json_encode($pusher_options); ?>;
   var pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", pusher_options);
   var channel = pusher.subscribe('notifications-channel-<?php echo get_staff_user_id(); ?>');
   channel.bind('notification', function(data) {
      fetch_notifications();
   });
</script>
    <script>
        var PAINEL = <?php echo PAINEL; ?>;
        var QUANTUM = 1;
        var INORTE = 2;
        var SUP_ESPERA = 1;
        var SUP_ATENDIMENTO = 2;
    </script>
<?php } ?>
<script>if(a == true){$("#master_notifications").modal("show");}</script>
<script>if(b == true){$("#pending_tickets").modal("show");}</script>
<script src="<?php echo base_url('assets/plugins/tinymce/plugins/codesample/prism.js'); ?>"></script>
<?php
/**
 * End users can inject any javascript/jquery code after all js is executed
 */
do_action('after_js_scripts_render');
?>
