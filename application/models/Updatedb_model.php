<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 22/12/2017
 * Time: 17:04
 */

class Updatedb_model extends CRM_Model
{
    public function versions()
    {
        $versions = array("0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20",
            "21","22", "23","24", "25","26", "27", "28", "29", "30","31","32", "33", "34","35", "36", "37", "38", "39",
        "40", "41", "42", "43", "44", "45", "46", "47", "48");
        return $versions;
    }

    public function last_version()
    {
        $versions = $this->versions();
        return end($versions);
    }

    public function comandos($version,$tipo = true, $update = false)
    {
        $description = NULL;
        switch ($version) {
            case "1":
                if ($tipo) {
                    $description = "Itens no menu";
                    $menu = '{"aside_menu_active":[{"name":"als_dashboard","url":"\/","permission":"","icon":"fa fa-tachometer","id":"dashboard"},{"name":"als_clients","url":"clients","permission":"customers","icon":"fa fa-users","id":"customers"},{"name":"Parceiros","url":"partner","permission":"partner","icon":"fa fa-handshake-o","id":"partners"},{"name":"als_sales","url":"#","permission":"","icon":"fa fa-balance-scale","id":"sales","children":[{"name":"proposals","url":"proposals","permission":"proposals","icon":"","id":"child-proposals"},{"name":"estimates","url":"estimates\/list_estimates","permission":"estimates","icon":"","id":"child-estimates"},{"name":"invoices","url":"invoices\/list_invoices","permission":"invoices","icon":"","id":"child-invoices"},{"name":"payments","url":"payments","permission":"payments","icon":"","id":"child-payments"},{"name":"items","url":"invoice_items","permission":"items","icon":"","id":"child-items"}]},{"name":"als_expenses","url":"expenses\/list_expenses","permission":"expenses","icon":"fa fa-heartbeat","id":"expenses"},{"name":"als_contracts","url":"contracts","permission":"contracts","icon":"fa fa-file","id":"contracts"},{"name":"projects","url":"projects","permission":"","icon":"fa fa-bars","id":"projects"},{"name":"als_tasks","url":"tasks\/list_tasks","permission":"","icon":"fa fa-tasks","id":"tasks"},{"name":"support","url":"tickets","permission":"","icon":"fa fa-ticket","id":"tickets"},{"name":"als_leads","url":"leads","permission":"is_staff_member","icon":"fa fa-tty","id":"leads"},{"name":"als_kb","url":"#","permission":"knowledge_base","icon":"fa fa-folder-open-o","id":"knowledge-base","children":[{"name":"als_all_articles","url":"knowledge_base","permission":"","icon":"","id":"child-all-articles"},{"name":"als_kb_groups","url":"knowledge_base\/manage_groups","permission":"","icon":"","id":"child-groups"}]},{"name":"als_utilities","url":"#","permission":"","icon":"fa fa-cogs","id":"utilities","children":[{"name":"als_media","url":"utilities\/media","permission":"","icon":"","id":"child-media"},{"name":"bulk_pdf_exporter","url":"utilities\/bulk_pdf_exporter","permission":"bulk_pdf_exporter","icon":"","id":"child-bulk-pdf-exporter"},{"name":"als_calendar_submenu","url":"utilities\/calendar","permission":"","icon":"","id":"child-calendar"},{"name":"als_goals_tracking","url":"goals","permission":"goals","icon":"","id":"child-goals-tracking"},{"name":"als_surveys","url":"surveys","permission":"surveys","icon":"","id":"child-surveys"},{"name":"als_announcements_submenu","url":"announcements","permission":"is_admin","icon":"","id":"child-announcements"},{"name":"utility_backup","url":"utilities\/backup","permission":"is_admin","icon":"","id":"child-database-backup"},{"name":"als_activity_log_submenu","url":"utilities\/activity_log","permission":"is_admin","icon":"","id":"child-activity-log"},{"name":"ticket_pipe_log","url":"utilities\/pipe_log","permission":"is_admin","icon":"","id":"ticket-pipe-log"},{"name":"Mudancas do Sistema","url":"utilities\/changelog","permission":"","icon":"","id":"changelog"}]},{"name":"als_reports","url":"#","permission":"reports","icon":"fa fa-area-chart","id":"reports","children":[{"name":"Relat\\\u00f3rio de Cargas","url":"utilities\/load_report","permission":"reports","icon":"","id":"child-load_report"},		{"name":"Relat\\\u00f3rio de Atendimentos","url":"utilities\/attendance_report","permission":"reports","icon":"","id":"child-attendance_report"},{"name":"timesheets_overview","url":"staff\/timesheets?view=all","permission":"reports","icon":"","id":"reports_timesheets_overview"},{"name":"Gr\\\u00e1ficos","url":"utilities\/chart","permission":"reports","icon":"","id":"charts"}]}]}';
                    $this->db->query("UPDATE `tbloptions` SET `value` = '" . $menu . "' WHERE `tbloptions`.`name` = 'aside_menu_active';");
                } else if (!tipo) {
                }
                break;
            case "2":
                if ($tipo) {
                    $description = "Changelog";
                    if (!$this->db->field_exists('change_id', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` ADD `change_id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`change_id`);");
                    if (!$this->db->field_exists('articleid', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` ADD `articleid` INT NULL AFTER `Version`, ADD INDEX (`articleid`);");
                } else if (!$tipo) {

                }
                break;
            case "3":
                if ($tipo) {
                    $description = "Notificações";
                    $menu = '{"aside_menu_active":[{"name":"als_dashboard","url":"\/","permission":"","icon":"fa fa-tachometer","id":"dashboard"},{"name":"als_clients","url":"clients","permission":"customers","icon":"fa fa-users","id":"customers"},{"name":"Parceiros","url":"partner","permission":"partner","icon":"fa fa-handshake-o","id":"partners"},{"name":"als_sales","url":"#","permission":"","icon":"fa fa-balance-scale","id":"sales","children":[{"name":"proposals","url":"proposals","permission":"proposals","icon":"","id":"child-proposals"},{"name":"estimates","url":"estimates\/list_estimates","permission":"estimates","icon":"","id":"child-estimates"},{"name":"invoices","url":"invoices\/list_invoices","permission":"invoices","icon":"","id":"child-invoices"},{"name":"payments","url":"payments","permission":"payments","icon":"","id":"child-payments"},{"name":"items","url":"invoice_items","permission":"items","icon":"","id":"child-items"}]},{"name":"als_expenses","url":"expenses\/list_expenses","permission":"expenses","icon":"fa fa-heartbeat","id":"expenses"},{"name":"als_contracts","url":"contracts","permission":"contracts","icon":"fa fa-file","id":"contracts"},{"name":"projects","url":"projects","permission":"projects","icon":"fa fa-bars","id":"projects"},{"name":"als_tasks","url":"tasks\/list_tasks","permission":"","icon":"fa fa-tasks","id":"tasks"},{"name":"support","url":"tickets","permission":"","icon":"fa fa-ticket","id":"tickets"},{"name":"als_leads","url":"leads","permission":"is_staff_member","icon":"fa fa-tty","id":"leads"},{"name":"als_kb","url":"#","permission":"knowledge_base","icon":"fa fa-folder-open-o","id":"knowledge-base","children":[{"name":"als_all_articles","url":"knowledge_base","permission":"","icon":"","id":"child-all-articles"},{"name":"als_kb_groups","url":"knowledge_base\/manage_groups","permission":"","icon":"","id":"child-groups"}]},{"name":"als_utilities","url":"#","permission":"","icon":"fa fa-cogs","id":"utilities","children":[{"name":"als_media","url":"utilities\/media","permission":"","icon":"","id":"child-media"},{"name":"bulk_pdf_exporter","url":"utilities\/bulk_pdf_exporter","permission":"bulk_pdf_exporter","icon":"","id":"child-bulk-pdf-exporter"},{"name":"als_calendar_submenu","url":"utilities\/calendar","permission":"","icon":"","id":"child-calendar"},{"name":"als_goals_tracking","url":"goals","permission":"goals","icon":"","id":"child-goals-tracking"},{"name":"als_surveys","url":"surveys","permission":"surveys","icon":"","id":"child-surveys"},{"name":"als_announcements_submenu","url":"announcements","permission":"is_admin","icon":"","id":"child-announcements"},{"name":"nav_notifications","url":"utilities\/notifications","permission":"is_admin","icon":"","id":"notifications"},{"name":"utility_backup","url":"utilities\/backup","permission":"is_admin","icon":"","id":"child-database-backup"},{"name":"als_activity_log_submenu","url":"utilities\/activity_log","permission":"is_admin","icon":"","id":"child-activity-log"},{"name":"ticket_pipe_log","url":"utilities\/pipe_log","permission":"is_admin","icon":"","id":"ticket-pipe-log"},{"name":"Mudancas do Sistema","url":"utilities\/changelog","permission":"","icon":"","id":"changelog"}]},{"name":"als_reports","url":"#","permission":"reports","icon":"fa fa-area-chart","id":"reports","children":[{"name":"Relat\\\u00f3rio de Cargas","url":"utilities\/load_report","permission":"reports","icon":"","id":"child-load_report"},		{"name":"Relat\\\u00f3rio de Atendimentos","url":"utilities\/attendance_report","permission":"reports","icon":"","id":"child-attendance_report"},{"name":"timesheets_overview","url":"staff\/timesheets?view=all","permission":"reports","icon":"","id":"reports_timesheets_overview"},{"name":"Gr\\\u00e1ficos","url":"utilities\/chart","permission":"reports","icon":"","id":"charts"}]}]}';
                    $this->db->query("UPDATE `tbloptions` SET `value` = '" . $menu . "' WHERE `tbloptions`.`name` = 'aside_menu_active';");
                    if (!$this->db->field_exists('master', 'tblnotifications'))
                        $this->db->query("ALTER TABLE `tblnotifications` ADD `master` BOOLEAN NOT NULL AFTER `additional_data`;");
                } else if (!tipo) {
                }
                break;
            case "4":
                if ($tipo) {
                    $description = "Nome Solicitante, Changelog e Base de Conhecimento";
                    if (!$this->db->field_exists('name_soli', 'tbltickets'))
                        $this->db->query("ALTER TABLE `tbltickets` ADD `name_soli` VARCHAR(100) NOT NULL AFTER `partner_id`;");
                    $this->db->query("DELETE FROM `tblknowledgebasegroups` WHERE `groupid` = 777");
                    $this->db->query("INSERT INTO `tblknowledgebasegroups` (`groupid`, `name`, `description`, `active`, `color`, `group_order`) VALUES(777, 'CRM', 'Documentação das alterações dos arquivos a serem replicados sempre que atualizar a versão do CRM', 1, '#3c8dbc', 0)");
                    $this->db->query("DELETE FROM `tblknowledgebase` WHERE `articlegroup` = 777");
                    if (!$this->db->field_exists('slug', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` ADD `slug` MEDIUMTEXT NOT NULL AFTER `Version`;");
                    $this->db->query("ALTER TABLE `changelog` CHANGE `slug` `slug` MEDIUMTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;");
                    if ($this->db->field_exists('articleid', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` DROP `articleid`;");
                    $this->update_menu_changelog_knowbase();
                } else if (!$tipo) {

                }
                break;
            case "5":
                if ($tipo) {
                    $description = "Permissões e Notificações";
                    $this->update_menu_changelog_knowbase();
                    $this->db->query("INSERT INTO `tblpermissions` (`permissionid`, `name`, `shortname`) VALUES (NULL, 'Parceiros', 'partner');");
                    $this->db->query("INSERT INTO `tblpermissions` (`permissionid`, `name`, `shortname`) VALUES (NULL, 'Controle de frotas', 'fleet')");
                } else if (!tipo) {
                }
                break;
            case "6":
                if ($tipo) {
                    $description = "Relatórios de Avaliação";
                    $this->update_menu_changelog_knowbase();
                } else if (!tipo) {
                }
                break;
            case "7":
                if ($tipo) {
                    $description = "Controle de frota";
                    if (!$this->db->table_exists('tblfleetvehicles'))
                        $this->db->query("CREATE TABLE `tblfleetvehicles` ( `vehicleid` INT NOT NULL AUTO_INCREMENT , `placa` VARCHAR(8) NOT NULL , `finalplaca` VARCHAR(4) NOT NULL , `marca` VARCHAR(100) NOT NULL , `medelo` VARCHAR(255) NOT NULL , `tipo` VARCHAR(50) NOT NULL , `descricao` MEDIUMTEXT NOT NULL , `codinternemp` INT NOT NULL , `ano` INT NOT NULL , `kmatual` INT NOT NULL , `categoria` VARCHAR(255) NOT NULL , `chassi` VARCHAR(255) NOT NULL , `renavan` VARCHAR(255) NOT NULL , `eixos` INT NOT NULL , `cor` VARCHAR(50) NOT NULL , `proprietario` VARCHAR(255) NOT NULL , `venclicenci` VARCHAR(50) NOT NULL , `alenado` VARCHAR(50) NOT NULL , `locallicenci` VARCHAR(50) NOT NULL , `valorveic` FLOAT NOT NULL , `numcontrato` VARCHAR(50) NOT NULL , `datainicicontr` DATE NOT NULL , `datafimcontr` DATE NOT NULL , `observacao` TEXT NOT NULL , `active` BOOLEAN NOT NULL , PRIMARY KEY (`vehicleid`)) ENGINE = InnoDB;");
                    if (!$this->db->table_exists('tblfleetout'))
                        $this->db->query("CREATE TABLE `tblfleetout` ( `idsaida` INT NOT NULL AUTO_INCREMENT , `data` DATE NOT NULL , `motivo` VARCHAR(255) NOT NULL , `vehicleid` INT NOT NULL , `staffid` INT NOT NULL , `km_inicial` FLOAT NOT NULL , `km_final` FLOAT NULL , `hora_inicial` TIME NOT NULL , `hora_final` TIME NULL , `obs` TEXT NOT NULL , PRIMARY KEY (`idsaida`), INDEX (`vehicleid`), INDEX (`staffid`)) ENGINE = InnoDB;");
                    if (!$this->db->field_exists('state', 'tblfleetout'))
                        $this->db->query("ALTER TABLE `tblfleetout` ADD `state` BOOLEAN NOT NULL AFTER `obs`;");
                    if (!$this->db->field_exists('inuse', 'tblfleetvehicles'))
                        $this->db->query("ALTER TABLE `tblfleetvehicles` ADD `inuse` BOOLEAN NOT NULL AFTER `active`;");
                    $this->db->query("ALTER TABLE `tblfleetvehicles` CHANGE `venclicenci` `venclicenci` DATE NOT NULL;");
                    $this->update_menu_changelog_knowbase();
                } else if (!$tipo) {
                    $this->db->query("DROP TABLE `tblfleetvehicles`");
                    $this->db->query("DROP TABLE `tblfleetout`");
                }
                break;
            case "8":
                if ($tipo) {
                    $description = "Controle de frota";
                    if (!$this->db->table_exists('tblfleetsupply'))
                        $this->db->query("CREATE TABLE `tblfleetsupply` ( `supplyid` INT NOT NULL AUTO_INCREMENT , `vehicleid` INT NOT NULL , `posto` VARCHAR(255) NOT NULL , `litro` FLOAT(2) NOT NULL , `valortotal` FLOAT(2) NOT NULL , `precoporlitro` FLOAT(2) NOT NULL , `data` DATETIME NOT NULL , PRIMARY KEY (`supplyid`), INDEX (`vehicleid`)) ENGINE = InnoDB;");
                    if (!$this->db->field_exists('datetime_inicial', 'tblfleetout'))
                        $this->db->query("ALTER TABLE `tblfleetout` CHANGE `hora_inicial` `datetime_inicial` DATETIME NOT NULL;");
                    if (!$this->db->field_exists('datetime_final', 'tblfleetout'))
                        $this->db->query("ALTER TABLE `tblfleetout` CHANGE `hora_final` `datetime_final` DATETIME NULL DEFAULT NULL;");
                    if (!$this->db->field_exists('doc_cnh_isenabled', 'tblstaff'))
                        $this->db->query("ALTER TABLE `tblstaff` ADD `doc_cnh_isenabled` BOOLEAN NOT NULL AFTER `partner_id`, ADD `doc_cnh_num` VARCHAR(30) NULL AFTER `doc_cnh_isenabled`, ADD `doc_cnh_categorycnh` VARCHAR(5) NULL AFTER `doc_cnh_num`, ADD `doc_cnh_validade` DATE NULL AFTER `doc_cnh_categorycnh`;");
                    if (get_option('fleet_out_limit_alert') == "" || get_option('fleet_out_limit_alert') == NULL)
                        add_option('fleet_out_limit_alert', '300');
                } else if (!$tipo)                                       //Função de desistalação
                {
                    $this->db->query("DROP TABLE `tblfleetsupply`");
                }
                break;
            case "9":
                if ($tipo) {
                    $description = "Relacionamento do banco de dados, controle de frota";
                    $this->update_menu_changelog_knowbase();
                    $this->db->query("ALTER TABLE `tblclients` ADD CONSTRAINT `tblclients_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `tblpartner` (`partner_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
                    $this->db->query("ALTER TABLE `tblcontacts` ADD CONSTRAINT `tblcontacts_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `tblclients` (`userid`)  ON DELETE NO ACTION ON UPDATE NO ACTION;");
                    $this->db->query("ALTER TABLE `tblfleetout` ADD CONSTRAINT `tblfleetout_ibfk_1` FOREIGN KEY (`vehicleid`) REFERENCES `tblfleetvehicles` (`vehicleid`) ON DELETE NO ACTION ON UPDATE NO ACTION, ADD CONSTRAINT `tblfleetout_ibfk_2` FOREIGN KEY (`staffid`) REFERENCES `tblstaff` (`staffid`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
                    $this->db->query("ALTER TABLE `tblfleetsupply` ADD CONSTRAINT `tblfleetsupply_ibfk_1` FOREIGN KEY (`vehicleid`) REFERENCES `tblfleetvehicles` (`vehicleid`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
                    $this->db->query("ALTER TABLE `tblleads` ADD CONSTRAINT `tblleads_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `tblpartner` (`partner_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
                    $this->db->query("ALTER TABLE `tblstaff` ADD CONSTRAINT `tblstaff_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `tblpartner` (`partner_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
                        $this->db->query("ALTER TABLE `tbltickets` ADD CONSTRAINT `tbltickets_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `tblpartner` (`partner_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
                    if (!$this->db->field_exists('chave_detran', 'tblfleetvehicles'))
                        $this->db->query("ALTER TABLE `tblfleetvehicles` ADD `chave_detran` VARCHAR(255) NOT NULL AFTER `inuse`, ADD `cpfcnpjveiculo` VARCHAR(15) NOT NULL AFTER `chave_detran`;");
                } else if (!$tipo) {
                    $this->db->query("ALTER TABLE `tblclients` DROP FOREIGN KEY `tblclients_ibfk_1`;");
                    $this->db->query("ALTER TABLE `tblcontacts` DROP FOREIGN KEY `tblcontacts_ibfk_1`;");
                    $this->db->query("ALTER TABLE `tblfleetout` DROP FOREIGN KEY `tblfleetout_ibfk_1`, DROP FOREIGN KEY `tblfleetout_ibfk_2`;");
                    $this->db->query("ALTER TABLE `tblfleetsupply` DROP FOREIGN KEY `tblfleetsupply_ibfk_1`;");
                    $this->db->query("ALTER TABLE `tblleads` DROP FOREIGN KEY `tblleads_ibfk_1`;");
                    $this->db->query("ALTER TABLE `tblstaff` DROP FOREIGN KEY `tblstaff_ibfk_1`;");
                    $this->db->query("ALTER TABLE `tbltickets` DROP FOREIGN KEY `tbltickets_ibfk_1`;");
                }
                break;
            case "10":
                if ($tipo) {
                    $description = "Email secundário staff, excluir colunas não usadas mais.";
                    $this->update_menu_changelog_knowbase();
                    if (!$this->db->field_exists('email_sec', 'tblstaff'))
                        $this->db->query("ALTER TABLE `tblstaff` ADD `email_sec` VARCHAR(100) NOT NULL AFTER `email`;");
                    if ($this->db->field_exists('ssubject', 'tblticketstatus'))
                        $this->db->query("ALTER TABLE `tblticketstatus` DROP `ssubject`, DROP `smessage`;");
                }
                break;
            case "11":
                if ($tipo) {
                    $description = "Novos campos (ticketid e local) no modal de new-fleet-out";
                    if (!$this->db->field_exists('rel_id', 'tblfleetout')) {
                        $this->db->query("ALTER TABLE `tblfleetout` ADD `rel_id` INT NOT NULL AFTER `state`, ADD INDEX (`rel_id`);");
                    }
                    if (!$this->db->field_exists('rel_type', 'tblfleetout')) {
                        $this->db->query("ALTER TABLE `tblfleetout` ADD `rel_type` VARCHAR(30) NOT NULL AFTER `rel_id`, ADD INDEX (`rel_type`);");
                    }
                    if (!$this->db->field_exists('local', 'tblfleetout')) {
                        $this->db->query("ALTER TABLE `tblfleetout` ADD `local` VARCHAR(50) NOT NULL AFTER `rel_type`;");
                    }
                    $this->setup_menu_active();
                } else if (!$tipo) {
                    $this->db->query("ALTER TABLE `tblfleetout` DROP `rel_id`;");
                    $this->db->query("ALTER TABLE `tblfleetout` DROP `rel_type`;");
                    $this->db->query("ALTER TABLE `tblfleetout` DROP `local`;");
                }
                break;
            case "12":
                if ($tipo) {
                    $description = "Novo campo kilometragem na tabela tblfleetsupply";
                    if (!$this->db->field_exists('kilometragem', 'tblfleetsupply')) {
                        $this->db->query("ALTER TABLE `tblfleetsupply` ADD `kilometragem` INT NOT NULL AFTER `data`;");
                    }
                } else if (!$tipo) {
                    $this->db->query("ALTER TABLE `tblfleetsupply` DROP `kilometragem`;");
                }
                break;
            case "13":
                $description = "Criando campo para identificar atendimentos no plantão.";
                if ($tipo) {
                    if (!$this->db->field_exists('plantao', 'tbltickets'))
                        $this->db->query("ALTER TABLE `tbltickets` ADD `plantao` BOOLEAN NOT NULL AFTER `name_soli`;");
                    if (PAINEL == QUANTUM) {
                        $this->update_menu_changelog_knowbase();
                        $msg = 'A tela de Tickets foi alterada.</br>Para ver detalhes sobre a mudança <a href="' . admin_url("knowledge_base/view/mudanca-tickets") . '">Clique Aqui.</a>';
                        $dataa['date'] = date('Y-m-d H:i:s');
                        $dataa['description'] = $msg;
                        $dataa['from_fullname'] = "Sistema";
                        $dataa['master'] = "1";
//                            $this->db->insert('tblnotifications',$dataa);
                        add_notification($dataa, true, false, true);
                    }
                } else if (!$tipo) {
                    if ($this->db->field_exists('plantao', 'tbltickets'))
                        $this->db->query("ALTER TABLE `tbltickets` DROP `plantao`;");
                }
                break;
            case "14":
                $description = "Opção do menu do relatório de frota.";
                if ($tipo) {
//                    $this->update_menu_changelog_knowbase();
                    $options = array(
                        'name' => 'fleet_control',
                        'permission' => 'fleet',
                        'icon' => '',
                        'url' => 'utilities/fleet_report',
                        'id' => 'fleet_report');

                    add_main_menu_item($options, 'reports');
                } else if (!tipo) {
                }
                break;
            case "15":
                $description = "Removendo Item inútil do menu e corrigindo problema das observações.";
                if ($tipo) {
                    remove_main_menu_item("notifications");
                    $this->db->query("UPDATE tblclients SET observation = REPLACE(observation,'\r\n','</br>')");
                    logActivity($this->db->affected_rows() . " Clientes atualizados. [Problema da observação - Tipo 1]");
                    $this->db->query("UPDATE tblclients SET observation = REPLACE(observation,'\n','</br>')");
                    logActivity($this->db->affected_rows() . " Clientes atualizados. [Problema da observação - Tipo 2]");
                    $this->db->query("UPDATE tblclients SET observation = REPLACE(observation,'\r','</br>')");
                    logActivity($this->db->affected_rows() . " Clientes atualizados. [Problema da observação - Tipo 3]");

                    //Desfazendo a função update_menu_changelog_knowbase()
                    $this->db->query("TRUNCATE changelog");
                    $this->db->query("INSERT INTO `changelog` (`change_id`, `Autor`, `Data`, `Titulo`, `Tipo`, `Version`, `slug`) VALUES(1, 'Italo Tavares Lima', '2017-12-05', 'Sistema de Parceiros', 'new', '0.1.0', NULL),(2, 'Italo Tavares Lima', '2017-12-26', 'Eventos ao mudar estado do Ticket', 'new', '0.1.0', 'como-configurar-eventos-ao-mudar-estado-do-ticket'),(3, 'Italo Tavares Lima', '2017-12-20', 'Atribuição automática de destino e seguidores da tarefa', 'new', '0.1.0', 'atribuicao-automatica-de-destino-e-seguidores-da-tarefa'),(4, 'Italo Tavares Lima', '2017-12-19', 'Relatórios', 'bug', '0.1.0', NULL),(5, 'Italo Tavares Lima', '2017-11-29', 'Mudanças do sistema', 'new', '0.1.0', NULL),(6, 'Italo Tavares Lima', '2017-11-22', 'Subconsulta nos relatórios de atendimentos', 'new', '0.0.6', NULL),(7, 'Italo Tavares Lima', '2017-12-29', 'Gráficos', 'new', '0.1.0', 'graficos'),(9, 'Italo Tavares Lima', '2018-01-02', 'Senha Painel', 'bug', '0.1.0', NULL),(10, 'Italo Tavares Lima', '2018-01-02', 'Notificação sonora no painel', 'new', '0.1.0', NULL),(11, 'Italo Tavares Lima', '2018-01-02', 'Notificação em tela cheia', 'new', '0.1.0', 'notificacao-em-tela-cheia'),(12, 'Italo Tavares Lima', '2017-12-21', 'Permissões do sistema de parceiros', 'bug', '0.1.0', NULL),(13, 'Italo Tavares Lima', '2017-12-15', 'Novos filtros na consulta da tabela de tickets', 'new', '0.1.0', NULL),(14, 'Italo Tavares Lima', '2018-01-11', 'Controle de frota', 'new', '0.1.0', 'controle-de-frota'),(15, 'Italo Tavares Lima', '2018-01-11', 'Envio de Email(lentidão)', 'bug', '0.1.0', NULL),(16, 'Italo Tavares Lima', '2018-01-10', 'Atribuição de ticket ao abrir chamado', 'bug', '0.1.0', NULL),(17, 'Italo Tavares Lima', '2018-01-10', 'Envio de Email ao alterar status do ticket', 'bug', '0.1.0', NULL);");
                    add_changelog_item('Italo Tavares Lima', '30/01/2018', 'Relatório de frota', 'new', '0.1.0');
                    if (PAINEL == QUANTUM) {
                        add_changelog_item('Italo Tavares Lima', '30/01/2018', 'Mudança nos tickets', 'note', '0.1.0', 'mudanca-tickets');
                        add_changelog_item('Italo Tavares Lima', '30/01/2018', 'Filtro dos tickets do plantão', 'new', '0.1.0');
                    }

                    //Continuando o atual
                    add_changelog_item('Italo Tavares Lima', '01/02/2018', 'Observação do clinte na tela de tickets', 'bug', '0.1.0');
                } else if (!tipo) {
                }
                break;
            case "16":
                if ($tipo)                                                                                                     //Função de instalação
                {
                    $description = "Mostrar tarefas na tabela de tickets";                                                                                        //Descrição da atualização
                    if (get_option('show_task_in_ticket_table') == "" || get_option('show_task_in_ticket_table') == NULL) {
                        if (PAINEL == INORTE) {
                            add_option('show_task_in_ticket_table', '0');
                        } else {
                            add_option('show_task_in_ticket_table', '1');
                        }
                    }
                    add_changelog_item('Matheus Machado Vilarino', '01/02/2018', 'Mostrar tarefas na tela de tickets', 'new', '0.1.1');  //Adiciona item no ChangeLog
                } else if (!$tipo)                                                                                               //Função de desistalação
                {
                    if (get_option('show_task_in_ticket_table') != "" || get_option('show_task_in_ticket_table') != NULL) {
                        delete_option('show_task_in_ticket_table');
                    }
                }
                break;
            case "17":
                $description = "Controle de equipamentos.";
                if ($tipo) {
                    $options = array(
                        'name' => 'Controle de equipamentos',
                        'permission' => 'equip',
                        'icon' => 'fa fa-desktop',
                        'url' => 'equip',
                        'id' => 'equipments',
                        'order' => '12');
                    if (ENVIRONMENT != "production")
                        add_main_menu_item($options);

                    if (!$this->db->table_exists('tblotherrelations'))
                        $this->db->query("CREATE TABLE `tblotherrelations` ( `relationid` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `link` VARCHAR(300) NOT NULL , `subtext` VARCHAR(255) NOT NULL , PRIMARY KEY (`relationid`)) ENGINE = InnoDB;");
                    if (!$this->db->field_exists('master_read', 'tblnotifications'))
                        $this->db->query("ALTER TABLE `tblnotifications` ADD `master_read` BOOLEAN NOT NULL AFTER `master`;");
                    if (!$this->db->table_exists('tblequipments_in'))
                        $this->db->query("CREATE TABLE `tblequipments_in` ( `equipinid` INT NOT NULL AUTO_INCREMENT , `data_from` DATE NOT NULL , `tipo` VARCHAR(100) NOT NULL, `rel_type` VARCHAR(50) NOT NULL , `rel_id` INT NOT NULL , `staffid` INT NOT NULL , `description` VARCHAR(255) NOT NULL , `observation` TEXT NOT NULL, `flag_out` tinyint(1) NOT NULL, PRIMARY KEY (`equipinid`), INDEX (`staffid`)) ENGINE = InnoDB;");
                    if (!$this->db->table_exists('tblequipments_mov'))
                        $this->db->query("CREATE TABLE `tblequipments_mov` ( `equipoutid` INT NOT NULL AUTO_INCREMENT , `data_from` DATETIME NOT NULL , `data_to` DATETIME NULL, `tipo` VARCHAR(100) NOT NULL, `rel_type` VARCHAR(50) NOT NULL , `rel_id` INT NOT NULL , `staffid` INT NOT NULL , `description` VARCHAR(255) NOT NULL , `observation` TEXT NOT NULL , `equipmentid` int(11) NOT NULL, `status` tinyint(1) NOT NULL,`flag_in_out` tinyint(1) NOT NULL, PRIMARY KEY (`equipoutid`), INDEX (`staffid`), INDEX (`equipmentid`)) ENGINE = InnoDB;");

                    add_changelog_item('Italo Tavares Lima', '07/02/2018', 'Gráfico: Tipos de Serviços por Ticket', 'new', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '07/02/2018', 'Confirmação de leitura de notificação em tela cheia', 'new', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '07/02/2018', 'Otimização da tabela de frota', 'note', '0.1.1');
                    if (PAINEL == QUANTUM)
                        add_changelog_item('Italo Tavares Lima', '07/02/2018', 'Filtro dos tickets por prioridade: Atualização', 'new', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '07/02/2018', 'Ordedenação da tabela de Atendimento por técnicos', 'bug', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '07/02/2018', 'Desvinculção automatica da atribuição do ticket', 'bug', '0.1.1');

                    $msg = 'O CRM foi atualizado.</br>Para ver detalhes sobre as mudanças <a href="' . admin_url("utilities/changelog") . '">Clique Aqui.</a>';
                    $dataa['date'] = date('Y-m-d H:i:s');
                    $dataa['description'] = $msg;
                    $dataa['from_fullname'] = "Sistema";
                    $dataa['master'] = "1";
                    add_notification($dataa, true, false, true);
                } else if (!$tipo) {
                    remove_main_menu_item("equipments");

                    if ($this->db->table_exists('tblotherrelations'))
                        $this->db->query("DROP TABLE `tblotherrelations`");
                    if ($this->db->field_exists('master_read', 'tblnotifications'))
                        $this->db->query("ALTER TABLE `tblnotifications` DROP `master_read`;");
                    if ($this->db->table_exists('tblequipments_in'))
                        $this->db->query("DROP TABLE `tblequipments_in`");
                    if ($this->db->table_exists('tblequipments_mov'))
                        $this->db->query("DROP TABLE `tblequipments_mov`");
                }
                break;
            case "18":
                $description = "Painel de Atendimentos";
                if ($tipo) {
                    add_option("ticket_waiting_alert_time", "10");
                    add_option("ticket_waiting_limit_time", "20");
                    add_option('ticket_waiting_alert_sound', "0");
                    add_option('ticket_waiting_limit_sound', "0");
                    add_option('ticket_waiting_alert_sound_type', "0");
                    add_option('ticket_waiting_limit_sound_type', "0");
                    add_option("ticket_main_color", MAIN_COLOR);
                    add_option('painel_refresh_time', "20");
                } else if (!$tipo) {
                    delete_option("ticket_waiting_alert_time");
                    delete_option("ticket_waiting_limit_time");
                    delete_option('ticket_waiting_alert_sound');
                    delete_option('ticket_waiting_limit_sound');
                    delete_option('ticket_waiting_alert_sound_type');
                    delete_option('ticket_waiting_limit_sound_type');
                    delete_option("ticket_main_color");
                    delete_option('painel_refresh_time');
                }
                break;
            case "19":
                $description = "Controle de Patrimonio";
                if ($tipo) {
                    remove_main_menu_item('equipments');
                    $options = array(
                        'name' => 'Controle de equipamentos',
                        'permission' => 'equip',
                        'icon' => 'fa fa-desktop',
                        'url' => 'equipmens',
                        'id' => 'equipments',
                        'order' => '12');
                    add_main_menu_item($options);
                    /**-----------------*/
//                    Deu um problema e teve que reconstruir as tabelas
                    if ($this->db->table_exists('tblequipments_mov')) {
                        $this->db->query("DROP TABLE `tblequipments_mov`");
                        $this->db->query("CREATE TABLE `tblequipments_mov` (`equipoutid` int(11) NOT NULL, `data_from` datetime NOT NULL,`data_to` datetime DEFAULT NULL,`tipo` varchar(100) NOT NULL,`rel_type` varchar(50) NOT NULL,`rel_id` int(11) NOT NULL,`staffid` int(11) NOT NULL,`description` varchar(255) NOT NULL,`observation` text NOT NULL,`equipmentid` int(11) NOT NULL,`status` tinyint(1) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
                        $this->db->query("ALTER TABLE `tblequipments_mov`ADD PRIMARY KEY (`equipoutid`),ADD KEY `staffid` (`staffid`),ADD KEY `equipmentid` (`equipmentid`);");
                        $this->db->query("ALTER TABLE `tblequipments_mov` MODIFY `equipoutid` int(11) NOT NULL AUTO_INCREMENT;");
                    }
                    if ($this->db->table_exists('tblequipments_in')) {
                        $this->db->query("DROP TABLE `tblequipments_in`");
                        $this->db->query("CREATE TABLE `tblequipments_in` (`equipinid` int(11) NOT NULL, `data_from` datetime NOT NULL, `data_to` datetime DEFAULT NULL, `tipo` varchar(100) NOT NULL, `rel_type` varchar(50) NOT NULL, `rel_id` int(11) NOT NULL, `staffid` int(11) NOT NULL, `description` varchar(255) NOT NULL, `observation` text NOT NULL, `status` tinyint(1) NOT NULL, `flag_out` tinyint(1) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
                        $this->db->query("ALTER TABLE `tblequipments_in` ADD PRIMARY KEY (`equipinid`), ADD KEY `staffid` (`staffid`);");
                        $this->db->query("ALTER TABLE `tblequipments_in` MODIFY `equipinid` int(11) NOT NULL AUTO_INCREMENT;");
                        $this->db->query("ALTER TABLE `tblequipments_in` CHANGE `staffid` `staffid` INT(11) NULL;");
                        $this->db->query("ALTER TABLE `tblequipments_in` ADD `patrimonyid` INT NULL AFTER `flag_out`, ADD INDEX (`patrimonyid`);");
                    }
                    /**-----------------*/
                    if (!$this->db->table_exists('tblpatrimony_categories'))
                        $this->db->query("CREATE TABLE `tblpatrimony_categories` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
                    if (!$this->db->table_exists('tblpatrimony_bens'))
                        $this->db->query("CREATE TABLE `tblpatrimony_bens` ( `id` INT NOT NULL AUTO_INCREMENT , `id_category` INT NOT NULL , `data_aquisicao` DATE NOT NULL , `descricao` VARCHAR(300) NOT NULL , `caracteristicas` TEXT NOT NULL , `forma_ingresso` VARCHAR(30) NOT NULL , `tipo_bem` INT NOT NULL , `valor` FLOAT NOT NULL , `plaquetavel` BOOLEAN NOT NULL , PRIMARY KEY (`id`), INDEX (`id_category`)) ENGINE = InnoDB;");

                    $this->db->query("INSERT INTO `tblpermissions` (`permissionid`, `name`, `shortname`) VALUES (NULL, 'Controle de Patrimônio/Equipamentos', 'equipments');");
                    if (!$this->db->field_exists('flag_backup', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` ADD `flag_backup` BOOLEAN NOT NULL AFTER `observation`;");
                    add_option("ticket_waiting_alert_time_attendance", "30");
                    add_option("ticket_waiting_limit_time_attendance", "60");

                    if (PAINEL == QUANTUM)
                        add_changelog_item('Italo Tavares Lima', '12/02/2018', 'Verificação backup cliente', 'new', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '12/02/2018', 'Alertas por tempo de Atendimentos|Painel', 'new', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '12/02/2018', 'Controle de Patrimônio/Equipamentos', 'new', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '12/02/2018', 'Verificação de Km [Controle de Frota]', 'bug', '0.1.1');
                } else if (!$tipo) {
                    remove_main_menu_item('equipments');
                    if ($this->db->table_exists('tblequipments_mov'))
                        $this->db->query("DROP TABLE `tblequipments_mov`");
                    if ($this->db->table_exists('tblequipments_in'))
                        $this->db->query("DROP TABLE `tblequipments_in`");
                    if ($this->db->table_exists('tblpatrimony_categories'))
                        $this->db->query("DROP TABLE `tblpatrimony_categories`");
                    if ($this->db->table_exists('tblpatrimony_bens'))
                        $this->db->query("DROP TABLE `tblpatrimony_bens`");
                    delete_option("ticket_waiting_alert_time_attendance");
                    delete_option("ticket_waiting_limit_time_attendance");
                }
                break;
            case "20":
                $description = "Gerenciado de vídeos";
                if ($tipo) {
                    if (!$this->db->table_exists('tblmoviescategory'))
                        $this->db->query("CREATE TABLE `tblmoviescategory` ( `categoryid` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`categoryid`)) ENGINE = InnoDB;");
                    if (!$this->db->table_exists('tblmoviessubcategory'))
                        $this->db->query("CREATE TABLE `tblmoviessubcategory` ( `idsubcategory` INT NOT NULL AUTO_INCREMENT , `subcategory` VARCHAR(255) NOT NULL , `categoryfk` INT NOT NULL , PRIMARY KEY (`idsubcategory`), INDEX (`categoryfk`)) ENGINE = InnoDB;");
                    if (!$this->db->table_exists('tblmovies'))
                        $this->db->query("CREATE TABLE `tblmovies` ( `idmovie` INT NOT NULL AUTO_INCREMENT , `title` VARCHAR(255) NOT NULL , `description` TEXT NOT NULL , `filename` VARCHAR(100) NOT NULL, `idsubcategoryfk` INT NOT NULL, `date` DATETIME NOT NULL, `perm_public` BOOLEAN NOT NULL, `perm_clients` BOOLEAN NOT NULL , `perm_staff` BOOLEAN NOT NULL, `perm_key` VARCHAR(100) NULL,`status` BOOLEAN NOT NULL DEFAULT TRUE, PRIMARY KEY (`idmovie`), INDEX (`idsubcategoryfk`)) ENGINE = InnoDB;");
//                    CREATE TABLE `tblmoviescomments` ( `commentid` INT NOT NULL AUTO_INCREMENT , `content` TEXT NOT NULL , `userid` INT NOT NULL , `typeuser` VARCHAR(30) NOT NULL , `movieidfk` INT NOT NULL , `date` DATETIME NOT NULL , PRIMARY KEY (`commentid`), INDEX (`movieidfk`)) ENGINE = InnoDB;
//                    CREATE TABLE `tblmovieslikes` ( `idlike` INT NOT NULL AUTO_INCREMENT , `userid` INT NOT NULL , `typeuser` VARCHAR(30) NOT NULL , `type` BOOLEAN NOT NULL COMMENT '1 = Like| 0 - Deslike' , `movieidfk` INT NOT NULL , PRIMARY KEY (`idlike`), INDEX (`movieidfk`)) ENGINE = InnoDB;
                    add_changelog_item('Italo Tavares Lima', '16/02/2018', 'Gerênciado de vídeos privados', 'new', '0.1.1');
                    add_option("pertubacao_notify", "0");
                    add_main_menu_item(array("name" => "Videos", "url" => "movies", "permission" => "", "icon" => "", "id" => "movies", "order" => 2), 'utilities');
                } else if (!$tipo) {
                    if ($this->db->table_exists('tblmoviescategory'))
                        $this->db->query("DROP TABLE `tblmoviescategory`");
                    if ($this->db->table_exists('tblmoviessubcategory'))
                        $this->db->query("DROP TABLE `tblmoviessubcategory`");
                    if ($this->db->table_exists('tblmovies'))
                        $this->db->query("DROP TABLE `tblmovies`");
                    delete_option("pertubacao_notify");
                    remove_main_menu_item("movies");
                }
                break;
            case "21":
                $description = "Filtro nas avaliações de atendimentos";
                if ($tipo) {
                    remove_main_menu_item("technician_evaluation");
                    remove_main_menu_item("attend_evaluation");
                    remove_main_menu_item("child-attendance_report");
                    add_main_menu_item(array("name" => "Atendimentos", "url" => "utilities/attendance_report", "permission" => "reports", "icon" => "", "id" => "child-attendance_report", "order" => 2), 'reports');
                    add_main_menu_item(array("name" => "Avaliações", "url" => "utilities/attend_evaluation", "permission" => "reports", "icon" => "", "id" => "evaluations", "order" => 3), 'reports');
                    if (!$this->db->field_exists('span_color', 'nota_atendimento'))
                        $this->db->query("ALTER TABLE `nota_atendimento` ADD `span_color` VARCHAR(100) NOT NULL AFTER `descricao`;");
                } else if (!$tipo) {
                }
                break;
            case "22":
                $description = "Modulos no changelog, adicionar vídeo por url";
                if ($tipo) {
                    if (!$this->db->table_exists('changelog_modules')) {
                        $this->db->query("CREATE TABLE `changelog_modules` ( `moduleid` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`moduleid`)) ENGINE = InnoDB;");
                        $this->db->query("INSERT INTO `changelog_modules` (`moduleid`, `name`) VALUES (NULL, 'CRM');");
                    }
                    if (!$this->db->field_exists('moduleid_fk', 'changelog')) {
                        $this->db->query("ALTER TABLE `changelog` ADD `moduleid_fk` INT NOT NULL AFTER `slug`, ADD INDEX (`moduleid_fk`);");
                        $this->db->query("UPDATE `changelog` SET `moduleid_fk`= 1");
                    }
                    $this->db->query("ALTER TABLE `changelog` ADD CONSTRAINT `changelog_ibfk_1` FOREIGN KEY (`moduleid_fk`) REFERENCES `changelog_modules` (`moduleid`) ON UPDATE CASCADE;");
                    if (!$this->db->field_exists('is_url', 'tblmovies'))
                        $this->db->query("ALTER TABLE `tblmovies` ADD `is_url` BOOLEAN NOT NULL AFTER `status`;");
                    remove_main_menu_item("technician_evaluation");
                    remove_main_menu_item("attend_evaluation");
                    add_changelog_item('Italo Tavares Lima', '19/02/2018', 'Módulos ChangeLog', 'new', '0.1.1');
                } else if (!tipo) {
                    if (!$this->db->table_exists('changelog_modules'))
                        $this->db->query("DROP TABLE `changelog_modules`");
                    if ($this->db->field_exists('moduleid_fk', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` DROP `moduleid_fk`;");
                    if ($this->db->field_exists('is_url', 'tblmovies'))
                        $this->db->query("ALTER TABLE `tblmovies` DROP `is_url`;");
                }
                break;
            case "23":
                $description = "Prioridade do cliente";
                if ($tipo) {
                    if (!$this->db->field_exists('priority', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` ADD `priority_client` INT NOT NULL DEFAULT '1' AFTER `partner_id`, ADD INDEX (`priority_client`)");
                } else if (!$tipo) {
                    if ($this->db->field_exists('priority', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` DROP `priority_client`, DROP INDEX `priority_client`");
                }
                break;
            case "24":
                $description = "Campo Telefone cliente";
                if ($tipo) {
                    if (!$this->db->field_exists('telefone', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` ADD `telefone` VARCHAR(30) NOT NULL AFTER `priority_client`;");
                    add_changelog_item('Italo Tavares Lima', '20/02/2018', 'Relatório de abastecimentos', 'new', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '20/02/2018', 'Correção bug anexos', 'bug', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '26/02/2018', 'Subconsultas Relatório de Avaliação', 'new', '0.1.1');
                } else if (!$tipo) {
                    if ($this->db->field_exists('telefone', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` DROP `telefone`");
                }
                break;
            case "25":
                $description = "Permissões Leads e Vendas";
                if ($tipo)
                {
                    $this->db->query("INSERT INTO `tblpermissions` (`permissionid`, `name`, `shortname`) VALUES (NULL, 'Leads', 'leads');");
                    $this->db->query("INSERT INTO `tblpermissions` (`permissionid`, `name`, `shortname`) VALUES (NULL, 'Utilities', 'utilities');");
                    add_changelog_item('Matheus Machado Vilarino', '01/03/2018', 'Permissões Leads e Utilidades', 'new', '0.1.1');
                    add_changelog_item('Matheus Machado Vilarino', '01/03/2018', 'Correção Permissões', 'bug', '0.1.1');
                    add_changelog_item('Matheus Machado Vilarino', '01/03/2018', 'Notificação de suporte pendente', 'new', '0.1.1');

                } else if(!$tipo)
                {
                    $this->db->query("DELETE FROM `tblpermissions` WHERE `tblpermissions`.`shortname` = 'leads';");
                    $this->db->query("DELETE FROM `tblpermissions` WHERE `tblpermissions`.`shortname` = 'utilities';");
                }
            break;
            case "26":
                $description = "Departamentos Vídeos";
                if($tipo)
                {
                    if (!$this->db->field_exists('departments', 'tblmovies'))
                        $this->db->query("ALTER TABLE `tblmovies` ADD `departments` VARCHAR(255) NOT NULL COMMENT 'Gambiarrazinha' AFTER `is_url`;");
                }
                else if(!$tipo)
                {
                    if ($this->db->field_exists('departments', 'tblmovies'))
                        $this->db->query("ALTER TABLE `tblmovies` DROP `departments`");
                }
            break;
            case "27":
                $description = "Ticket externo(cliente) assigned predefinido";
                if($tipo)
                {
                    if (!$this->db->field_exists('clients_predefined_assign', 'tbloptions'))
                        add_option('clients_predefined_assign', '0');
                }
                else if(!$tipo)
                {
                    if ($this->db->field_exists('clients_predefined_assign', 'tbloptions'))
                        delete_option('clients_predefined_assign');
                }
            break;
            case "28":
                $description = "Permissão Avaliação de Atendimento";
                if($tipo)
                {
                    if (!$this->db->field_exists('avaliacao_atendimento', 'tblpermissions'))
                        $this->db->query("INSERT INTO `tblpermissions` (`permissionid`, `name`, `shortname`) VALUES (NULL, 'Avaliação de Atendimento', 'avaliacao_atendimento');");
                        add_changelog_item('Matheus Machado Vilarino', '23/03/2018', 'Permissões Avaliação de Atendimento', 'new', '0.1.1');
                }
                else if(!$tipo)
                {
                    if ($this->db->field_exists('clients_predefined_assign', 'tblpermissions'))
                        $this->db->query("DELETE FROM `tblpermissions` WHERE `tblpermissions`.`shortname` = 'avaliacao_atendimento';");
                }
                break;
            case "29":
                $description = "Relatório de clientes sem solicitação";
                if($tipo)
                    add_main_menu_item(array("name" => "Clientes sem Solicitação", "url" => "utilities/no_request_clients", "permission" => "reports", "icon" => "", "id" => "child-no_request_clients", "order" => 5), 'reports');
                else if(!$tipo)
                    remove_main_menu_item("child-no_request_clients");
                break;
            case "30":
                $description = "Many-to-Many ClientsContacts(mudando a relação de clientes e contatos)";
                if($tipo)
                {
                    if(!$this->db->table_exists('tblclientscontacts')){
                        $this->db->query("CREATE TABLE  `tblclientscontacts` (`id` INT NOT NULL AUTO_INCREMENT, `clientid` INT NOT NULL, `contactid` INT NOT NULL, PRIMARY KEY(`id`))  ENGINE = InnoDB;");
//                        $this->db->query("ALTER TABLE `tblclients` ENGINE=InnoDB;");
//                        $this->db->query("ALTER TABLE `tblcontacts` ENGINE=InnoDB;");
//                        $this->db->query("ALTER TABLE `tblclientscontacts` ADD CONSTRAINT `clientid_fk` PRIMARY KEY (`clientid`) REFERENCES `tblclients`(`userid`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
//                        $this->db->query("ALTER TABLE `tblclientscontacts` ADD CONSTRAINT `contactid_fk` PRIMARY KEY (`contactid`) REFERENCES `tblcontacts`(``id) ON DELETE NO ACTION ON UPDATE NO ACTION;");
//                        $this->db->query("ALTER TABLE `tblclientscontacts` ADD PRIMARY KEY (`clientid`, `contactid`)");
                        $this->db->query("CREATE INDEX `clientid_contactid` ON `tblclientscontacts` (`clientid`, `contactid`);");

                        $contatos = $this->db->select('userid, id')
                            ->get('tblcontacts')->result_array();

                        $this->db->trans_start();
                        foreach ($contatos as $contact){
                            $this->db->insert('tblclientscontacts', array(
                                'clientid' => $contact['userid'],
                                'contactid' => $contact['id'],
                            ));
                        }
                        $this->db->trans_complete();
                    }
                    if (!$this->db->field_exists('primary_contact', 'tblclients')){
                        $this->db->query('ALTER TABLE `tblclients` ADD `primary_contact` INT NOT NULL AFTER `userid`;');
                        $this->db->query("CREATE INDEX `primari_contact` ON `tblclients` (`primary_contact`);");

                        $contatos = $this->db->select('userid, id')
                            ->where('is_primary', 1)
                            ->get('tblcontacts')->result_array();


                        $this->db->trans_start();
                        foreach ($contatos as $contact){
                            $this->db->where('userid', $contact['userid']);
                            $this->db->update('tblclients', array(
                                'primary_contact' => $contact['id'],
                            ));
                        }
                        $this->db->trans_complete();

                    }
                    add_changelog_item('Matheus Machado Vilarino', '11/04/2018', 'Relação clientes x contatos alterada, possibilitanto a inclusão do mesmo contato em diversas empreas', 'new', '0.1.11');
                }
                else if(!$tipo)
                {
                    if($this->db->table_exists('tblclientscontacts')){
//                        $this->db->query("ALTER TABLE `tblclientscontacts` DROP CONSTRAINT `clientid_fk`;");
//                        $this->db->query("ALTER TABLE `tblclientscontacts` DROP CONSTRAINT `contactid_fk`;");
                        $this->db->query("DROP TABLE `tblclientscontacts`;");
                    }
                    if ($this->db->field_exists('primary_contact', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` DROP `primary_contact`");
                }
            break;
            case "31":
                $description = "Controle da base de conhecimento por departamentos";
                if($tipo)
                {
                    add_changelog_item('Italo Tavares Lima', '9/04/2018', 'Adicionado motivo de parada à tarefas', 'new', '0.1.1');
                    add_changelog_item('Italo Tavares Lima', '12/04/2018', 'Controle de departamentos na base de conhecimento', 'new', '0.1.1');
                    if (!$this->db->field_exists('departments', 'tblknowledgebasegroups'))
                        $this->db->query("ALTER TABLE `tblknowledgebasegroups` ADD `departments` VARCHAR(255) NOT NULL AFTER `group_order`;");
                }
                else if(!$tipo)
                {
                    if($this->db->field_exists('departments', 'tblknowledgebasegroups'))
                        $this->db->query("ALTER TABLE `tblknowledgebasegroups` DROP `departments`");
                }
            break;
            case "32":
                $description = "Bloqueio limite tickets abertos";
                if($tipo)
                {
                    add_changelog_item('Lucas Fontinele', '23/04/2018', 'Opção bloqueio novos tickets', 'new', '0.1.11');
                    add_option("ticket_block_limit_time_attendance","5");
                    add_option("ticket_block_limit_number_attendance","6");
                    add_option('bloqueio_tickets', "0");
                }
                else if(!$tipo)
                {
                    delete_option("ticket_block_limit_time_attendance");
                    delete_option("ticket_block_limit_number_attendance");
                    delete_option("bloqueio_tickets");
                }

            break;
            case "33":
                $description = "Codigo da Empresa(id da empresa criado no cliente";
                if($tipo)
                {
                    add_changelog_item('Matheus Machado', '10/05/2018', 'Adição do código da empresa', 'new', '0.1.12');
                    if (!$this->db->field_exists('cod_empresa', 'tblclients')) {
                        $this->db->query("ALTER TABLE `tblclients` ADD `cod_empresa` INT AFTER `telefone`;");
                    }
                }
                else if(!$tipo)
                {
                    if($this->db->field_exists('cod_empresa', 'tblclients')) {
                        $this->db->query("ALTER TABLE `tblclients` DROP `cod_empresa`");
                    }
                }
            break;
            case "34":
                $description = "Painel de Desenvolvimento";
                if ($tipo) {
                    add_changelog_item('Matheus Machado', '25/05/2018', 'Adição do painel de desenvolvimento', 'new', '0.1.13');
                    add_option("task_waiting_alert_time", "10");
                    add_option("task_waiting_limit_time", "20");
                    add_option('task_waiting_alert_sound', "0");
                    add_option('task_waiting_limit_sound', "0");
                    add_option('task_waiting_alert_sound_type', "0");
                    add_option('task_waiting_limit_sound_type', "0");
                    add_option("task_main_color", MAIN_COLOR);
                    add_option('painel_task_refresh_time', "20");
                    add_option("task_waiting_alert_time_attendance", "30");
                    add_option("task_waiting_limit_time_attendance", "60");
                } else if (!$tipo) {
                    delete_option("task_waiting_alert_time");
                    delete_option("task_waiting_limit_time");
                    delete_option('task_waiting_alert_sound');
                    delete_option('task_waiting_limit_sound');
                    delete_option('task_waiting_alert_sound_type');
                    delete_option('task_waiting_limit_sound_type');
                    delete_option("task_main_color");
                    delete_option('painel_task_refresh_time');
                    delete_option("task_waiting_alert_time_attendance");
                    delete_option("task_waiting_limit_time_attendance");
                }
            break;
//            case "x":case "x":
            case "35":
                $description = "Controle de Equipamentos";
                logActivity("Inicio");
                if($tipo)
                {
                    if(!$this->db->table_exists('tblequipmentmodel'))
                        $this->db->query("CREATE TABLE tblequipmentmodel (id_equip_model int(11) NOT NULL AUTO_INCREMENT, nome varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, PRIMARY KEY (id_equip_model) USING BTREE ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
                    $options = array(
                        'name' => 'Equipamentos',
                        'permission' => 'equip',
                        'icon' => '',
                        'url' => 'equipmens/add_equipments',
                        'id' => 'setup_equipments',
                        'order' => '2');
                    add_setup_menu_item($options);

//                    add_setup_menu_item(array("name"=>"Equipamento","url"=>"equipmens/add_equipments","permission"=>"is_admin","icon"=>"","id"=>"setup_add_equipments","order"=>'2'));
                }
                else if(!$tipo)
                {
                    if ($this->db->table_exists('tblequipmentmodel'))
                        $this->db->query("DROP TABLE tblequipmentmodel;");
                }
            break;
            case "36":
                $description = "Adição de campos Avaliação do Atendimento";
                if($tipo)
                {
                    if(!$this->db->field_exists('nota_sistema', 'tbltickets'))
                        $this->db->query("ALTER TABLE `tbltickets` ADD `nota_sistema` INT AFTER `nota_tecnico`, ADD INDEX (`nota_sistema`);");
                    if(!$this->db->table_exists('tbldescricaoavaliacao')) {
                        $this->db->query("CREATE TABLE tbldescricaoavaliacao (id int(11) NOT NULL AUTO_INCREMENT, ticketid int(11) NOT NULL, nota_atend_desc varchar (255) CHARACTER SET utf8, nota_tecnico_desc varchar (255) CHARACTER SET utf8, nota_sistema_desc varchar (255) CHARACTER SET utf8, PRIMARY KEY (id)) ENGINE = InnoDB;");
                        $this->db->query("ALTER TABLE tbldescricaoavaliacao ADD INDEX (`ticketid`);");
                    }
                }
                else if(!$tipo)
                {
                    if ($this->db->field_exists('nota_sistema', 'tbltickets'))
                        $this->db->query("ALTER TABLE `tbltickets` DROP `nota_sistema`;");
                    if ($this->db->table_exists('tbldescricaoavaliacao'))
                        $this->db->query("DROP TABLE tbldescricaoavaliacao;");
                }
            break;
            case "37":
                $description = "Adiçao de CPF dos Contatos das Empresas(clientes)";
                if($tipo)
                {
                    if(!$this->db->field_exists('cpf', 'tblcontacts'))
                        $this->db->query("ALTER TABLE `tblcontacts` ADD `cpf` VARCHAR(15) AFTER `phonenumber`;");
                }else if(!$tipo)
                {
                    if($this->db->field_exists('cpf', 'tblcontacts'))
                        $this->db->query("ALTER TABLE `tblcontacts` DROP `cpf`;");
                }
            break;
            case "38":
                $description = "Adiçao de campos Cidade e Estado do parceiro";
                if($tipo)
                {
                    if(!$this->db->field_exists('cidade', 'tblpartner'))
                        $this->db->query("ALTER TABLE `tblpartner` ADD `cidade` VARCHAR(50) AFTER `phonenumber`;");
                    if(!$this->db->field_exists('estado', 'tblpartner'))
                        $this->db->query("ALTER TABLE `tblpartner` ADD `estado` VARCHAR(50) AFTER `cidade`;");
                }else if(!$tipo)
                {
                    if($this->db->field_exists('cidade', 'tblpartner'))
                        $this->db->query("ALTER TABLE `tblpartner` DROP `cidade`;");
                    if($this->db->field_exists('estado', 'tblpartner'))
                        $this->db->query("ALTER TABLE `tblpartner` DROP `estado`;");
                }
            break;
            case "39":
                $description = "Adiçao de visibilidade do changelog";
                if($tipo)
                {
                    if(!$this->db->field_exists('visible', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` ADD `visible` BOOLEAN NOT NULL DEFAULT 1 AFTER `slug`;");
                    if(!$this->db->field_exists('rel_type', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` ADD `rel_type` VARCHAR(30) NOT NULL AFTER `visible`, ADD INDEX (`rel_type`);");
                    if(!$this->db->field_exists('rel_id', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` ADD `rel_id` INT NOT NULL AFTER `rel_type`, ADD INDEX (`rel_id`);");
                }else if(!$tipo)
                {
                    if($this->db->field_exists('visible', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` DROP `visible`;");
                    if($this->db->field_exists('rel_type', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` DROP `rel_type`;");
                    if($this->db->field_exists('rel_id', 'changelog'))
                        $this->db->query("ALTER TABLE `changelog` DROP `rel_id`;");
                }
            break;
            case "40":
                $description = "Adição de item de relatório";
                $options = array(
                    'name' => 'Relatório DEV',
                    'permission' => 'is_admin',
                    'icon' => '',
                    'url' => 'dev_report/show',
                    'id' => 'setup_dev',
                    'order' => '3');
                add_main_menu_item($options, 'reports'); //função top kkkk
            break;
            case "41":
                $description = "Adição do campo Observation na tabela changelog_modules";
                if($tipo)
                {
                    if(!$this->db->field_exists('observation', 'changelog_modules'))
                        $this->db->query("ALTER TABLE `changelog_modules` ADD `observation` TEXT AFTER `name`;");
                }else if(!$tipo)
                {
                    if($this->db->field_exists('observation', 'changelog_modules'))
                        $this->db->query("ALTER TABLE `changelog_modules` DROP `observation`;");
                }
            break;
            case "42":
                $description = "Seeding permissões de contatos";
                if($tipo)
                {
                    $contatos = $this->db
                        ->order_by('userid', 'ASC')
                        ->get('tblcontacts')
                        ->result_array();

                    foreach ($contatos as $contato){
                        $has_ticket_permission = $this->db
                            ->where('userid', $contato['id'])
                            ->where('permission_id', 5)
                            ->get('tblcontactpermissions')
                            ->result_array();

                        if(empty($has_ticket_permission)){
                            $this->db->insert('tblcontactpermissions', array(
                                'userid' => $contato['id'],
                                'permission_id' => 5,
                            ));
                        }
                    }
                }
            break;
            case "43":
                $description = "Criação dos campos Versão Retaguarda e Atualiza";
                if($tipo)
                {
                    if(!$this->db->field_exists('versao', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` ADD `versao` VARCHAR(30) AFTER `cod_empresa`;");
                    if(!$this->db->field_exists('atualiza', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` ADD `atualiza` BOOLEAN NOT NULL DEFAULT 0 AFTER `versao`;");
                }else if(!$tipo){
                    if($this->db->field_exists('atualiza', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` DROP `atualiza`;");
                    if($this->db->field_exists('versao', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` DROP `versao`;");
                }
            break;
            case "44":
                $description = "Criação tabela versões";
                if($tipo)
                {
                    if(!$this->db->table_exists('tblversoes')) {
                        $this->db->query("CREATE TABLE `tblversoes` ( `id` INT NOT NULL AUTO_INCREMENT , `executavel` VARCHAR(255) NOT NULL, `versao` VARCHAR(20) NOT NULL, PRIMARY KEY (`id`)) ENGINE = InnoDB;");
                        $this->db->query("ALTER TABLE `tblversoes` ADD `userid` INT NULL AFTER `versao`, ADD INDEX (`userid`);");
                    }

                    if($this->db->field_exists('versao', 'tblclients'))
                        $this->db->query("ALTER TABLE `tblclients` DROP `versao`;");

                }else if(!$tipo){
                    if($this->db->table_exists('tblversoes'))
                        $this->db->query("DROP TABLE `tblversoes`;");
                }
            break;
            case "45":
                $description = "Seed Cod_empresa";
                if($tipo)
                {
                    $clients = $this->db->get('tblclients')->result_array();
                    foreach ($clients as $client){
                        $cod_empresa = explode(' ', $client['company'])[0];
                        if(is_numeric($cod_empresa)){
                            $this->db->where('userid', $client['userid']);
                            $this->db->update('tblclients', array(
                                'cod_empresa' => explode(' ', $client['company'])[0],
                            ));
                        }
                    }

                }
            break;
            case "46":
                $description = "Adicionando campo data de agendamento no ticket";
                if($tipo)
                {
                    if(!$this->db->field_exists('scheduled_date', 'tbltickets'))
                        $this->db->query("ALTER TABLE `tbltickets` ADD `scheduled_date` DATETIME NULL AFTER `date`;");
                }else if(!$tipo){
                    if($this->db->field_exists('scheduled_date', 'tbltickets'))
                        $this->db->query("ALTER TABLE `tbltickets` DROP `scheduled_date`;");
                }
            break;
            case "47":
                $description = "Adicionando campo data de agendamento no ticket";
                if($tipo)
                {
                    $description = "Adição de item de relatório";
                    $options = array(
                        'name' => 'Tipos de Atendimento',
                        'permission' => 'is_admin',
                        'icon' => '',
                        'url' => 'reports/attendance_type_report',
                        'id' => 'attendance_type_report',
                        'order' => '9');
                    add_main_menu_item($options, 'reports'); //funcao top kkkk
                }else if(!$tipo){

                }
            break;
            case "48":
                $description = "Adicionando campo de próximo ticket";
                if($tipo)
                {
                    if(!$this->db->field_exists('is_next_attend', 'tbltickets'))
                        $this->db->query("ALTER TABLE `tbltickets` ADD `is_next_attend` BOOLEAN NOT NULL DEFAULT 0 AFTER `plantao`;");
                }else if(!$tipo){
                    if($this->db->field_exists('is_next_attend', 'tbltickets'))
                        $this->db->query("ALTER TABLE `tbltickets` DROP `is_next_attend`;");
                }
            break;
//            case "x":
//                $description = "";                                                                                            //Descrição da atualização
//                if($tipo)                                                                                                     //Função de instalação
//                {
//                    add_option("","");                                                                                        //Adiciona uma opção na tabela "tbloptions"
//                    $this->update_menu_changelog_knowbase();                                                                  //Atualizar o menu, Base de conhecimento e ChangeLog
//                    $this->db->field_exists('coluna', 'tabela')                                                               //Verifica se a coluna existe
//                    $this->db->table_exists('tabela');                                                                        //Verifica se a tabela existe
//                    $this->db->query("");                                                                                     //Executa uma query SQL
//                    add_changelog_item('Nome', 'data', 'Descrição', 'new/bug/note', 'Verssão', 'Base de Conhecimento Slug');  //Adiciona item no ChangeLog
//                    add_main_menu_item(array("name"=>"","url"=>"/","permission"=>"","icon"=>"","id"=>"","order"=>(:num)));    //Adiciona item no menu principal
//                    remove_main_menu_item($id)                                                                                //Remove um item do menu principal
//                }
//                else if(!$tipo)                                                                                               //Função de desistalação
//                {
//
//                }
//                break;
        }
        if($update) {
            if ($this->db->error()['code'] == 0)
                return array("status"=>"success","description"=>$description);
            else
                return $this->db->error();
        }
        else
            return array("error"=>$this->db->error()['code']);
    }
    public function update()
    {
        $versions = $this->versions();
        foreach ($versions as $version)
        {
            $this->db->query("SELECT installed FROM tbldatabasehistory WHERE version = ".$version)->row('installed');
            if($this->db->affected_rows() == 0)
            {
                $success = $this->comandos($version,true, true);
                if($success["status"] = "success")
                {
                    $this->db->query("INSERT INTO `tbldatabasehistory` (`version`, `description`, `installed`, `date`) VALUES ('" . $version . "', '" . $success["description"] . "', '1', NOW());");
                    update_option('db_version',$version);
                }
            }
        }
    }

//    public function setup_menu_active(){
//        $setup_menu_active = '{"setup_menu_active":[{"name":"Usuário","url":"staff","permission":"manageStaff","icon":"","id":"staff"}, {"name":"clients","url":"#","permission":"manageClients","icon":"","id":"customers","children":[ {"name":"customer_groups","url":"clients\/groups","permission":"","icon":"","id":"groups"}]},{"name":"support","url":"#","permission":"manageDepartments","icon":"","id":"tickets","children":[{"name":"acs_departments","url":"departments","permission":"manageDepartments","icon":"","id":"departments"},{"name":"acs_ticket_predefined_replies_submenu","url":"tickets\/predefined_replies","permission":"is_admin","icon":"","id":"predefined-replies"},{"id":"ticket-priority","name":"Prioridade da Solicitação","url":"tickets\/priorities","permission":"is_admin","icon":""},{"id":"ticket-statuses","name":"Status da Solicitação","url":"tickets\/statuses","permission":"is_admin","icon":""},{"name":"acs_ticket_services_submenu","url":"tickets\/services","permission":"is_admin","icon":"","id":"services"},{"name":"Serviços nível 2","url":"tickets\/servicesnv2","permission":"is_admin","icon":"","id":"services"},{"name":"spam_filters","url":"tickets\/spam_filters","permission":"is_admin","icon":"","id":"spam-filters"}]},{"name":"acs_leads","url":"#","permission":"is_admin","icon":"","id":"leads","children":[{"name":"acs_leads_sources_submenu","url":"leads\/sources","permission":"","icon":"","id":"sources"},{"name":"acs_leads_statuses_submenu","url":"leads\/statuses","permission":"","icon":"","id":"statuses"},{"name":"leads_email_integration","url":"leads\/email_integration","permission":"","icon":"","id":"email-integration"},{"name":"web_to_lead","permission":"is_admin","icon":"","url":"leads\/forms","id":"web-to-lead"}]},{"name":"acs_finance","url":"#","permission":"manageSales","icon":"","id":"finance","children":[{"name":"acs_sales_taxes_submenu","url":"taxes","permission":"","icon":"","id":"taxes"},{"name":"acs_sales_currencies_submenu","url":"currencies","permission":"","icon":"","id":"currencies"},{"name":"acs_sales_payment_modes_submenu","url":"paymentmodes","permission":"","icon":"","id":"payment-modes"},{"name":"acs_expense_categories","url":"expenses\/categories","permission":"","icon":"","id":"expenses-categories"}]},{"name":"acs_contracts","url":"#","permission":"manageContracts","icon":"","id":"contracts","children":[{"name":"acs_contract_types","url":"contracts\/types","permission":"","icon":"","id":"contract-types"}]},{"name":"Tarefas","url":"#","permission":"tasks","icon":"","id":"tasks","children":[{"name":"Grupos Padrao","url":"tasks\/groups","permission":"","icon":"","id":"tasks-groups"}]},{"name":"acs_email_templates","url":"emails","permission":"editEmailTemplates","icon":"","id":"email-templates"},{"name":"asc_custom_fields","url":"custom_fields","permission":"is_admin","icon":"","id":"custom-fields"},{"name":"acs_roles","url":"roles","permission":"manageRoles","icon":"","id":"roles"},{"name":"menu_builder","url":"#","permission":"is_admin","icon":"","id":"menu-builder","children":[{"name":"main_menu","url":"utilities\/main_menu","permission":"","icon":"","id":"organize-sidebar"},{"name":"setup_menu","url":"utilities\/setup_menu","permission":"is_admin","icon":"","id":"setup-menu"}]},{"name":"theme_style","permission":"is_admin","icon":"","url":"utilities\/theme_style","id":"theme-style"},{"name":"acs_settings","url":"settings","permission":"editSettings","icon":"","id":"settings"}]}';
//        $this->db->query("UPDATE `tbloptions` SET `value` = '" . $setup_menu_active . "' WHERE `tbloptions`.`name` = 'setup_menu_active';");
//    }

    public function update_menu_changelog_knowbase()
    {
        //Menu
//        $menu = '{"aside_menu_active":[{"name":"als_dashboard","url":"\/","permission":"","icon":"fa fa-tachometer","id":"dashboard"},{"name":"als_clients","url":"clients","permission":"customers","icon":"fa fa-users","id":"customers"},{"name":"Parceiros","url":"partner","permission":"partner","icon":"fa fa-handshake-o","id":"partners"},{"name":"als_sales","url":"#","permission":"proposals","icon":"fa fa-balance-scale","id":"sales","children":[{"name":"proposals","url":"proposals","permission":"proposals","icon":"","id":"child-proposals"},{"name":"estimates","url":"estimates\/list_estimates","permission":"estimates","icon":"","id":"child-estimates"},{"name":"invoices","url":"invoices\/list_invoices","permission":"invoices","icon":"","id":"child-invoices"},{"name":"payments","url":"payments","permission":"payments","icon":"","id":"child-payments"},{"name":"items","url":"invoice_items","permission":"items","icon":"","id":"child-items"}]},{"name":"als_expenses","url":"expenses\/list_expenses","permission":"expenses","icon":"fa fa-heartbeat","id":"expenses"},{"name":"als_contracts","url":"contracts","permission":"contracts","icon":"fa fa-file","id":"contracts"},{"name":"projects","url":"projects","permission":"projects","icon":"fa fa-bars","id":"projects"},{"name":"als_tasks","url":"tasks\/list_tasks","permission":"","icon":"fa fa-tasks","id":"tasks"},{"name":"support","url":"tickets","permission":"","icon":"fa fa-ticket","id":"tickets"},{"name":"als_leads","url":"leads","permission":"leads","icon":"fa fa-tty","id":"leads"},{"name":"Controle de frota","url":"fleet","permission":"fleet","icon":"fa fa-car","id":"fleet"},{"name":"als_kb","url":"knowledge_base","permission":"knowledge_base","icon":"fa fa-folder-open-o","id":"knowledge-base"},{"name":"als_utilities","url":"#","permission":"utilities","icon":"fa fa-cogs","id":"utilities","children":[{"name":"als_media","url":"utilities\/media","permission":"","icon":"","id":"child-media"},{"name":"bulk_pdf_exporter","url":"utilities\/bulk_pdf_exporter","permission":"bulk_pdf_exporter","icon":"","id":"child-bulk-pdf-exporter"},{"name":"als_calendar_submenu","url":"utilities\/calendar","permission":"","icon":"","id":"child-calendar"},{"name":"als_goals_tracking","url":"goals","permission":"goals","icon":"","id":"child-goals-tracking"},{"name":"als_surveys","url":"surveys","permission":"surveys","icon":"","id":"child-surveys"},{"name":"als_announcements_submenu","url":"announcements","permission":"is_admin","icon":"","id":"child-announcements"},{"name":"utility_backup","url":"utilities\/backup","permission":"is_admin","icon":"","id":"child-database-backup"},{"name":"als_activity_log_submenu","url":"utilities\/activity_log","permission":"is_admin","icon":"","id":"child-activity-log"},{"name":"ticket_pipe_log","url":"utilities\/pipe_log","permission":"is_admin","icon":"","id":"ticket-pipe-log"},{"name":"Mudancas do Sistema","url":"utilities\/changelog","permission":"","icon":"","id":"changelog"}]},{"name":"als_reports","url":"#","permission":"reports","icon":"fa fa-area-chart","id":"reports","children":[{"name":"Relat\\\u00f3rio de Cargas","url":"utilities\/load_report","permission":"reports","icon":"","id":"child-load_report"},		{"name":"Relat\\\u00f3rio de Atendimentos","url":"utilities\/attendance_report","permission":"reports","icon":"","id":"child-attendance_report"},{"name":"Avaliação dos Técnicos","url":"utilities\/technician_evaluation","permission":"reports","icon":"","id":"technician_evaluation"},{"name":"Avaliação dos Atendimentos","url":"utilities\/attend_evaluation","permission":"reports","icon":"","id":"attend_evaluation"},{"name":"timesheets_overview","url":"staff\/timesheets?view=all","permission":"reports","icon":"","id":"reports_timesheets_overview"},{"name":"Gráficos","url":"utilities\/chart","permission":"reports","icon":"","id":"charts"}]}]}';
//        $this->db->query("UPDATE `tbloptions` SET `value` = '".$menu."' WHERE `tbloptions`.`name` = 'aside_menu_active';");

        //Base de Conhecimento
//        $this->db->query("DELETE FROM `tblknowledgebase` WHERE `articlegroup` = 777");
//        $base ="INSERT INTO `tblknowledgebase` (`articleid`, `articlegroup`, `subject`, `description`, `slug`, `active`, `datecreated`, `article_order`, `staff_article`, `views`) VALUES
//            (35, 777, 'Gráficos', 'Para acessar os relat&oacute;rios/gr&aacute;ficos, <span>realize os procedimentos abaixo:</span><br />\r\n<ol>\r\n<li><span>No menu lateral v&aacute; em&nbsp;<em><strong>Relat&oacute;rios-&gt;Gr&aacute;ficos</strong></em>.</span></li>\r\n</ol>', 'graficos', 1, '2018-01-02 08:56:16', 0, 1, NULL),
//            (36, 777, 'Como configurar eventos ao mudar estado do Ticket', 'Para configurar a cria&ccedil;&atilde;o de <strong>evento no calend&aacute;rio</strong>&nbsp;ao mudar estado do Ticket<span>, realize os procedimentos abaixo:</span><br />\r\n<ol>\r\n<li><span>No menu lateral v&aacute; em <em><strong>Defini&ccedil;&otilde;es-&gt;Suporte-&gt;Status</strong><strong> da Solicita&ccedil;&atilde;o</strong></em>.<br /></span></li>\r\n<li><span>Escolha o Status da Solicita&ccedil;&atilde;o que deseja configurar e clique em editar.</span></li>\r\n<li><span>Selecione a caixa <strong>Criar Evento(Colaborador).</strong></span></li>\r\n<li><span>Clique em <em><strong>Salvar.</strong></em></span></li>\r\n</ol>\r\n<span>Para&nbsp;configurar o envio de email&nbsp;ao mudar estado do Ticket, realize os procedimentos abaixo:</span>\r\n<ol>\r\n<li><span>No menu lateral v&aacute; em <em><strong>Defini&ccedil;&otilde;es-&gt;Suporte-&gt;Status</strong><strong> da Solicita&ccedil;&atilde;o</strong></em>.<br /></span></li>\r\n<li><span>Escolha o Status da Solicita&ccedil;&atilde;o que deseja configurar e clique em editar.</span></li>\r\n<li><span>Selecione a caixa  <strong>Enviar email(Colaborador).</strong></span></li>\r\n<li>Clique em <em style=\"font - size: 12pt;\"><strong>Salvar.</strong></em></li>\r\n</ol>', 'como-configurar-eventos-ao-mudar-estado-do-ticket', 1, '2017-12-26 16:32:20', 0, 1, NULL),
//            (39, 777, 'Controle de frota', '<p>O controle de Frota CRM &eacute; dividido da seguinte forma:</p>\r\n<ol>\r\n<li>Sa&iacute;da.\r\n<ol>\r\n<li>Regras.<br />\r\n<ul>\r\n<li>A distancia final deve ser maior que a inicial.</li>\r\n<li>O condutor deve ser habilitado.</li>\r\n<li>Todos os campos obrigat&oacute;rios devem estar preenchidos.</li>\r\n<li>Para finalizar, dever&aacute; indicar a data e o Km final.</li>\r\n</ul>\r\n</li>\r\n<li>Alertas.\r\n<ul>\r\n<li>Antes de salvar &eacute; notificado a distancia total percorrida.</li>\r\n<li>Caso o percusso ultrapasse 300Km &eacute; mostrada uma mensagem de alerta.</li>\r\n</ul>\r\n</li>\r\n</ol>\r\n</li>\r\n<li>Ve&iacute;culos.\r\n<ol>\r\n<li>Cadastro.</li>\r\n<li>Edi&ccedil;&atilde;o.</li>\r\n<li>Exclus&atilde;o.\r\n<ol>\r\n<li>Ao excluir um ve&iacute;culo, todos os dados referentes a ele tamb&eacute;m ser&atilde;o exclu&iacute;dos.</li>\r\n</ol>\r\n</li>\r\n<li>Dashboard.\r\n<ol>\r\n<li>Resumo do Ve&iacute;culo.\r\n<ul>\r\n<li>Distancia total.</li>\r\n<li>Distancia mensal.</li>\r\n<li>Ultimo uso.</li>\r\n<li>Sa&iacute;das.</li>\r\n<li>Abastecimentos.</li>\r\n</ul>\r\n</li>\r\n<li>Dados do Ve&iacute;culo.</li>\r\n<li>Dados Detran.\r\n<ul>\r\n<li>As informa&ccedil;&otilde;es do ve&iacute;culo registradas no site do Detran ser&atilde;o mostradas como, D&eacute;bitos,&nbsp;<span>Infra&ccedil;&otilde;es em Autua&ccedil;&atilde;o, Penalidades (Multas) e&nbsp;Recursos de Infra&ccedil;&atilde;o.</span></li>\r\n</ul>\r\n</li>\r\n</ol>\r\n</li>\r\n<li>Abastecimento.</li>\r\n</ol>\r\n</li>\r\n<li>Colaboradores.\r\n<ol>\r\n<li>Documenta&ccedil;&atilde;o.</li>\r\n</ol>\r\n</li>\r\n</ol>\r\nPara registrar um ve&iacute;culo, siga os procedimentos abaixo:<br />\r\n<ol>\r\n<li>No menu lateral esquerdo clique em <strong><em>Controle de Frota.</em></strong></li>\r\n<li>Na parte superior clique em <em><strong>ve&iacute;culos</strong></em>.</li>\r\n<li>Na parte superior clique em <strong><em>Registrar Ve&iacute;culo</em></strong>.</li>\r\n<li>Ap&oacute;s preencher os dados do ve&iacute;culo clique em <strong><em>salvar</em></strong>.</li>\r\n</ol>\r\nPara excluir um ve&iacute;culo, siga os procedimentos abaixo:<br />\r\n<ol>\r\n<li>No menu lateral esquerdo clique em <strong><em>Controle de Frota.</em></strong></li>\r\n<li>Na parte superior clique em <em><strong>ve&iacute;culos</strong></em>.</li>\r\n<li>No ve&iacute;culo desejado clique no <strong><em>X</em></strong> no canto direito na linha referente ao mesmo.</li>\r\n<li>Confirme a exclus&atilde;o.</li>\r\n</ol>\r\nPara editar um ve&iacute;culo, siga os procedimentos abaixo:<br />\r\n<ol>\r\n<li>No menu lateral esquerdo clique em <strong><em>Controle de Frota.</em></strong></li>\r\n<li>Na parte superior clique em <em><strong>ve&iacute;culos</strong></em>.</li>\r\n<li>No ve&iacute;culo desejado clique no <strong><em>Lapis</em></strong> no canto direito na linha referente ao mesmo.</li>\r\n<li>Ap&oacute;s preencher os dados do ve&iacute;culo clique em <strong><em>Atualizar&nbsp;</em></strong>no final da p&aacute;gina.</li>\r\n</ol>\r\nPara registrar um abastecimento, siga os procedimentos abaixo:<br />\r\n<ol>\r\n<li>No menu lateral esquerdo clique em <strong><em>Controle de Frota.</em></strong></li>\r\n<li>Na parte superior clique em <em><strong>ve&iacute;culos</strong></em>.</li>\r\n<li>No ve&iacute;culo desejado clique no <b><i>fogo(azul)&nbsp;</i></b>no canto direito na linha referente ao mesmo.</li>\r\n<li>Ap&oacute;s preencher os dados do ve&iacute;culo clique em <strong><em>Salvar&nbsp;</em></strong>no final da p&aacute;gina.</li>\r\n</ol>\r\n<br />Para atualizar os dados referentes a habilita&ccedil;&atilde;o do colaborador, siga os procedimentos abaixo:<br />\r\n<ol>\r\n<li>No menu lateral esquerdo clique em <strong><em>Controle de Frota.</em></strong></li>\r\n<li>Na parte superior clique em <em><strong>Colaboradores</strong></em>.</li>\r\n<li>Escolha o colaborador que deseja alterar e clique nele.</li>\r\n<li>Na aba <em><strong>Documenta&ccedil;&atilde;o&nbsp;</strong></em>preencha os dados correspondentes.</li>\r\n<li>Clique em <em><strong>Salvar</strong></em>.</li>\r\n</ol>\r\nPara registrar uma sa&iacute;da, siga os procedimentos abaixo:<br />\r\n<ol>\r\n<li>No menu lateral esquerdo clique em <strong><em>Controle de Frota</em></strong>&nbsp;ou na <em><strong>seta</strong> </em>referente ao ve&iacute;culo desejado.</li>\r\n<li>Na parte superior clique em <em><strong>Sa&iacute;da de frotas</strong></em>.</li>\r\n<li>Preencha os dados correspondentes.\r\n<ul>\r\n<li>Para inserir um registro completo, insira os dados finais.</li>\r\n<li>Para marcar uma sa&iacute;da em tempo real deixe as informa&ccedil;&otilde;es finais em branco.</li>\r\n</ul>\r\n</li>\r\n<li>Clique em <em><strong>Salvar</strong></em>&nbsp;ou&nbsp;<em><strong>Iniciar.</strong></em></li>\r\n</ol>\r\nPara ver dados de um ve&iacute;culo como sa&iacute;das, abastecimentos etc, siga os procedimentos abaixo:<br />\r\n<ol>\r\n<li>No menu lateral esquerdo clique em <strong><em>Controle de Frota.</em></strong></li>\r\n<li>Na parte superior clique em <em><strong>ve&iacute;culos</strong></em>.</li>\r\n<li>No ve&iacute;culo desejado clique no <strong><em>Lapis</em></strong> no canto direito na linha referente ao mesmo.</li>\r\n</ol>', 'controle-de-frota', 1, '2018-01-19 11:50:33', 0, 1, NULL),
//            (38, 777, 'Notificação em tela cheia', '<p>A notifica&ccedil;&atilde;o em tela cheia cria uma janela na frente de tudo o que o usu&aacute;rio estiver fazendo exibindo uma mensagem.</p>\r\n<p>Para criar uma notifica&ccedil;&atilde;o em tela cheia,&nbsp;<span>&nbsp;</span><span>realize os procedimentos abaixo:</span></p>\r\n<ol>\r\n<li><span>No menu lateral v&aacute; em&nbsp;<em><strong>Utilidades-&gt;Notifica&ccedil;&otilde;es</strong></em>.</span></li>\r\n<li><span>Clique em <em><strong>Novo.</strong></em></span></li>\r\n<li><strong></strong>Selecione o destinat&aacute;rio.<br />\r\n<ul>\r\n<li>Obs: &Eacute; poss&iacute;vel enviar para apenas um, ou para todos os colaboradores.</li>\r\n</ul>\r\n</li>\r\n<li>Defina a mensagem.</li>\r\n<li>Clique em <em><strong>Enviar</strong></em>.</li>\r\n</ol>', 'notificacao-em-tela-cheia', 1, '2018-01-03 09:00:04', 0, 1, NULL),
//            (37, 777, 'Atribuição automática de destino e seguidores da tarefa', '<div style=\"text - align: justify;\">Para criar uma regra de atribui&ccedil;&atilde;o autom&aacute;tica de colaboradores para a tarefa,<span>realize os procedimentos abaixo:<br /></span>\r\n<ol>\r\n<li><span>No menu lateral v&aacute; em&nbsp;</span><em><strong>Defini&ccedil;&otilde;es-&gt;Tarefas-&gt;Grupos Padr&atilde;o</strong></em><span>.</span></li>\r\n<li><span>Selecione as pessoas para o tipo(<em><strong>Destinat&aacute;rios, Seguidores</strong></em>).</span>\r\n<ul>\r\n<li><span>Obs: Ao selecionar uma pessoa ela &eacute; adicionada automaticamente.</span></li>\r\n</ul>\r\n</li>\r\n</ol>\r\n<ul>\r\n<li>Para excluir basta clicar em cima da foto do colaborador.</li>\r\n</ul>\r\nAgora, toda vez que uma tarefa for criada atribuir&aacute; automaticamente os respectivos colaboradores.</div>', 'atribuicao-automatica-de-destino-e-seguidores-da-tarefa', 1, '2017-12-26 17:08:29', 0, 1, NULL);
//        ";
//        $this->db->query($base);
//        if(PAINEL == QUANTUM)
//            $this->db->query("INSERT INTO `tblknowledgebase` (`articleid`, `articlegroup`, `subject`, `description`, `slug`, `active`, `datecreated`, `article_order`, `staff_article`, `views`) VALUES (41, 777, 'Mudança Tickets', '<ul>\r\n<li>O campo <em><strong>Assunto</strong> </em>foi movido para a coluna direita e renomeada para&nbsp;<em><strong>Complemento</strong></em>&nbsp;e <em><strong>N&Atilde;O ser&aacute; mais</strong></em>&nbsp;<em><strong>o</strong><strong>brigat&oacute;rio</strong></em>; Ao digitar um valor no <em><strong>campo</strong></em>, o <em><strong>Ticket</strong> </em>assumir&aacute; o mesmo como identifica&ccedil;&atilde;o principal.</li>\r\n<li>Os campos <em><strong>Servi&ccedil;o</strong></em> e <em><strong>Servi&ccedil;o n&iacute;vel 2</strong></em> foram movidos para a coluna esquerda e ser&atilde;o obrigat&oacute;rios quando o status do Ticket for alterado para&nbsp;<span><em><strong>SUP - ATENDIDO</strong></em>.</span></li>\r\n<li>O campo <em><strong>Contato</strong></em> &eacute; reservado para o nome de contato do Ticket n&atilde;o sendo mais necess&aacute;rio colocar no <em><strong>Complemento</strong></em>.</li>\r\n<li>Caso queira adicionar uma <em>informa&ccedil;&atilde;o relacionada ao Ticket</em>(telefone,email,<em>observa&ccedil;&atilde;o do ticket</em>), na aba <em><strong>Adicionar Nota</strong></em> digite a informa&ccedil;&atilde;o. As notas s&atilde;o privadas para os <em>colaboradores </em>e aparecer&atilde;o na aba&nbsp;<strong><em>Adicionar Resposta</em></strong>.</li><li>O filtro padr&atilde;o na tela de listagem de Tickets foi alterado&nbsp;para os usu&aacute;rios que tiverem a Fun&ccedil;&atilde;o definida como <em><strong>Suporte</strong> </em>para: <em><strong>Meus Tickets</strong></em>, <em><strong>SUP - ESPERA</strong></em>, <em><strong>SUP - ATENDIMENTO</strong></em>, <em><strong>SUP - PENDENTE</strong></em>, os demais usu&aacute;rios continuar&atilde;o o mesmo.&nbsp;&nbsp;</li><li>Foi adicionado um tempo de 5 minutos de perman&ecirc;ncia ao alternar entre os status na tela de listagem de Tickets.</li>\r\n</ul>', 'mudanca-tickets', 1, '2018-01-26 13:02:44', 0, 1, NULL);");

    }
}