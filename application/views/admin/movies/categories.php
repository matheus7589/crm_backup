<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 16/02/2018
 * Time: 13:59
 */

init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?php echo admin_url("movies");?>" class="btn btn-info"><i class="fa fa-angle-double-left fa-1x" aria-hidden="true"></i></a>
<!--                        <h4>Categorías</h4>-->
                        <a class="btn btn-info" onclick="$('#new-cate').modal('show'); return false;">Nova Categoría</a>
<!--                        <hr>-->
                        <table class="table table-striped dataTable no-footer dtr-inline">
                            <tr>
                                <td>
                                    <h4>Categoría</h4>
                                </td>
                                <td>

                                </td>
                            </tr>
                            <?php foreach ($categories as $category){?>
                                <tr>
                                    <td>
                                        <?php echo $category['name']; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo admin_url("movies/categories?type=delete_cate&id=".$category['categoryid']);?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                                    </td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <a class="btn btn-info" onclick="$('#new-sub-cate').modal('show'); return false;">Nova Sub-Categoría</a>
<!--                        <hr>-->
                        <table class="table table-striped dataTable no-footer dtr-inline">
                            <tr>
                                <td>
                                    <h4>Sub-Categoría</h4>
                                </td>
                                <td>
                                    <h4>Categoría</h4>
                                </td>
                                <td>

                                </td>
                            </tr>
                            <?php foreach ($sub_categories as $sub_category){?>
                                <tr>
                                    <td>
                                        <?php echo $sub_category['subcategory']; ?>
                                    </td>
                                    <td>
                                        <?php echo $sub_category['category']; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo admin_url("movies/categories?type=delete_subcate&id=".$sub_category['idsubcategory']);?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                                    </td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="new-cate" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar Categoría</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            echo "<form method='post' action='".admin_url("movies/categories")."'>";
                            echo form_hidden("type","add_categorie");
                            echo render_input("name","Categoría");
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info">Salvar</button>
                <?php echo form_close();?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="modal fade" id="new-sub-cate" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar Sub-Categoría</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo "<form method='post' action='".admin_url("movies/categories")."'>";
                        echo render_select("categoryfk",$categories,array("categoryid","name"),"Categoria",'',array("required"=>"true"),array(),'','',false);
                        echo form_hidden("type","add_subcategorie");
                        echo render_input("subcategory","Sub-Categoría");
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info">Salvar</button>
                <?php echo form_close();?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
