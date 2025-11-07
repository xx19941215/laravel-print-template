<?php

return [
    // 用户模型类路径
    'user_model' => env('PRINT_TEMPLATE_USER_MODEL', 'App\\Models\\Admin'),
    // 路由配置
    'routes' => [
        [
            'method' => 'get',
            'uri' => 'printTemplate/list',
            'action' => [\Xx19941215\PrintTemplate\Controllers\PrintTemplateController::class, 'getList'],
            'middlewares' => ['auth:gw'],
            'name' => 'print-template.list'
        ],
        [
            'method' => 'post',
            'uri' => 'printTemplate/create',
            'action' => [\Xx19941215\PrintTemplate\Controllers\PrintTemplateController::class, 'create'],
            'middlewares' => ['auth:gw'],
            'name' => 'print-template.create'
        ],
        [
            'method' => 'get',
            'uri' => 'printTemplate',
            'action' => [\Xx19941215\PrintTemplate\Controllers\PrintTemplateController::class, 'getInfo'],
            'middlewares' => ['auth:gw'],
            'name' => 'print-template.show'
        ],
        [
            'method' => 'post',
            'uri' => 'printTemplate/update',
            'action' => [\Xx19941215\PrintTemplate\Controllers\PrintTemplateController::class, 'update'],
            'middlewares' => ['auth:gw'],
            'name' => 'print-template.update'
        ],
        [
            'method' => 'post',
            'uri' => 'printTemplate/delete',
            'action' => [\Xx19941215\PrintTemplate\Controllers\PrintTemplateController::class, 'delete'],
            'middlewares' => ['auth:gw'],
            'name' => 'print-template.delete'
        ],
    ],
];