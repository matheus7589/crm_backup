<div class="modal fade proposal-convert-modal" id="convert_to_estimate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xxl" role="document">
        <?php echo form_open('admin/proposals/convert_to_estimate/'.$proposal->id,array('id'=>'proposal_convert_to_estimate_form','class'=>'_transaction_form disable-on-submit')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close dismiss-proposal-convert-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('proposal_convert_to_estimate'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $this->load->view('admin/estimates/estimate_template'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default dismiss-proposal-convert-modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php $this->load->view('admin/invoice_items/item'); ?>
<script>
    init_ajax_search('customer','#clientid.ajax-search');
    custom_fields_hyperlink();
    init_selectpicker();
    init_datepicker();
    init_color_pickers();
    init_items_sortable();
    init_tags_inputs();
    validate_estimate_form('#proposal_convert_to_estimate_form');
    <?php if($proposal->assigned != 0){ ?>
    $('#convert_to_estimate #sale_agent').selectpicker('val',<?php echo $proposal->assigned; ?>);
    <?php } ?>
    $('select[name="discount_type"]').selectpicker('val','<?php echo $proposal->discount_type; ?>');
    $('input[name="discount_percent"]').val('<?php echo $proposal->discount_percent; ?>');
    $('input[name="adjustment"]').val('<?php echo $proposal->adjustment; ?>');
    $('input[name="show_quantity_as"]').val('<?php echo $proposal->show_quantity_as; ?>').prop('checked',true).change();
    $('#convert_to_estimate #clientid').change();
</script>
