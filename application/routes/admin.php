<?php

use DSisconeto\Ciroute\Route;

Route::group(['prefix' => 'admin', 'folder' => 'admin'], function () {
    Route::group(["prefix" => "painel/atendimento", 'controller' => 'painel_atendimento'], function () {
        Route::get("", "index")->name("painel.atedimento.index");
        Route::get("dados", "data")->name("painel.atendimento.dados");
    });

    Route::group(["prefix" => "painel/desenvolvimento", 'controller' => 'painel_desenvolvimento'], function () {
        Route::get("", "index")->name("painel.desenvolvimento.index");
        Route::get("dados", "data")->name("painel.desenvolvimento.dados");
    });

    Route::group(["prefix" => "clients", 'controller' => 'clients'], function () {
        Route::any("senha/quantum", "senha_quantum");
    });
    Route::group(['prefix' => 'tickets/validacao', 'controller' => 'tickets'], function () {
        Route::post('', 'insere')->name("tickets.insere");
    });
    Route::group(['prefix' => 'tickets', 'controller' => 'tickets'], function () {
        Route::any('tickets_dev', 'show')->name('tickets-desenvolvimento.show');
        Route::any('set_temp', 'set_temp_list_tickets')->name('set_temp');
        Route::any('take_attend', 'take_attend')->name('take_attend');
    });

    Route::get('tickets/validacao', 'tickets/valida_dias')->name('tickets.valida_dias');
    Route::post('sendmail', 'emails/sendmail');
    Route::post('tickets/ticket/(:num)/correcao', 'tickets/correcao/$1');
    Route::any('/', 'home');
    Route::any('access_denied', 'misc/access_denied');
    Route::any('not_found', 'misc/not_found');
    Route::any('profile', 'staff/profile');
    Route::any('profile/(:num)', 'staff/profile/$1');

    Route::group(['prefix' => 'tasks', 'controller' => 'tasks'], function () {
        Route::any('view/(:any)', 'index/$1');
        Route::any('groups', 'groups');
        Route::any('addgp', 'addgp');
        Route::any("config_painel", "config_painel")->name("config.painel.desenvolvimento");
        Route::post('insere', 'insere')->name("tasks.insere");
    });

    Route::group(['prefix' => 'equipmens', 'controller' => 'equipments'], function () {
        Route::any('','index');
        Route::any('add','add');
        Route::any('add_equipments','add_equipments_models');
        Route::post('addother','addother');
        Route::post('add_equip_in','add_equip_in');
        Route::post('add_equip_out','add_equip_out');
        Route::post('out_to_in','out_to_in');
        Route::post('in_to_return','in_to_return');
        Route::group(['prefix' => 'patrimony'], function () {
            Route::any('','index_patrimony');
            Route::get('(:num)/(:num)','patrimony_out/$1/$2');
            Route::post('category','patrimony_category');
        });
    });

    Route::group(['prefix' => 'movies', 'controller' => 'movies'], function () {
        Route::any('', 'index');
        Route::get('subcategory', 'subcategory');
        Route::any('player/(:num)', 'get_media/$1');
        Route::any('(:num)', 'edit/$1');
        Route::post('delete', 'delete');
        Route::any('movies/categories', 'categories');
    });

    Route::group(['prefix' => 'fleet', 'controller' => 'fleet_control'], function () {
        Route::any('','index');
        Route::any('vehicles','vehicles');
        Route::any('vehicles/(:num)','vehicles_single/$1');
        Route::post('vehicles/add_vehicle','add_vehicle');
        Route::post('vehicles/delete','delete_vehicle');
        Route::post('vehicles/update_vehicle','update_vehicle');
        Route::post('vehicles/supply','add_supply');
        Route::get('vehicles/change_vehicle_status/(:num)/(:num)','change_vehicle_status/$1/$2');
        Route::get('get_vehicle/(:num)','get_vehicle/$1');
        Route::get('get_vehicle_out/(:num)','get_vehicle_out/$1');
        Route::any('get_out/(:num)','get_out/$1');
        Route::post('out','fleet_out');
        Route::post('out/delete','out_delete');
        Route::any('get_relation_value', 'get_relation_value');

    });

    Route::group(['prefix' => 'tarefas', 'controller' => 'tasks'], function () {
        Route::get('', 'list_tasks')->name("tarefas.index");
        Route::get('(:num)', 'index/$1');
    });

    Route::group(["prefix" => "tarefas/parada", 'controller' => 'stop_timers'], function () {
        Route::get('', 'create')->name("parar-tarefas.create");
        Route::post('', 'store')->name("parar-tarefas.store");
        Route::post('(:num)/destroy', 'destroy/$1')->name("parar-tarefas.destroy");
        Route::post('parar', 'stop')->name("parar-tarefas.stop");
    });

    Route::group(['prefix' => 'reports'], function () {
        Route::any('equipments', 'reports/equipments');
    });

    Route::group(["prefix" => "reports", 'controller' => 'Attendance_Type_Report'], function () {
        Route::any('attendance_type_report', 'show')->name('attendance.report.show');
        Route::get('get_atendimentos', 'get_atendimentos')->name('attendance.report.get.atendimentos');
        Route::get('get_services_percentage', 'get_services_percentage')->name('attendance.report.get.services.percentage');
        Route::get('get_tickets_from_services', 'get_tickets_from_services')->name('attendance.report.get.tickets.from.services');
        Route::get('get_second_service_percentage', 'get_second_service_percentage')->name('attendance.report.get.second.service.percentage');
        Route::get('get_tickets_from_servicesnv1', 'get_tickets_from_servicesnv1')->name('attendance.report.get.tickets.service.nv1');
        Route::get('get_tickets_from_servicesnv2', 'get_tickets_from_servicesnv2')->name('attendance.report.get.tickets.service.nv2');
    });

    Route::group(['prefix' => 'utilities'], function (){
        Route::any('fleet_report', 'reports/fleet_control');
        Route::any('supply', 'reports/supply');
        Route::get('read_master_notification', 'utilities/read_master_notification');
        Route::group(['prefix' => 'load_report', 'controller' => 'load_report'], function () {
            Route::get('', 'load')->name('relatorio_cargas');
            Route::get('kan_ban', 'kan_ban_load')->name('relatorio_cargas.kan_ban');
        });

        Route::group(['prefix' => 'attendance_report', 'controller' => 'attendance_report'], function () {
            Route::any('', 'show')->name('relatorio-atendimentos.show');
            Route::post('get_Replies_Tickets_Formated', 'get_Replies_Tickets_Formated')->name('relatorio-atendimentos.get_Replies_Tickets_Formated');
            Route::post('show_formated', 'show_formated')->name('relatorio-atendimentos.show_formated');
        });

        Route::group(['prefix' => 'number_attends', 'controller' => 'number_attends'], function () {
            Route::any('', 'show')->name('numero-atendimentos.show');
            Route::post('get_ticket_data', 'get_ticket_data')->name('numero-atendimentos.get_ticket_data');
            Route::any('sub_table', 'sub_table')->name('numero-atendimentos.sub_table');
        });

        Route::group(['prefix' => 'changelog', 'controller' => 'changelog'], function () {
            Route::any('', 'show')->name('Mudancas.show');
            Route::any('edit', 'edit');
            Route::post('cadas_module', 'addmodule');
        });

        Route::group(['prefix' => 'attend_by_technician', 'controller' => 'attend_by_technician'], function (){
            Route::any('', 'show')->name('atendimento-por-tecnico.show');
        });

        Route::group(['prefix' => 'dev_report', 'controller' => 'Dev_report'], function (){
            Route::any('', 'show')->name('dev-report.show');
        });

        Route::group(['prefix' => 'attend_average_time', 'controller' => 'Attend_average_time'], function (){
            Route::any('', 'show')->name('atendimento-tempo-medio.show');
            Route::post('get_replies_times_formated', 'get_Replies_Times_Formated');
            Route::post('show_formated', 'show_formated')->name('atendimento-tempo-medio.show_formated');
        });

        Route::group(['prefix' => 'attend_evaluation', 'controller' => 'Attend_evaluation'], function (){
            Route::any('', 'show')->name('avaliacao-atendimento.show');
        });

        Route::group(['prefix' => 'chart', 'controller' => 'Charts'], function (){
            Route::any('', 'index');
            Route::post('get', 'get_data');
        });

        Route::group(['prefix' => 'technician_evaluation', 'controller' => 'Technician_evaluation'], function (){
            Route::any('', 'show')->name('avaliacao-tecnico.show');
        });

        Route::group(['prefix' => 'no_request_clients', 'controller' => 'No_request_clients'], function (){
           Route::any('', 'show')->name('no-request-clients.show');
           Route::get('connect_contact/(:num)', 'connect_contact/$1');
        });
    });

    Route::group(['prefix' => 'whatsapp', 'controller' => 'whatsapp'], function () {
        Route::any('', 'show')->name('Whats.show');
        Route::get('messages/(:num)', 'getMessages/$1')->name('Whats.msg');
    });

    Route::group(['prefix' => 'partner', 'controller' => 'partner'], function () {
        Route::any('', 'show')->name('Parceiro.show');
        Route::any('add', 'add')->name('Parceiro.add');
        Route::any('(:num)', 'edit/$1')->name('Parceiro.edit');
        Route::any('alter/(:num)', 'alter/$1')->name('Parceiro.alter');
        Route::post('delete', 'delete')->name('Parceiro.delete');
        Route::any('change_partner_status/(:num)/(:num)', 'change_partner_status/$1/$2');
    });

    Route::group(['prefix' => 'announcements', 'controller' => 'announcements'], function () {
        Route::get('change_to_department', 'change_to_department');
        Route::get('change_to_staff', 'change_to_staff');
    });

});