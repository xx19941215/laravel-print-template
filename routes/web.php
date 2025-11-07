<?php

use Illuminate\Support\Facades\Route;

// 获取路由配置
$routes = config('print_template.routes', []);

// 注册路由
foreach ($routes as $route) {
    Route::match([$route['method']], $route['uri'], $route['action'])
        ->middleware($route['middlewares'] ?? [])
        ->name($route['name'] ?? '');
}