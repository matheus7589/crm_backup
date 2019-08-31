<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 15/02/2018
 * Time: 17:05
 */

init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s _buttons">
                    <div class="panel-body">
                        <?php echo form_open(admin_url("movies/".$id)); ?>
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                <li class="nav-item active">
                                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                                        Geral
                                    </a>
                                </li>
<!--                                --><?php //if($movie->is_url != 1){ ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">
                                            Permissões
                                        </a>
                                    </li>
<!--                                --><?php //}?>
                            </ul>
<!--                        <hr style="margin-top: 2px;">-->
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade active in" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                    <?php if($movie->is_url == 1){
                                        echo render_input("filename","URL do Vídeo",$movie->filename,'url',array("required"=>"true"));
                                    }?>
                                    <?php echo render_input("title","Titulo",$movie->title,'text',array("required"=>"true")); ?>
                                    <div class="col-md-6" style="padding-left: 0px;">
                                        <?php echo render_select("category",$categories,array("categoryid","name"),"Categoria",$movie->idcategory,array("required"=>"true")); ?>
                                    </div>
                                    <div class="col-md-6" style="padding-right: 0px;">
                                        <?php echo render_select("idsubcategoryfk",$subcategories,array("idsubcategory","subcategory"),"SubCategoria",$movie->idsubcategoryfk,array("required"=>"true"),array(),'','',false); ?>
                                    </div>
                                    <?php echo render_textarea("description","Descrição",$movie->description,array("required"=>"true")); ?>
<!--                                <div class="form-group">-->
<!--                                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> --><?php //echo _l('tags'); ?>
<!--                                    </label>-->
<!--                                    <input type="text" class="tagsinput" id="tags" name="tags" data-role="tagsinput">-->
<!--                                </div>-->
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                    <div class="form-group">
                                        <div class="col-md-6" style="padding-left: 0px;">
                                            <div class="checkbox checkbox-primary" id="perm-public-wrapper">
                                                <input type="checkbox" name="perm-public" id="perm-public" <?php if($movie->perm_public == 1){echo "checked='true'";}?>>
                                                <label data-toggle="tooltip" title="" for="perm-public" data-original-title="">Público</label>
                                            </div>
                                            <div class="checkbox checkbox-primary" id="perm-staff-wrapper">
                                                <input type="checkbox" name="perm-staff" id="perm-staff" <?php if($movie->perm_staff == 1){echo "checked='true'";}?>>
                                                <label data-toggle="tooltip" title="" for="perm-staff" data-original-title="">Funcionários</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="padding-right: 0px;">
                                            <div class="checkbox checkbox-primary" id="perm-clients-wrapper">
                                                <input type="checkbox" name="perm-clients" id="perm-clients" <?php if($movie->perm_clients == 1){echo "checked='true'";}?>>
                                                <label data-toggle="tooltip" title="" for="perm-clients" data-original-title="">Clientes</label>
                                            </div>
                                            <div class="checkbox checkbox-primary" id="perm-key-wrapper">
                                                <input type="checkbox" name="perm-key" id="perm-key" <?php if($movie->perm_key != NULL){echo "checked='true'";}?>>
                                                <label data-toggle="tooltip" title="" for="perm-key" data-original-title="">Chave de acesso</label>
                                            </div>
                                        </div>
                                        <?php echo render_select('departments[]', $departments, array('departmentid','name'),'Departamentos', explode(',',$movie->departments), array("multiple"=>"1"),array("id"=>"departments-wrapper"),'hide','',false); ?>
                                        <?php echo render_input('perm_key','Chave de Acesso <i class="fa fa-question-circle hide" data-toggle="tooltip" data-title="" data-original-title="" title=""></i>', ($movie->perm_key != NULL)? $movie->perm_key:md5(Carbon\Carbon::now()->timestamp),'text',array(),array("id"=>"key-wrapper")); ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">

                                </div>
                            </div>
                            <a href="<?php echo admin_url("movies");?>" class="btn btn-default">Voltar</a>
                            <button type="submit" class="btn btn-success"><?php echo _l('submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $("#category").change(function() {
        $.get( admin_url+"movies/subcategory?categoryid="+$(this).val(), function( data ) {
            $("#idsubcategoryfk")[0].innerHTML = data;
            $("#idsubcategoryfk").selectpicker("refresh");
        });
    });
    $(function () {
        check();
    });
    $("#perm-public").change(function() {
        check();
    });
    $("#perm-key").change(function() {
       check();
    });
    $("#perm-staff").change(function() {
        check();
    });
    function check() {
        if ($("#perm-staff")[0].checked == true)
            $("#departments-wrapper").removeClass("hide");
        else
            $("#departments-wrapper").addClass("hide");
        if ($("#perm-key")[0].checked == true)
            $("#key-wrapper").removeClass("hide");
        else
            $("#key-wrapper").addClass("hide");
        if($("#perm-public")[0].checked == true){
            $("#perm-clients-wrapper").addClass("hide");
            $("#perm-staff-wrapper").addClass("hide");
            $("#perm-key-wrapper").addClass("hide");
            $("#key-wrapper").addClass("hide");
        }
        else{
            $("#perm-clients-wrapper").removeClass("hide");
            $("#perm-staff-wrapper").removeClass("hide");
            $("#perm-key-wrapper").removeClass("hide");
            if ($("#perm-key")[0].checked == true)
                $("#key-wrapper").removeClass("hide");
        }
    }
</script>
