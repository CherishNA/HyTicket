<?php

use Illuminate\Routing\Router;
use App\Admin\Controllers;

Admin::registerAuthRoutes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('hy/ticketsign', CheckSignController::class);
    $router->resource('hy/ysyDaily', YsyDailyController::class);
    $router->resource('hy/ysySubject', YsySubjectController::class);
    $router->resource('hy/ysyClass', YsyClassController::class);
    $router->resource('hy/ysyOrder', YsyOrderController::class);
    $router->resource('hy/ysyProject', YsyProjectController::class);
    $router->resource('hy/ysyCode', YsyTicketController::class);
});
