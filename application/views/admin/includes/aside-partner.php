<aside id="menu" class="sidebar">
    <ul class="nav metis-menu" id="side-menu">
        <li class="dashboard_user">
            <?php echo _l('welcome_top',$current_user->firstname); ?> <i class="fa fa-power-off top-left-logout pull-right" data-toggle="tooltip" data-title="Sair" data-placement="left" onclick="logout(); return false;"></i>
        </li>
        <li class="quick-links">
            <div class="dropdown dropdown-quick-links">
                <a href="#" class="dropdown-toggle" id="dropdownQuickLinks" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="fa fa-gavel" aria-hidden="true"></i>
                </a>
            </div>
        </li>
        <li class="menu-item-dashboard">
            <a href="<?php echo APP_BASE_URL; ?>admin/" aria-expanded="false"><i class="fa fa-tachometer menu-icon"></i>
                Painel                  </a>
        </li>
        <?php if(is_admin() || PAINEL == INORTE){ ?>
        <li class="menu-item-customers">
            <a href="<?php echo APP_BASE_URL; ?>admin/clients" aria-expanded="false"><i class="fa fa-users menu-icon"></i>
                Clientes                  </a>
        </li>
        <?php } ?>
        <li class="menu-item-tickets">
            <a href="<?php echo APP_BASE_URL; ?>admin/tickets" aria-expanded="false"><i class="fa fa-ticket menu-icon"></i>
                Suporte                  </a>
        </li>
        <?php if(PAINEL == INORTE){ ?>
        <li class="menu-item-tickets-develop">
            <a href="<?php echo APP_BASE_URL; ?>admin/tickets/tickets_dev" aria-expanded="false"><i class="fa fa-code menu-icon"></i>
                DEV Tickets                  </a>
        </li>
        <?php } ?>
        <li class="menu-item-leads">
            <a href="<?php echo APP_BASE_URL; ?>admin/leads" aria-expanded="false"><i class="fa fa-tty menu-icon"></i>
                Leads                  </a>
        </li>
        <li class="menu-item-knowledge-base">
            <a href="<?php echo admin_url('knowledge_base'); ?>" aria-expanded="false"><i class="fa fa-folder-open-o menu-icon"></i>
                Base de Conhecimento
            </a>
        </li>

        <li class="menu-item-utilities">
            <a href="#" aria-expanded="false"><i class="fa fa-cogs menu-icon"></i>
                Utilidades                  <span class="fa arrow"></span>
            </a>
            <ul class="nav nav-second-level collapse" aria-expanded="false">
                <li class="sub-menu-item-changelog"><a href="<?php echo APP_BASE_URL; ?>admin/utilities/changelog">
                        Mudancas do Sistema</a>
                </li>
            </ul>
        </li>
        <li class="menu-item-reports">
            <a href="#" aria-expanded="false"><i class="fa fa-area-chart menu-icon"></i>
                Relatórios                  <span class="fa arrow"></span>
            </a>
            <ul class="nav nav-second-level collapse" aria-expanded="false">
                <li class="sub-menu-item-child-attendance_report"><a href="<?php echo APP_BASE_URL; ?>admin/utilities/attendance_report">
                        Relatório de Atendimentos</a>
                </li>
            </ul>
        </li>
        <?php if(is_admin()){ ?>
        <li class="menu-item-reports">
            <a href="#" aria-expanded="false"><i class="fa fa-cog menu-icon"></i>
                Definições                  <span class="fa arrow"></span>
            </a>
            <ul class="nav nav-second-level collapse" aria-expanded="false">
                <li class="sub-menu-item-child-attendance_report"><a href="<?php echo APP_BASE_URL; ?>admin/staff">
                        Usuário</a>
                </li>
            </ul>
        </li>
        <?php } ?>
    </ul>
</aside>