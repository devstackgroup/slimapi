<?php

namespace SlimApi;

use Slim\Slim;

/**
* Middleware class
*/
class Middleware extends \Slim\Middleware
{
    private static function errorType($code = 1)
    {
        switch ($code) {
            default:
            case E_ERROR: 
                return 'ERROR';
            case E_WARNING: 
                return 'WARNING';
            case E_PARSE: 
                return 'PARSE';
            case E_NOTICE: 
                return 'NOTICE';
            case E_CORE_ERROR: 
                return 'CORE_ERROR';
            case E_CORE_WARNING: 
                return 'CORE_WARNING';
            case E_COMPILE_ERROR: 
                return 'COMPILE_ERROR';
            case E_COMPILE_WARNING: 
                return 'COMPILE_WARNING';
            case E_USER_ERROR: 
                return 'USER_ERROR';
            case E_USER_WARNING: 
                return 'USER_WARNING';
            case E_USER_NOTICE: 
                return 'USER_NOTICE';
            case E_STRICT: 
                return 'STRICT';
            case E_RECOVERABLE_ERROR: 
                return 'RECOVERABLE_ERROR';
            case E_DEPRECATED: 
                return 'DEPRECATED';
            case E_USER_DEPRECATED: 
                return 'USER_DEPRECATED';
            case E_ALL:
            	return 'ALL';
        }
    }

	public function __construct(Slim $app = null)
	{
		$app = ($app instanceof Slim) ? $app : Slim::getInstance();

		$app->get('/request', function () use ($app) {
			$app->render(200, [
				'method'  => $app->request()->getMethod(),
                'name'    => $app->request()->get('name'),
                'headers' => $app->request()->headers(),
                'params'  => $app->request()->params()
			]);
		});

		$app->error(function (\Exception $e) use ($app) {
            $statusCode = empty($e->getCode()) ? 500 : $e->getCode();
            $errorMessage = ini_get('display_errors') === '1' ? $this->errorMessage($e) : 'Server error';
            $app->render($statusCode, [
                'error' => $errorMessage
            ]);
        });

        $app->notFound(function () use ($app) {
            $app->render(404, [
                'error' => 'Invalid route'
            ]);
        });

        $app->hook('slim.after.router', function () use ($app) {
            if ($app->response()->header('Content-Type') === 'application/octet-stream') {
                return;
            }

            if ($app->response()->getStatus() != 304 && strlen($app->response()->body()) == 0) {
                $app->render(204);
            }
        });
	}

    public function call(){
        return $this->next->call();
    }

	private function errorMessage($e)
	{
		return Middleware::errorType($e->getCode()).": {$e->getMessage()} - in file: {$e->getFile()} on line: {$e->getLine()}";
	}
}