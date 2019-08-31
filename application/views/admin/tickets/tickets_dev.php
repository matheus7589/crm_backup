<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 16/12/2017
 * Time: 10:21
 */

init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div id="summary">
                            <?php do_action('before_render_tickets_list_table'); ?>
                            <?php
                            $this->load->view('admin/tickets/summary_partner');
                            ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php
                        echo AdminTicketsTableStructure('tickets-partner', false); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<?php echo app_script('assets/js', 'tickets.js'); ?>

<script>

    $(function () {
        init_table_dev_only();

    });

</script>
