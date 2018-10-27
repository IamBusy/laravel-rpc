<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/10/27
 * Time: 7:14 PM
 */

namespace WilliamWei\LaravelRPC\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller AS BaseController;


class ServiceProviderController extends BaseController
{
    public function handle(Request $request) {
        $payload = $this->validate($request, [
            'provider'  =>  'required | string',
            'function'  =>  'required | string',
            'parameters'    =>  'required | array',
        ]);
        $provider = app(config('app.rpc.service_providers').ucfirst($payload['provider']));
        $res = call_user_func_array([$provider, $payload['function']], $payload['parameters']);
        return response()->json([
            'status'    =>  $res === false ? 'fail': 'ok',
            'result'    =>  json_encode($res),
        ]);
    }

}