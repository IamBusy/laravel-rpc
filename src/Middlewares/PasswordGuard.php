<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/10/27
 * Time: 7:27 PM
 */

namespace WilliamWei\LaravelRPC\Middlewares\Guard;


use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PasswordGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if ($request->get('internal_code') != config('rpc.internal.code')) {
            throw new UnauthorizedHttpException('非法的访问');
        }

        return $next($request);
    }

}