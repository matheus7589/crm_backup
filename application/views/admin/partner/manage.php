<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento 04
 * Date: 29/11/2017
 * Time: 14:40
 */

init_head();?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!--Inicio-Cabeçario-Parceiro-->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if(has_permission('partner','','create')){?>
                                <a href="<?php echo admin_url('partner/add'); ?>"
                                   class="btn btn-info mright5 test pull-left display-block">
                                    <?php echo 'Novo Parceiro'; ?>
                                </a>
                            <?php } if(has_permission('partner','','edit')){?>
                                <a href="<?php echo admin_url('partner/alter/0'); ?>"
                                   class="btn btn-info mright5 test pull-left display-block">
                                    <?php echo "Ir para ";if(PAINEL==1)echo "QUANTUM";else echo "INORTE"; ?>
                                </a>
                            <?php } ?>
                        </div>
                        <br/>
                        <hr class="hr-panel-heading" />
                        <div class="row mbot15">
                            <div class="col-md-12">
                                <h3 class="text-success no-margin"><?php echo 'Resumo dos Parceiros'; ?></h3>
                            </div>
                            <div class="col-md-2 col-xs-6 border-right">
                                <a href="#" onclick="dt_custom_view_partner('.table-partner', 2, '1');">
                                    <h3 class="bold"><?php echo total_rows('tblpartner', ('active=1')); ?></h3>
                                    <span class="text-success"><?php echo 'Parceiros Ativos'; ?></span>
                                </a>
                            </div>
                            <div class="col-md-2 col-xs-6 border-right">
                                <a href="#" onclick="dt_custom_view_partner('.table-partner', 2, '0');">
                                    <h3 class="bold"><?php echo total_rows('tblpartner', ('active=0')); ?></h3>
                                    <span class="text-danger"><?php echo _l('Parceiros Inativos'); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Fim-Cabeçario-Parceiro-->
                <!--Inicio-listagem-Parceiros-->
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                        $table_data = array(
                            'Nome',
                            'CNPJ',
                            'Ativo',
                            'Opções'
                        );
                        render_datatable($table_data,'partner');
                        ?>
                    </div>
                </div>
                <!--Fim-listagem-Parceiros-->
            </div>
        </div>
    </div>
</div>
<?php if(has_permission('partner','','delete')){ ?>
    <div class="modal fade" id="delete_partner" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <?php echo form_open(admin_url('partner/delete',array('delete_partner_form'))); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Excluir parceiro</h4>
                </div>
                <div class="modal-body">
                    <div class="delete_id">
                        <?php echo form_hidden('id'); ?>
                    </div>
                    <?php echo render_select('partner_dest',$partners,array('partner_id','lastname'),'Mover dados do parceiro para','',array(),array(),'','',false); ?>
                    <p>Tem certeza que deseja excluir o parceiro?</p>
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
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-partner', window.location.href);
        dt_custom_view_partner('.table-partner', 2, '1');
    });
   function delete_staff_member(id){
       $('#delete_partner').modal('show');
       $('#delete_partner .delete_id input').val(id);
   }

    function dt_custom_view_partner(table, column, val) {
        var tableApi = $(table).DataTable();
        tableApi.column(column).search(val).draw();
        tableApi.ajax.reload();
    }
</script>
