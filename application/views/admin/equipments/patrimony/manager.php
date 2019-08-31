<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 09/02/2018
 * Time: 15:49
 */

init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <div class="row">
                            <div class="col-md-12">
<!--                                <h3 style="margin: 0px;">Controle de Patrimonio</h3>-->
<!--                                <hr>-->
                                <a href="<?php echo admin_url("equipmens"); ?>" class="btn btn-info"><i class="fa fa-angle-double-left fa-1x" aria-hidden="true"></i></a>
                                <?php if(has_permission('equipments','','create')){?>
                                    <a onclick="cadast_patri()" class="btn btn-info new">Cadastrar Patrimônio</a>
                                <?php }?>
<!--                                <a onclick="saida()" class="btn btn-info new">Registrar Saída</a>-->
<!--                                <a href="--><?php //echo admin_url("equipmens/patrimony");?><!--" class="btn btn-info new pull-right">Patrimonio</a>-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s" style="padding-left: 0px;">
                    <div class="panel-body">
                        <div class="row">
                            <?php
                                echo render_datatable(array(
                                    '#',
                                    'Descrição',
                                    'Categoría',
                                    'Disponível para Sair',
                                    'Opções'
                                ),'patrimony');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(has_permission('equipments','','create')){?>
<!--Modal-Adicionar-Outros-->
<div class="modal fade" id="cadas-patri" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open(admin_url("equipmens/patrimony")); ?>
                        <div class="col-md-12">
<!--                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">-->
                            <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                <li class="nav-item active">
                                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                                        Geral
                                    </a>
                                </li>
<!--                                <li class="nav-item">-->
<!--                                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">-->
<!--                                        Documentação-->
<!--                                    </a>-->
<!--                                </li>-->
<!--                                <li class="nav-item">-->
<!--                                    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">-->
<!--                                        Contact-->
<!--                                    </a>-->
<!--                                </li>-->
                            </ul>
                            <hr style="margin-top: 2px;">
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active in" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                    <div class="col-md-6">
                                        <?php echo render_select('id_category',$categories,array("id","name"),'Categoria <a onclick="cadas_cate();">Adicionar</a>','',array("required"=>"true"),array(),'','',false);?>
                                    </div>
                                    <div class="col-md-6">
<!--                                        --><?php //echo render_input('quantidade','Quantidade','','number');?>
                                        <?php echo render_date_input('data_aquisicao','Data de Aquisição',Carbon\Carbon::now()->format("d/m/Y"),array("required"=>"true"));?>
                                    </div>
                                    <div class="col-md-12">
                                        <?php echo render_input('descricao','Descrição','','text',array("required"=>"true"));?>
                                        <?php echo render_textarea('caracteristicas','Características');?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_select('forma_ingresso',array(array("name"=>"Compra"),array("name"=>"Doação")),array("name","name"),'Forma de Ingresso','',array(),array(),'','',false);?>
                                        <div class="form-group">
                                            <label for="rtl_support_admin" class="control-label clearfix">Tipo do Bem</label>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" id="tipo_bem_op_1" name="tipo_bem" value="1" checked="">
                                                <label for="tipo_bem_op_1">
                                                    Móvel
                                                </label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" id="tipo_bem_op_2" name="tipo_bem" value="2">
                                                <label for="tipo_bem_op_2">
                                                    Imóvel
                                                </label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" id="tipo_bem_op_3" name="tipo_bem" value="3">
                                                <label for="tipo_bem_op_3">
                                                    Semovente
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('valor','Valor','','number');?>
                                        <div class="form-group">
                                            <label for="rtl_support_admin" class="control-label clearfix">Plaquetável</label>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" id="plaqueta_1" name="plaquetavel" value="1">
                                                <label for="plaqueta_1">
                                                    Sim
                                                </label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" id="plaqueta_2" name="plaquetavel" value="0" checked="">
                                                <label for="plaqueta_2">
                                                    Não
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

                                </div>
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-default"><?php echo _l('close'); ?></button>
                        <button type="submit" class="btn btn-success"><?php echo _l('submit'); ?></button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--Modal-Adicionar-Outros-->
<div class="modal fade" id="cadascate" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar Categoria</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open(admin_url("equipmens/patrimony/category")); ?>
                        <div class="col-md-12">
                            <?php echo render_input('name','Nome','','text',array("required"=>"true")); ?>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="cadas_cate('return')" class="btn btn-default">Voltar</button>
<!--                    Coloquei o submit'zão pq precisa atulizar a página para aparecer-->
                    <button type="submit" class="btn btn-success"><?php echo _l('submit'); ?></button>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php }?>
<?php init_tail(); ?>
<script>
    function cadast_patri() {
        $("#cadas-patri").modal("show");
    }
    $(function() {
        initDataTable('.table-patrimony', admin_url+"equipmens?type=patrimony");
    });
    function cadas_cate(type) {
        if(type == "return"){
            $("#cadas-patri").modal("show");
            $("#cadascate").modal("hide");
        }
        else {
            $("#cadas-patri").modal("hide");
            $("#cadascate").modal("show");
        }
    }
    function verifica_requireds()
    {
        var form = $("#equipments_reg_form");
        for(i = 0; i < form[0].length; i++)
        {
            if(form[0][i].required == true && form[0][i].value == "")
            {
                console.log("entrou required");
                alert_float("danger","Campo obrigatório em branco.");
                form[0][i].focus();
                return false;
            }
        }
        return true;
    }
</script>
