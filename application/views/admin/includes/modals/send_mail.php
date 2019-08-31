<div class="modal fade" id="SendMailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar Email</h4>
            </div>
            <?php echo form_open(admin_url('sendmail')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="mailmodal">
                        <?php echo render_input('tomodal','Para',$to??"");?>
                        <?php echo render_input('cabecamodal','Assunto',"",'text');?>
                        <?php echo render_textarea('messagemodal','Mensagem',$corpoemail??"");?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="mailmodalbntclose" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <a id="mailmodalbntsub" onclick="sendmailmodal(); return false;" class="btn btn-success">Enviar</a>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    var data = {};
    function disabled(op)
    {
        $("#tomodal")[0].disabled = op;
        $("#mailmodalbntclose")[0].disabled = op;
        $("#mailmodalbntsub")[0].disabled = op;
        $("#messagemodal")[0].disabled = op;
        $("#cabecamodal")[0].disabled = op;
    }
    function sendmailmodal() {
        disabled(true);
        data.tom = $("#tomodal").val();
        data.message = $('#messagemodal').val();
        data.subject = $("#cabecamodal").val();
        $.post(admin_url+'sendmail', data).done(function (response) {
            if(response == 'true'){
                $("#SendMailModal").modal("hide");
                alert_float('success','Email enviado com sucesso!!');
            }
            else
                alert_float('danger','Falha no envio do email.');
            disabled(false);
        });
    }
</script>