<?php

namespace Xx19941215\PrintTemplate;

use Illuminate\Support\ServiceProvider;

class PrintTemplateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__.'/../config/print_template.php' => config_path('print_template.php'),
        ], 'print_template-config');

        // 发布迁移文件
        $this->publishes([
            __DIR__.'/../migrations/' => database_path('migrations'),
        ], 'print_template-migrations');

        // 注册路由
        $this->registerRoutes();
    }

    /**
     * 注册路由
     *
     * @return void
     */
    protected function registerRoutes()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../routes/web.php';
        }
    }
}