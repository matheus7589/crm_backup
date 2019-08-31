<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 02/01/2018
 * Time: 11:11
 */?>
<script>var a = false;</script>
<?php
    $dados = $this->db->query("SELECT description,id FROM `tblnotifications` WHERE `master` = '1' AND `touserid` = '".get_staff_user_id()."' AND master_read = '0'")->row();
    if($this->db->affected_rows() > 0){
?>
        <div class="modal fade" id="master_notifications" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><div id="conttent-modal-replies-title">Notificação</div></h4>
                    </div>
                    <div class="modal-body" id="conttent-modal-replies">
                        <h3 class="text-warning">
                            <?php echo $dados->description; ?>
                        </h3>
                        <a href="<?php echo admin_url("utilities/read_master_notification?id=".$dados->id)?>">Não mostrar mais</a>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
<script>var a = true;</script>
<?php } ?>
