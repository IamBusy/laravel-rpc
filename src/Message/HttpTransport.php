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
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class HttpTransport
{
    private function getClient() {
        return new Client([
            'timeout'   =>  3,
        ]);
    }

    /**
     * @param ResponseInterface $response
     * @return mixed|null
     */
    protected function parseResFromResp(ResponseInterface $response) {
        $cont = $response->getBody()->getContents();
        if (strlen($cont) > 0) {
            try{
                $res = json_decode($cont, true);
                if (array_key_exists('status', $res) && $res['status'] == 'ok') {
                    return json_decode($res['result']);
                } else {
                    Log::error('RPC call failed', $res);
                }
            } catch (\Exception $exception) {
                Log::error('Exception occurred when parse results from response', [
                    'content'   =>  $cont,
                    'exception' =>  $exception,
                ]);
                return null;
            }
        }
        Log::notice('Invalid response format parsed!');
        return null;
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
        return $this->parseResFromResp($resp);
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
            $callback($this->parseResFromResp($resp));
        }, function($resp) {
            Log::error('Service async call failed', $resp);
        });
    }

}