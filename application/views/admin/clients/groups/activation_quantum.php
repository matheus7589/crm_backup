<?php
    $senhas = QuantumPrivateSenha::GetSenhas($client->cnpj_or_cpf);
?>
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Senha</a></li>
    <li><a data-toggle="tab" href="#menu1">Modulos</a></li>
<!--    <li><a data-toggle="tab" href="#menu2">Menu 2</a></li>-->
</ul>
<div class="tab-content">
    <div id="home" class="tab-pane fade in active">
    <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="customer-profile-group-heading-math">Gerar Senha de Ativação</h4>
                    <div class="col-md-6">
                        <?php echo render_date_input('datein', 'Data de Inicio','',array("required"=>"true")); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_date_input('dateen', 'Data Final','',array("required"=>"true")); ?>
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-info mbot25" id="gerarsenha" onclick="gerarsenha('getpass');" data-toggle="modal" data-target="#">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                            Gerar Senha
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
                    <h4 class="customer-profile-group-heading-math">Outras senhas</h4>
                    <?php if(count($senhas) > 0){ ?>
                        <div class="table-responsive">
                            <table class="table dt-table">
                                <thead>
                                    <th>#</th>
                                    <th>Data Geração</th>
                                    <th>Senha</th>
                                    <th>Usuário</th>
                                    <th>Data Inicial</th>
                                    <th>Data Final</th>
                                </thead>
                                <tbody>
                                <?php foreach($senhas as $senha){ ?>
                                    <tr>
                                        <td>
                                            <?php echo $senha["Id"]; ?>
                                        </td>
                                        <td>
                                            <?php echo date_format(date_create($senha['datageracao']),'d/m/Y h:i'); ?>
                                        </td>
                                        <td>
                                            <?php echo $senha['senha']; ?>
                                        </td>
                                        <td>
                                            <?php echo $senha['usuariogerou']; ?>
                                        </td>
                                        <td>
                                            <?php echo date_format(date_create($senha['datainicial']),'d/m/Y'); ?>
                                        </td>
                                        <td>
                                            <?php echo date_format(date_create($senha['datafinal']),'d/m/Y'); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <p class="no-margin">Nenhuma senha.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php
        $modules = array(
            "moduloquantumretaguardasemsped"=>"Quantum Sem SPED",
            "moduloquantumretaguardacomsped"=>"Quantum Com SPED",
            "modulopafecf"=>"PafEcf",
            "modulodavprevenda"=>"Dav-PreVenda",
            "modulonfe"=>"NFe",
            "modulonfse"=>"NFSe",
            "modulonfce"=>"NFCe",
            "modulocte"=>"CTe",
            "modulonaofiscal"=>"Não Fiscal (Sem Ecf)",
            "modulolite"=>"Quantum Lite",
            "modulomdfe"=>"MDFe",
        );
        $modules_clients = QuantumLiberaModulos::getLicenca($client->cnpj_or_cpf);
    ?>
    <div id="menu1" class="tab-pane fade">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo form_open(admin_url("clients/senha/quantum")); ?>
                            <input type="hidden" id="cnpj" name="cnpj" value="<?php echo $client->cnpj_or_cpf;?>">
                            <input type="hidden" id="userid" name="userid" value="<?php echo $client->userid;?>">
                            <input type="hidden" id="tipo" name="tipo" value="modulos">
                            <h4 class="customer-profile-group-heading-math">Módulos</h4>
                            <?php
                                foreach($modules as $key => $module)
                                {
                                    ?>
                                    <div class="checkbox checkbox-info mbot20 no-mtop col-md-4">
                                        <input type="checkbox" name="modulos[<?php echo $key;?>]" value="1" id="<?php echo $key;?>" <?php if(boolval($modules_clients[$key])){echo " checked='true'";}?>>
                                        <label for="<?php echo $key;?>"><?php echo $module;?></label>
                                    </div>
                                    <?php
                                }
                            ?>
                            <div class="col-md-12">
                                <input type="submit" class="btn btn-info pull-right" value="Salvar">
                            </div>
                        <?php echo form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Inicio-Modal-->
<div class="modal fade" id="senha" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><div id="conttent-modal-replies-title">Senha: <span id="senhagerada"></span></div></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <h4>Deseja enviar a senha para o site??</h4>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Voltar</button>
                <button type="button" onclick="gerarsenha('enviar');" id="bntenvialmodal" class="btn btn-info">Enviar</button>
                <?php echo form_close(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--Fim-Modal-->

<?php
    init_tail();
    if($this->input->get("suc")) {
        set_alert("success", $this->input->get("suc"));
        header("location:".admin_url("clients/client/".$client->userid."?group=activation_quantum"));
    }
?>
<script>
    var userid = <?php echo $client->userid; ?>;
    function disabled(type) {
        $("#datein")[0].disabled = type;
        $("#dateen")[0].disabled = type;
        $("#gerarsenha")[0].disabled = type;
        $("#bntenvialmodal")[0].disabled = type;
    }
    function gerarsenha(tipo) {
        var data = {};
        data.datai = $("#datein")[0].value;
        data.dataf = $("#dateen")[0].value;
        if(tipo == "getpass")
            data.tipo = "ajax-gerasenha";
        else if(tipo == "enviar")
            data.tipo = "ajax-enviasenhasite";

        data.clienttipo = "userid";
        data.userid = userid;
        if(data.datai == "" || data.dataf == "")
        {
            alert_float("danger","Campo obrigatório em branco.");
            return false;
        }
        disabled(true);

        $.post(admin_url+'clients/senha/quantum', data).done(function (response) {
            disabled(false);
            response = JSON.parse(response);
            if(tipo == "getpass") {
                $("#senhagerada").text(response.senha);
                $("#senha").modal("show");

            }
            else if(tipo == "enviar") {
                if(response.error_code == 0) {
                    $("#senha").modal("hide");
                    // alert_float("success", response.mensagem);
                    window.location.href = window.location.href+"&suc="+response.mensagem;
                }
                else {
                    alert_float("danger", response.mensagem);
                }
            }
        });
    }
</script>