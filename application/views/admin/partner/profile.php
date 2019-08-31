<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 05/12/2017
 * Time: 12:44
 */init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="customer-heading-profile bold">#<?php echo $partner[0]['partner_id']." ".$partner[0]['firstname']; ?></h4>
                        <ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
                            <li class="active">
                                <a data-group="profile" href="#">
                                <i class="fa fa-user-circle menu-icon" aria-hidden="true"></i>Perfil</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="customer-profile-group-heading">Perfil</h4>
                        <div class="row">
                            <?php echo form_open($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off'));
                                if(!has_permission('partner','','edit'))
                                    echo "<fieldset disabled>";
                            ?>
                            <div class="additional"></div>
                            <div class="col-md-12">
                                <ul class="nav nav-tabs profile-tabs row customer-profile-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#" role="tab" data-toggle="tab">
                                            Detalhes do Parceiro
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input("partner_cnpj", "CNPJ", $partner[0]['partner_cnpj'], "text"); ?>
                                <?php echo render_input("firstname", "Razao Social", $partner[0]['firstname'], "text"); ?>

                            </div>
                            <div class="col-md-6">
                                <?php echo render_input("lastname", "Fantasia", $partner[0]['lastname'], "text"); ?>
                                <?php echo render_input("phonenumber", "Telefone", $partner[0]['phonenumber'], "text"); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input("cidade", "Cidade", $partner[0]['cidade'], "text"); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input("estado", "Estado", $partner[0]['estado'], "text"); ?>
                            </div>
                        </div>
<!--                        --><?php //echo json_encode($partner); ?>
                    </div>
                </div>
            </div>
            <?php if(has_permission('partner','','edit')) {?>
                <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
                    <button type="submit" class="btn btn-info">Salvar</button>
                </div>
            <?php }?>
            <?php
                if(!has_permission('partner','','edit'))
                    echo "</fieldset>";
                echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>