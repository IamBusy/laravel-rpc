<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/10/27
 * Time: 7:58 PM
 */
namespace WilliamWei\LaravelRPC;

use Illuminate\Support\Facades\Route;
use WilliamWei\LaravelRPC\Middlewares\Guard\PasswordGuard;

class RPC
{
    public static function routes() {
        Route::middleware(PasswordGuard::class)
            ->post('serviceProvider', 'WilliamWei\LaravelRPC\Controllers\ServiceProviderController@handle');
    }

}