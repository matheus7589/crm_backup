<script>var b = false;</script>
<?php
/**
 * Created by PhpStorm.
 * User: matheus.machado
 * Date: 28/02/2018
 * Time: 09:04
 */
if($this->session->userdata('first_pending_notification') == true){
    $this->db->query("SELECT ticketid, subject FROM tbltickets WHERE assigned=" . get_staff_user_id() . " AND partner_id = " . get_staff_partner_id() . " AND status = 18;")->row();
    if($this->db->affected_rows() > 0 && get_staff_role() == 4){ ?>
        <div class="modal fade" id="pending_tickets" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><div id="conttent-modal-replies-title">Tickets Pendentes</div></h4>
                    </div>
                    <div class="modal-body" id="conttent-modal-pending">
                        <?php
//                            echo form_hidden('my_tickets', '1');
                            echo form_hidden('pending_only', '1');
                            echo AdminTicketsTableStructure('pending-table', false);
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    <script>var b = true;</script>
<?php }
    $this->session->set_userdata('first_pending_notification', false);
}?>

