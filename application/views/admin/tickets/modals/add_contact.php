<!-- Modal Contact -->
<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/tickets/add_contact/' . $customer_id, array('id' => 'contact-form', 'autocomplete' => 'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <?php echo form_hidden('userid', $customer_id); ?>
                        <?php echo form_hidden('lastname', ' '); ?>
                        <!--  For email exist check -->
                        <?php echo form_hidden('contactid', $contactid); ?>
                        <?php $value = (isset($contact) ? $contact->firstname : ''); ?>
                        <?php echo render_input('firstname', 'client_firstname', $value); ?>
                        <?php $value = (isset($contact) ? $contact->email : ''); ?>
                        <?php echo render_input('email', 'client_email', $value, 'email'); ?>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>"
                            autocomplete="off" data-form="#contact-form"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>

    $(function mascara2() {
        $('input[name="phonenumber"]').inputmask({
            "mask": "([0]99) 9999[9]-9999",
            greedy: false,
            skipOptionalPartCharacter: " "
        })

    });

</script>
