<div class="row">
    <div class="col-md-12">
        <div class="col-md-6 text-center">
            <div class="alert <?php if($dbversion == $dbcurrentversion){echo "alert-success";}else{echo "alert-danger";}?>">
                <h4 class="bold">Sua versão</h4>
                <p class="font-medium bold"><?php echo $dbcurrentversion;?></p>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <div class="alert alert-success">
                <h4 class="bold">Última Versão</h4>
                <p class="font-medium bold"><?php echo $dbversion;?></p>

                <input type="hidden" name="latest_version" value="196">
            </div>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div class="col-md-12 text-center">
<!--            <div class="alert alert-warning">-->
<!--                Before performing an update, it is <b>strongly recommended to create a full backup</b> of your current installation <b>(files and database)</b> and review the changelog.-->
<!--            </div>-->
            <?php if($dbversion == $dbcurrentversion){?>
                <h3 class="bold mbot20 text-success">
                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                    Você está usando a versão mais recente
                </h3>
            <?php } else {?>
                <h3 class="bold text-center mbot20">
                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                    Uma atualização está disponível
                </h3>
            <?php }?>
                <?php if($dbversion != $dbcurrentversion){ ?>
                    <a href="<?php echo admin_url("settings?group=update-db&update=true")?>" id="update_db" class="btn btn-success">Atualizar Banco de Dados</a>
                <?php }?>
                <hr>
                <div class="panel-body">
                    <table class="col-md-12">
                        <tr><td>Número da Versão</td><td>Descrição</td><td>Situação</td><td>Data</td><td>Opções</td></tr>
                        <tr><td colspan="5"><hr></td></tr>
                        <?php foreach ($installed_versions as $versioni){?>
                            <tr>
                                <td>
                                    <?php echo $versioni['version']; ?>
                                </td>
                                <td>
                                    <?php echo $versioni['description']; ?>
                                </td>
                                <td>

                                    <?php
                                        if($versioni['installed'] == 1)
                                            echo '<span class="label inline-block" style="border:1px solid #84c529; color:#84c529">Instalada</span>';
                                        else
                                            echo '<span class="label inline-block" style="border:1px solid #ff2d42; color:#ff2d42">Não Instalada</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php echo \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $versioni['date'])->diffForHumans(); ?>
<!--                                            --><?php //echo $versioni['date']; ?>
                                </td>
                                <td>
                                    <button id="oopcoes-<?php echo $versioni['version']; ?>" href="#opcoes-<?php echo $versioni['version']; ?>" data-toggle="collapse" data-parent="#options" class="btn btn-success collapsed">+</button>
                                </td>
                            </tr>
                            <?php if(ENVIRONMENT != "production"){?>
                            <tr>
                                <td colspan="5">
                                    <div id="opcoes-<?php echo $versioni['version']; ?>" class="panel-collapse collapse" style="height: 0px;" aria-expanded="false">
                                        <div class="panel-body">
                                            <div class="col-md-4">
                                                <a href="<?php echo admin_url("settings?group=update-db&install=".$versioni['version']); ?>" class="btn btn-success">Instalar</a>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="<?php echo admin_url("settings?group=update-db&reinstall=".$versioni['version']); ?>" class="btn btn-warning">Reinstalar</a>
                                            </div>
                                            <div class="col-md-4">
                                                <a href="<?php echo admin_url("settings?group=update-db&uninstall=".$versioni['version']); ?>" class="btn btn-danger">Desinstalar</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php }?>
                        <tr>
                            <td colspan="5">
                                <hr>
                            </td>
                        </tr>
                        <?php }?>
                    </table>
                </div>
            <div id="update_messages" class="mtop25 text-left">
            </div>
        </div>
    </div>
</div>