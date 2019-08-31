<?php
/**
 * Created by PhpStorm.
 * User: matheus.machado
 * Date: 02/04/2018
 * Time: 13:56
 */
?>

<div class="row">
    <div class="col-md-12">
        <div class="container">
            <div class="col-md-5 col-md-offset-3">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="customer-heading-profile bold mleft5"><i class="fa fa-sign-in fa-sm"></i> Acessar
                            Empresa</h4>
                        <ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">
                            <?php foreach ($companies as $company) { ?>
                                <li style="font-size: medium">
                                    <a href="#"
                                       onclick="set_session_data(<?php echo $company['companyid']; ?>); return false;"><i
                                                class=" fa fa-building fa-lg pull-right"
                                                aria-hidden="true"></i>
                                        <?php echo $company['company']; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function set_session_data(clientid) {
        $.post(site_url + 'clients/set_user_data/' + clientid).done(function (response) {
            response = JSON.parse(response);
            window.location.href = site_url + 'clients/tickets';
        });
    }

</script>
