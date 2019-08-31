<?php
use DSisconeto\Ciroute\Route;


Route::group(["prefix" => 'clientes'], function () {


    Route::get("autologin/(:any)/(:any)", "clients_autologin/autologin/$1/$2")->name("clientes.autologin");
    Route::post('retaguarda_crm/test', 'retaguarda_crm/test');
    Route::any('retaguarda_crm/client_retaguarda', 'retaguarda_crm/client_retaguarda');
    Route::any('retaguarda_crm/contato_retaguarda', 'retaguarda_crm/contato_retaguarda');
    Route::any('retaguarda_crm/delete_contact_retaguarda', 'retaguarda_crm/delete_contact_retaguarda');
    Route::any('retaguarda_crm/set_versao', 'retaguarda_crm/set_versao');
    Route::any('retaguarda_crm/after_atualiza', 'retaguarda_crm/after_atualiza');

});


Route::any('/', 'clients');
Route::any('movies', 'clients/movies');
Route::any('movies/player/(:num)', 'clients/player/$1');