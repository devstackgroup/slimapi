<?php

namespace SlimApi\JSONRequest;

use Slim\Slim;

/**
* Middleware class
*/
class Middleware extends \Slim\Middleware
{
    private $config = [];

	public function __construct(array $config = [])
	{
        $this->config = array_merge(['isObject' => false], $config);
	}

    public function call(){
        $app = $this->app;
        $app->hook('slim.before.router', function () use ($app) {
            $body = $app->request->getBody();
            if ($app->request->getMediaType() == 'application/json' && !empty($body)) {
                try {
                    $params = json_decode($body, !$this->config['isObject']);
                } catch (\ErrorException $e) {
                    $msg = sprintf('Unknown error occured: %s, JSON: %s',
                                    str_replace("json_decode(): ", "", $e->getMessage()), 
                                    $body);
                    throw new \UnexpectedValueException($msg);
                }
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \UnexpectedValueException("POST body is not JSON format: $body");
                }
                $app->JSONBody = $params;
            } else {
                $app->JSONBody = $this->config['isObject'] ? null : [];
            }
        });
        $this->next->call();
    }
}