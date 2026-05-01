<?php

Route::group([
    'middleware' => ['web', 'auth', 'roles'],
    'prefix'     => \Helper::getSubdirectory(),
    'namespace'  => 'Modules\PretixIntegration\Http\Controllers',
], function () {
    Route::post('/pretixintegration/ajax', [
        'uses'     => 'PretixController@ajax',
        'laroute'  => true,
    ])->name('pretixintegration.ajax');
});
