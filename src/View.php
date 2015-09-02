<?php

namespace SlimApi;

use Slim\Slim;

/**
* View class
*/
class View extends \Slim\View
{
	private $app = null;

	private $encodingJSON = 0;

	private static $contentType = [
		'json'  => 'application/json',
		'jsonp' => 'application/javascript'
	];

	public function __construct(Slim $app = null)
    {
		$this->app = ($app instanceof Slim) ? $app : Slim::getInstance();
        parent::__construct();
	}

	public function render($status = 200, $data = null)
	{
		
        $app = $this->app;

		$status = (int) $status;

		$JSONResponse = $this->all();

        if (!$this->has('error')) {
            $JSONResponse['error'] = false;
        }

        $JSONResponse['status'] = $status;

		if(isset($this->data->flash) && is_object($this->data->flash)){
            $flashMessages = $this->data->flash->getMessages();
            if (count($flashMessages)) {
                $JSONResponse['flash'] = $flashMessages;
            } else {
                unset($JSONResponse['flash']);
            }
        }

        $JSONResponse = isset($JSONResponse[0]) && count($JSONResponse) === 1 && is_scalar($JSONResponse[0]) ? $JSONResponse[0] : $JSONResponse;

   		$app->response()->status($status);
        $app->response()->header('Content-Type', View::$contentType['json']);

        $jsonp_callback = $app->request->get('callback', null);

        if ($jsonp_callback !== null) {
        	$app->response()->header('Content-Type', View::$contentType['jsonp']);
            $app->response()->body(call_user_func_array($jsonp_callback, [json_encode($JSONResponse, $this->encodingJSON)]));
        } else {
            $app->response()->body(json_encode($JSONResponse, $this->encodingJSON));
        }

        $app->stop();
	}

    public function setEncodingJSON($encodingCode)
    {
        $this->encodingJSON = $encodingCode;
        return 
    }
}