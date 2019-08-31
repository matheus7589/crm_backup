<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento2
 * Date: 05/01/2018
 * Time: 15:45
 */
if($codigo_ativacao['cliCodAtivacao'] == ''){
    $bloqueado = false;
}else{
    $bloqueado = true;
}

?>

<style type="text/css">
    textarea[readonly].default-cursor {
        cursor: default;
    }
</style>

<div class="panel_s">
    <div class="panel-body">
        <div class="alert alert-<?php echo $bloqueado == true ? 'danger' : 'info'; ?> text-center">
            <?php echo $bloqueado == true ? "Cliente Bloqueado!!" : "Cliente Liberado"; ?>
        </div>
    </div>
</div>

<div class="panel_s">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h4 class="customer-profile-group-heading"><?php echo "Gerar Código de Ativação"; ?></h4>
                <div class="col-md-6">
                    <?php echo render_date_input('date_activation', 'Data de Ativação'); ?>
                    <button class="btn btn-info mbot25" onclick="GerarCodigoAtivacao(<?php echo $client->userid; ?>);" data-toggle="modal" data-target="#"><i class="fa fa-lock"
                                                                                               aria-hidden="true"></i> <?php echo "Gerar Código de Ativação"; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel_s">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h4 class="customer-profile-group-heading-math"><?php echo "Gerar LUM"; ?></h4>
                <div class="col-md-6">
                    <?php echo render_date_input('date_lum', 'Data da LUM'); ?>
                    <button class="btn btn-info mbot25" onclick="GerarLum(<?php echo $client->userid; ?>);" data-toggle="modal" data-target="#"><i class="fa fa-lock"
                                                                                               aria-hidden="true"></i> <?php echo "Gerar LUM"; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel_s">
    <div class="panel-body">
        <div class="row text-center">
            <div class="col-md-12">
                <div class="col-md-6">
                    <button class="btn btn-info mbot25 mtop35" data-toggle="modal" data-target="#"><i
                                class="fa fa-lock" aria-hidden="true"></i> <?php echo "Ativar"; ?></button>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-info mbot25 mtop35" data-toggle="modal" data-target="#"><i
                                class="fa fa-lock" aria-hidden="true"></i> <?php echo "Bloquear"; ?></button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="panel_s">
    <div class="panel-body">
        <div class="form-group">
            <label for="sql">SQL:</label>
            <textarea class="form-control default-cursor" style="font-size: large" rows="5"  id="sql" readonly><?php echo $codigo_ativacao['cliCodAtivacao']; ?></textarea>
        </div>
    </div>
</div>

<script>
    function GerarCodigoAtivacao(userid) {
        var date = $('input[name="date_activation"').val();
        if(!empty(date)){
            var data = {};
            // data.date = date.replace(/\//g, '');
            data.date = date;
            data.userid = userid;
            $.post(admin_url+'clients/activation', data).done(function (response) {
                response = JSON.parse(response);
                alert(response.message);
            });
        }else{
            alert('Campo de data vazio, preencha com a data de ativação do cliente!');
        }

    }

    function GerarLum(userid) {
        var date = $('input[name="date_lum"').val();
        if(!empty(date)){
            var data = {};
            // data.date = date.replace(/\//g, '');
            data.date = date;
            data.userid = userid;
            $.post(admin_url+'clients/gerar_lum', data).done(function (response) {
                response = JSON.parse(response);
                alert(response.message);
            });
        }else{
            alert('Campo de data vazio, preencha com a data de ativação do cliente!');
        }
    }

    //caso o readonly impessa de selecionar o texto inside
    // $('textarea[readonly]').focus(function(){
    //     this.select();
    // });
</script>
