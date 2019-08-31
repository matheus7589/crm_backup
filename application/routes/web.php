<?php

use DSisconeto\Ciroute\Route;

Route::set_default_controller('clients/');




Route::any('viewinvoice', 'clients/viewinvoice');
Route::any('viewinvoice/(:num)/(:any)', 'clients/viewinvoice/$1/$2');
Route::any('viewestimate/(:num)/(:any)', 'clients/viewestimate/$1/$2');
Route::any('viewestimate', 'clients/viewestimate');
Route::any('viewproposal/(:num)/(:any)', 'clients/viewproposal/$1/$2');
Route::any('survey/(:num)/(:any)', 'clients/survey/$1/$2');
Route::any('knowledge_base', 'clients/knowledge_base');
Route::any('knowledge_base/(:any)', 'clients/knowledge_base/$1');
Route::any("knowledge_base/export/(:num)", "ExportPDF/exportpdf_knowledge_base/$1");

