<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/10/27
 * Time: 7:23 PM
 */

namespace WilliamWei\LaravelRPC\Providers;


use Illuminate\Support\ServiceProvider;

class RPCProvider extends ServiceProvider
{
    public function register() {
        // Merge config.
        $this->mergeConfigFrom(__DIR__ . '/../../config/rpc.php', 'rpc');
        $this->mergeConfigFrom(__DIR__ . '/../../config/server.php', 'server');
    }

    /**
     * 在注册后进行服务的启动。
     *
     * @return void
     */
    public function boot() {
        $this->publishes([
            __DIR__.'/../../config/rpc.php' => config_path('rpc.php'),
            __DIR__.'/../../config/server.php' => config_path('server.php')
        ]);
    }

}