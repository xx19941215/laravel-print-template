<?php

use Illuminate\Support\Facades\Route;

// 获取路由配置
$routes = config('print_template.routes', []);

// 注册路由
foreach ($routes as $route) {
    // 确保 method 是数组形式，以支持字符串和数组两种配置方式
    $methods = is_array($route['method']) ? $route['method'] : [$route['method']];
    Route::match($methods, $route['uri'], $route['action'])
        ->middleware($route['middlewares'] ?? [])
        ->name($route['name'] ?? '');
}