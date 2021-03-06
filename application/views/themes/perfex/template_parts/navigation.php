<nav class="navbar navbar-default">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <?php get_company_logo('','navbar-brand'); ?>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <?php if((get_option('use_knowledge_base') == 1 && !is_client_logged_in() && get_option('knowledge_base_without_registration') == 1) || (get_option('use_knowledge_base') == 1 && is_client_logged_in())){ ?>
                <li><a href="<?php echo site_url('knowledge_base'); ?>"><?php echo _l('clients_nav_kb'); ?></a></li>
                <?php } ?>
                <li><a href="<?php echo site_url('movies'); ?>">Vídeos</a></li>
                <?php if(!is_client_logged_in()){ ?>
                <li class="customers-nav-item-login"><a href="<?php echo site_url('clients/login'); ?>"><?php echo _l('clients_nav_login'); ?></a></li>
                <?php if(get_option('allow_registration') == 1){ ?>
                <li><a href="<?php echo site_url('clients/register'); ?>"><?php echo _l('clients_nav_register'); ?></a></li>
                <?php } ?>
                <?php } else { ?>
                <?php if(has_contact_permission('projects')){ ?>
                <li><a href="<?php echo site_url('clients/projects'); ?>"><?php echo _l('clients_nav_projects'); ?></a></li>
                <?php } ?>
                <?php if(has_contact_permission('invoices')){ ?>
                <li><a href="<?php echo site_url('clients/invoices'); ?>"><?php echo _l('clients_nav_invoices'); ?></a></li>
                <?php } ?>
                <?php if(has_contact_permission('contracts')){ ?>
                <li><a href="<?php echo site_url('clients/contracts'); ?>"><?php echo _l('clients_nav_contracts'); ?></a></li>
                <?php } ?>
                <?php if(has_contact_permission('estimates')){ ?>
                <li><a href="<?php echo site_url('clients/estimates'); ?>"><?php echo _l('clients_nav_estimates'); ?></a></li>
                <?php } ?>
                <?php if(has_contact_permission('proposals')){ ?>
                <li><a href="<?php echo site_url('clients/proposals'); ?>"><?php echo _l('clients_nav_proposals'); ?></a></li>
                <?php } ?>
                <?php if(has_contact_permission('support')){ ?>
                <li><a href="<?php echo site_url('clients/tickets'); ?>"><?php echo _l('clients_nav_support'); ?></a></li>
                <?php } ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <?php echo 'Empresas'; ?>
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu animated fadeIn">
                        <?php foreach ($list_clients as $list_client){ ?>
                            <li class="<?php if(get_client_user_id() == $list_client['companyid']) echo 'active'; ?>"><a href="#" onclick="set_session_data(<?php echo $list_client['companyid']; ?>); return false;">
                                    <?php echo $list_client['company']; ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="<?php echo contact_profile_image_url($contact->id,'thumb'); ?>" class="client-profile-image-small mright5">
                        <?php echo $contact->firstname . ' ' .$contact->lastname; ?>
                        <span class="caret"></span></a>
                        <ul class="dropdown-menu animated fadeIn">
                            <li><a href="<?php echo site_url('clients/profile'); ?>"><?php echo _l('clients_nav_profile'); ?></a></li>
                            <li><a href="<?php echo site_url('clients/company'); ?>"><?php echo _l('client_company_info'); ?></a></li>
                            <li>
                                <a href="<?php echo site_url('clients/announcements'); ?>"><?php echo _l('announcements'); ?>
                                   <?php if($total_undismissed_announcements != 0){ ?>
                                   <span class="badge"><?php echo $total_undismissed_announcements; ?></span>
                                   <?php } ?>
                               </a>
                           </li>
                           <?php if(is_primary_contact() && get_option('disable_language') == 0){
                            ?>
                            <li class="dropdown-submenu pull-left">
                               <a href="#" tabindex="-1"><?php echo _l('language'); ?></a>
                               <ul class="dropdown-menu dropdown-menu-left">
                                   <li class="<?php if($client->default_language == ""){echo 'active';} ?>"><a href="<?php echo site_url('clients/change_language'); ?>"><?php echo _l('system_default_string'); ?></a></li>
                                   <?php foreach($this->perfex_base->get_available_languages() as $user_lang) { ?>
                                   <li <?php if($client->default_language == $user_lang){echo 'class="active"';} ?>>
                                       <a href="<?php echo site_url('clients/change_language/'.$user_lang); ?>"><?php echo ucfirst($user_lang); ?></a>
                                   </li>
                                   <?php } ?>
                               </ul>
                           </li>
                           <?php } ?>
                           <li><a href="<?php echo site_url('clients/logout'); ?>"><?php echo _l('clients_nav_logout'); ?></a></li>
                       </ul>
                   </li>
                   <?php } ?>
               </ul>
           </div>
           <!-- /.navbar-collapse -->
       </div>
       <!-- /.container-fluid -->
   </nav>

<script>

    function set_session_data(clientid) {
        $.post(site_url + 'clients/set_user_data/' + clientid).done(function (response) {
            response = JSON.parse(response);
            window.location.reload();
            // window.location.href = site_url + 'clients/tickets';
        });
    }

</script>
