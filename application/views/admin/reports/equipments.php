<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 15/03/2018
 * Time: 16:57
 */

init_head();?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                            echo render_datatable(array(
                                '#',
                                'Tipo',
                                '1',
                                'Data de Entrada',
                                'Data de Saída'
                            ),'equipments');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        initDataTable('.table-equipments',admin_url+'reports/equipments');
    });
</script>