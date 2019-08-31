<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento 04
 * Date: 29/11/2017
 * Time: 14:40
 */
init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <?php echo form_open(admin_url('partner/add')); ?>
                        <div class="panel-body">
                            <?php echo render_input('fantasia', 'Fantasia', '', 'text', array('required' => 'true')); ?>
                            <?php echo render_input('socialr', 'Razão Social', '', 'text', array('required' => 'true')); ?>
                            <?php echo render_input('email', 'Email', '', 'text', array('required' => 'true')); ?>
                            <?php echo render_input('phonenumber', 'Telefone', '', 'text', array('required' => 'true')); ?>
                            <?php echo render_input('cidade', 'Cidade', '', 'text', array('required' => 'true')); ?>
                            <?php echo render_input('estado', 'Estado', '', 'text', array('required' => 'true')); ?>
                            <?php echo render_input('partner_cnpj', 'CNPJ', '', 'text', array('required' => 'true')); ?>
                        </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>