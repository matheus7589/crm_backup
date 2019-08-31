<?php
/**
 * Created by PhpStorm.
 * User: matheus.machado
 * Date: 29/03/2018
 * Time: 10:42
 */
?>
<div class="modal fade" id="connect_contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo "Selecione um Contato"; ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
<!--                        --><?php //echo render_select('contact', $contacts, array('id', 'firstname'), 'Selecione um Contato'); ?>
                        <select name="contactid" required="true" id="contactid" class="ajax-search"
                                data-width="100%" data-live-search="true"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" data-dismiss="modal" onclick="save_connect_contact(<?php echo $client_id; ?>, $('#contactid').val()); return false;" class="btn btn-info"><?php echo "Salvar"; ?></button>
            </div>
        </div>
    </div>
</div>
