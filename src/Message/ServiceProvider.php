<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2018/9/22
 * Time: 6:18 PM
 */

namespace WilliamWei\LaravelRPC\Services\Message;


use function GuzzleHttp\Psr7\parse_query;
use Illuminate\Support\Facades\Log;
use WilliamWei\LaravelRPC\Exceptions\InvalidParametersException;
use WilliamWei\LaravelRPC\Message\HttpTransport;

class ServiceProvider
{
    private $apis = [];

    private $transport;

    public function __construct(HttpTransport $transport)
    {
        $this->apis = config('server');
        $this->transport = $transport;
    }


    /**
     * 调用远程服务
     * @param $server
     * @param $entryPoints
     * @param array $parameters
     * @return bool|mixed
     * @throws InvalidParametersException
     */
    public function call($server, $entryPoints, $parameters = []) {
        Log::info('ServiceProvider call remote service', [
            'server'    =>  $server,
            'entryPoints'   =>  $entryPoints,
            'parameters'    =>  $parameters
        ]);
        if (! array_key_exists($server, $this->apis)) {
            throw new InvalidParametersException(sprintf('无效的服务提供者[%s]', $server));
        }
        $url = $this->apis[$server]['api']['base'];
        $code = $this->apis[$server]['code'];
        $url = $this->buildUrl($url, [
            'internal_code' => $code,
        ]);
        $entry = explode('.', $entryPoints);
        if (count($entry) != 2) {
            throw new InvalidParametersException('服务点必须为provider.func格式');
        }
        return $this->transport->transfer($url, [
            'provider' => $entry[0],
            'function' => $entry[1],
            'parameters' => $parameters
        ]);
    }

    protected function buildUrl($u, $q = []) {
        $parts = parse_url($u);

        if (array_key_exists('query', $parts)) {
            $parts['query'] = parse_query($parts['query']) + $q;
        } else {
            $parts['query'] = $q;
        }

        if (array_key_exists('fragment', $parts) && $idx = strpos($parts['fragment'], '?') !== false) {
            $fragmentAndQuery = explode('?', $parts['fragment'], 2);
            $parts['fragment'] = $fragmentAndQuery[0];
            parse_str($fragmentAndQuery[1], $query);
            if ($query && count($query) > 0) {
                $parts['query'] += $query;
            }
        }
        if (array_key_exists('port', $parts)) {
            $parts['host'] = sprintf('%s:%s', $parts['host'], $parts['port']);
        }
        $url = sprintf('%s://%s%s', $parts['scheme'], $parts['host'], $parts['path']);
        if (array_key_exists('fragment', $parts)) {
            $url .= '#'.$parts['fragment'];
        }
        if (array_key_exists('query', $parts)) {
            $url .= '?'.http_build_query($parts['query']);
        }
        return $url;
    }
}