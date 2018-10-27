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
use WilliamWei\LaravelRPC\Exceptions\InvalidParametersException;


class ServiceProviderController extends BaseController
{
    /**
     * @param Request $request
     * @return mixed
     * @throws InvalidParametersException
     */
    public function handle(Request $request) {
        $rules =  [
            'provider'  =>  'required | string',
            'function'  =>  'required | string',
            'parameters'    =>  'required | array',
        ];
        $validator = app('validator')->make($request->all(), $rules);

        if ($validator->fails())
        {
           throw new InvalidParametersException($validator->errors());
        }

        $payload = $request->all(['provider', 'function', 'parameters']);
        $provider = app(config('rpc.service_providers').ucfirst($payload['provider']));
        $res = call_user_func_array([$provider, $payload['function']], $payload['parameters']);
        return response()->json([
            'status'    =>  $res === false ? 'fail': 'ok',
            'result'    =>  json_encode($res),
        ]);
    }

}