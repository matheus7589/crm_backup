<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1" />
    <?php if(get_option('favicon') != ''){ ?>
    <link href="<?php echo base_url('uploads/company/'.get_option('favicon')); ?>" rel="shortcut icon">
    <?php } ?>
    <title><?php if (isset($title)){ echo $title; } else { echo get_option('companyname'); } ?></title>
    <?php echo app_stylesheet('assets/css','reset.css'); ?>
    <link href='<?php echo base_url('assets/plugins/roboto/roboto.css'); ?>' rel='stylesheet'>
    <link href="<?php echo base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <?php if($isRTL === 'true'){ ?>
    <link href="<?php echo base_url('assets/plugins/bootstrap-arabic/css/bootstrap-arabic.min.css'); ?>" rel="stylesheet">
    <?php } ?>
    <link href="<?php echo base_url('assets/plugins/jquery-ui/jquery-ui.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/app-build/vendor.css?v='.get_app_version()); ?>" rel="stylesheet">
    <?php if(isset($calendar_assets)){ ?>
    <link href='<?php echo base_url('assets/plugins/fullcalendar/fullcalendar.min.css?v='.get_app_version()); ?>' rel='stylesheet' />
    <?php } ?>
    <?php if(isset($lightbox_assets)){ ?>
    <link id="lightbox-css" href='<?php echo base_url('assets/plugins/lightbox/css/lightbox.min.css'); ?>' rel='stylesheet' />
    <?php } ?>
    <?php if(isset($form_builder_assets)){ ?>
    <link href='<?php echo base_url('assets/plugins/form-builder/form-builder.min.css'); ?>' rel='stylesheet' />
    <?php } ?>
    <?php if(isset($projects_assets)){ ?>
    <link href='<?php echo base_url('assets/plugins/jquery-comments/css/jquery-comments.css'); ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/plugins/gantt/css/style.css'); ?>' rel='stylesheet' />
    <?php } ?>
     <?php if(isset($media_assets)){ ?>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url('assets/plugins/elFinder/css/elfinder.min.css'); ?>">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url('assets/plugins/elFinder/themes/windows-10/css/theme.css'); ?>">
    <?php } ?>
    <?php echo app_stylesheet('assets/css','style.css'); ?>
    <?php echo app_stylesheet('assets/css','modal.css'); ?>

    <?php if(file_exists(FCPATH.'assets/css/custom.css')){ ?>
    <link href="<?php echo base_url('assets/css/custom.css'); ?>" rel="stylesheet">
    <?php } ?>
    <?php echo app_stylesheet('assets/css','component.css'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <?php echo app_stylesheet('node_modules/bootstrap-toggle/css','bootstrap-toggle.min.css'); ?>

    <?php render_custom_styles(array('general','tabs','buttons','admin','modals','tags')); ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <?php render_admin_js_variables(); ?>
        <script>
            appLang['datatables'] = <?php echo json_encode(get_datatables_language_array()); ?>;

            var total_unread_notifications = <?php echo $unread_notifications; ?>,
            proposal_templates = <?php echo json_encode(get_proposal_templates()); ?>,
            availableTags = <?php echo json_encode(get_tags_clean()); ?>,
            availableTagsIds = <?php echo json_encode(get_tags_ids()); ?>,
            bs_fields = ['billing_street','billing_city','billing_state','billing_zip','billing_country','shipping_street','shipping_city','shipping_state','shipping_zip','shipping_country'],
            locale = '<?php echo $locale; ?>',
            isRTL = '<?php echo $isRTL; ?>',
            tinymce_lang = '<?php echo get_tinymce_language(get_locale_key($app_language)); ?>',
            months_json = '<?php echo json_encode(array(_l('January'),_l('February'),_l('March'),_l('April'),_l('May'),_l('June'),_l('July'),_l('August'),_l('September'),_l('October'),_l('November'),_l('December'))); ?>',
            _table_api,taskid,task_tracking_stats_data,taskAttachmentDropzone,leadAttachmentsDropzone,newsFeedDropzone,expensePreviewDropzone,autocheck_notifications_timer_id = 0,task_track_chart,cfh_popover_templates = {};
        </script>
    <link href='<?php echo base_url('assets/plugins/tinymce/plugins/codesample/css/prism.css?3'); ?>' rel='stylesheet'>
        <?php do_action('app_admin_head'); ?>
    </head>
    <body <?php if($isRTL === 'true'){ echo 'dir="rtl"';} ?> class="<?php echo 'page'.($this->uri->segment(2) ? '-'.$this->uri->segment(2) : '') . '-'.$this->uri->segment(1); ?> admin <?php if(isset($bodyclass)){echo $bodyclass . ' '; } ?><?php if($this->session->has_userdata('is_mobile') && $this->session->userdata('is_mobile') == true){echo 'hide-sidebar ';} ?><?php if($isRTL === 'true'){echo 'rtl';} ?>">
        <?php do_action('after_body_start'); ?>
