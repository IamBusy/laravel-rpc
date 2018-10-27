<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/9/23
 * Time: 11:18 AM
 */

namespace WilliamWei\LaravelRPC\Message;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class HttpTransport
{
    private function getClient() {
        return new Client([
            'timeout'   =>  3,
        ]);
    }

    /**
     * 同步调用
     * @param $uri
     * @param $data
     * @return bool|mixed
     */
    public function transfer($uri, $data) {
        $client = $this->getClient();
        $resp = $client->post($uri, [
            'json'  =>  $data,
        ]);
        if ($resp->getStatusCode() != 200) {
            throw new ServiceUnavailableHttpException('服务不可用', $resp);
        }
        $data = $resp->getBody();
        if ($data['status'] == 'ok') {
            return json_decode($data['result']);
        }
        return false;
    }

    /**
     * 异步调用
     * @param $uri
     * @param $data
     */
    public function asyncTransfer($uri, $data, $callback) {
        $client = $this->getClient();
        $promise = $client->postAsync($uri, [
            'json'  =>  $data,
        ]);
        $promise->then(function($resp) use ($callback) {
            $data = $resp->getBody();
            if ($data['status'] == 'ok') {
                $callback($data['result']);
            }
        }, function($resp) {
            Log::error('Service async call failed', $resp);
        });
    }

}