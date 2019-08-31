<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 14/02/2018
 * Time: 14:20
 */

if($admin)
    init_head();
?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s _buttons">
                    <div class="panel-body">
                        <?php if(has_permission_video('movies','create')){?>
                            <a class="btn btn-success" onclick="$('#new-movie').modal('show'); return false;">Adicionar Vídeo</a>
                        <?php }?>
                        <a class="btn btn-info" onclick="$('#key-movie').modal('show'); return false;">Código de Acesso</a>
                        <?php if(has_permission_video('movies','create')){?>
                            <a class="btn btn-info" href="<?php echo admin_url("movies/categories");?>">Categorías</a>
                            <a class="btn btn-default btn-with-tooltip" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-bar-chart"></i>
                            </a>
                            <div class="collapse" id="collapseExample">
                                <div class="card card-body">
                                    <hr>
                                    <h4>
                                        Espaço Livre:
                                        <?php
                                            $bytes = disk_free_space(__DIR__);
                                            $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
                                            $base = 1024;
                                            $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
                                            echo sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />';
                                        ?>
                                    </h4>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                        $key = "";
                        if($this->input->get("key"))
                            $key = $this->input->get("key");
                        foreach ($movies as $movie){
                        if(has_permission_video($movie['idmovie'],$key)){ ?>
                            <div class="panel-group col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" href="#collapse<?php echo $movie['idmovie'];?>" id="titulo<?php echo $movie['idmovie'];?>"><?php echo $movie['title'];?></a>
                                            <hr>
                                            <?php if($admin){?>
                                                <a href="<?php echo admin_url("movies/player/".$movie['idmovie'].($key = $this->input->get("key")?"?key=".$key = $this->input->get("key"):""));?>" target="_blank">Assistir</a>
                                            <?php }else{?>
                                                <a href="<?php echo base_url("movies/player/".$movie['idmovie'].($key = $this->input->get("key")?"?key=".$key = $this->input->get("key"):""));?>" target="_blank">Assistir</a>
                                            <?php }?>
                                        </h4>
                                    </div>
                                    <div id="collapse<?php echo $movie['idmovie'];?>" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <?php echo $movie['description'];?>
                                            <?php if(has_permission_video('midia','edit')){?>
                                                <hr>
                                                <h4 class="panel-title">
                                                    <?php if(has_permission_video('movies','edit')){?>
                                                        <a href="<?php echo admin_url("movies/".$movie['idmovie']);?>">Editar</a>
                                                    <?php }?>
                                                    &nbsp;&nbsp;&nbsp;
                                                    <?php if(has_permission_video('movies','delete')){?>
                                                        <a href="#" onclick="drop_video(<?php echo $movie['idmovie'];?>); return false;">Excluir</a>
                                                    <?php }?>
                                                </h4>
                                                <?php }?>
                                            <hr>
                                            <?php echo "<p>".$movie['category']." -> ".$movie['subcategory']."</p>".Carbon\Carbon::parse($movie['date'])->format("d/m/Y H:i:s");?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(has_permission_video('movies','create')){?>
    <div class="modal fade" id="new-movie" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Novo Vídeo</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php echo form_open_multipart(admin_url("movies")); ?>
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                <li class="nav-item active">
                                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                                        Geral
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">
                                        Permissões
                                    </a>
                                </li>
    <!--                        <li class="nav-item">-->
    <!--                            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">-->
    <!--                                Contact-->
    <!--                            </a>-->
    <!--                        </li>-->
                            </ul>
    <!--                        <hr style="margin-top: 2px;">-->
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade active in" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                    <div class="form-group">
                                        <label for="attachment" class="control-label">Vídeo</label>
                                        <div class="input-group">
                                            <input type="file" extension="mp4" filesize="20971520" class="form-control" id="video" name="video" required="true">
                                            <span class="input-group-btn">
                                                <div class="checkbox checkbox-primary" id="perm-public-wrapper">
                                                    <input type="checkbox" name="is-url" id="is-url">
                                                    <label data-toggle="tooltip" title="" for="is-url" data-original-title="" data-title="Caso o vídeo seja do youtube ultilizar o modelo 'youtube.com/embed/video'">URL</label>
                                                </div>
<!--                                                <button class="btn btn-success add_more_attachments p8-half" type="button">-->
<!--                                                    URL?-->
<!--                                                </button>-->
                                            </span>
                                        </div>
                                    </div>
                                    <?php echo render_input("title","Titulo",'','text',array("required"=>"true")); ?>
                                    <div class="col-md-6" style="padding-left: 0px;">
                                        <?php echo render_select("category",$categories,array("categoryid","name"),"Categoria",'',array("required"=>"true")); ?>
                                    </div>
                                    <div class="col-md-6" style="padding-right: 0px;">
                                        <?php echo render_select("idsubcategoryfk",array(),array(),"SubCategoria",'',array("required"=>"true")); ?>
                                    </div>
                                    <?php echo render_textarea("description","Descrição",'',array("required"=>"true")); ?>
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
                                                <input type="checkbox" name="perm-public" id="perm-public">
                                                <label data-toggle="tooltip" title="" for="perm-public" data-original-title="">Público</label>
                                            </div>
                                            <div class="checkbox checkbox-primary" id="perm-staff-wrapper">
                                                <input type="checkbox" name="perm-staff" id="perm-staff">
                                                <label data-toggle="tooltip" title="" for="perm-staff" data-original-title="">Funcionários</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="padding-right: 0px;">
                                            <div class="checkbox checkbox-primary" id="perm-clients-wrapper">
                                                <input type="checkbox" name="perm-clients" id="perm-clients">
                                                <label data-toggle="tooltip" title="" for="perm-clients" data-original-title="">Clientes</label>
                                            </div>
                                            <div class="checkbox checkbox-primary" id="perm-key-wrapper">
                                                <input type="checkbox" name="perm-key" id="perm-key">
                                                <label data-toggle="tooltip" title="" for="perm-key" data-original-title="">Chave de acesso</label>
                                            </div>
                                        </div>
                                        <?php echo render_select('departments[]', $departments, array('departmentid','name'),'Departamentos', '', array("multiple"=>"true"),array("id"=>"departments-wrapper"),'hide','',false); ?>
                                        <?php echo render_input('perm_key','Chave de Acesso <i class="fa fa-question-circle hide" data-toggle="tooltip" data-title="" data-original-title="" title=""></i>',md5(Carbon\Carbon::now()->timestamp),'text',array(),array("id"=>"key-wrapper"),'hide'); ?>
                                    </div>
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
<?php }?>

<div class="modal fade" id="key-movie" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Acessar vídeo por código</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            if($admin)
                                echo "<form method='get' action='".admin_url("movies")."'>";
                            else
                                echo "<form method='get' action='".base_url("movies")."'>";
                        ?>
                        <?php echo render_input("key","Chave de Acesso");?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info">Acessar</button>
                <?php echo form_close();?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php if(has_permission_video('movies','delete')){?>
    <div class="modal fade" id="delete_video" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <?php echo form_open(admin_url('movies/delete')); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Excluir Vídeo</h4>
                </div>
                <div class="modal-body">
                    <div class="delete_id">
                        <?php echo form_hidden('idvideo'); ?>
                    </div>
                    <p>Tem certeza que deseja excluir o vídeo?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-danger _delete"><?php echo _l('confirm'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php }?>
<?php init_tail();?>
<?php if(has_permission_video('movies','edit')){?>
    <script>
        $("#category").change(function() {
            $.get( admin_url+"movies/subcategory?categoryid="+$(this).val(), function( data ) {
                $("#idsubcategoryfk")[0].innerHTML = data;
                $("#idsubcategoryfk").selectpicker("refresh");
            });
        });
        $("#perm-public").change(function() {
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
        });
        $("#perm-key").change(function() {
            if ($("#perm-key")[0].checked == true)
                $("#key-wrapper").removeClass("hide");
            else
                $("#key-wrapper").addClass("hide");
        });
        $("#perm-staff").change(function() {
            if ($("#perm-staff")[0].checked == true)
                $("#departments-wrapper").removeClass("hide");
            else
                $("#departments-wrapper").addClass("hide");
        });

        function drop_video(id) {
            $("[name='idvideo']").val(id);
            $("#delete_video").modal("show");
        }
        $("#is-url").change(function() {
            if ($("#is-url")[0].checked == true)
                $("#video")[0].type = "url";
            else
                $("#video")[0].type = "file";
        });
    </script>
<?php }?>
