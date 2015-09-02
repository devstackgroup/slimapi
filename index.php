<?php   

require 'vendor/autoload.php';

use SlimApi\JSONApi\View as JSONApiView;
use SlimApi\JSONApi\Middleware as JSONApiMiddleware;
use SlimApi\JSONRequest\Middleware as JSONRequestMiddleware;

$app = new \Slim\Slim();
$app->config('debug', false);
$app->view((new JSONApiView($app))->setEncodingJSON(JSON_PRETTY_PRINT));
$app->add(new JSONApiMiddleware($app));
$app->add(new JSONRequestMiddleware());

$app->get('/', function() use ($app) {
    $app->render(200, [
        'msg' => 'Welcome to SlimAPI!'
    ]);
});

$app->post('/', function() use ($app) {
    $app->render(200, [
        'msg' => "Welcome to SlimAPI {$app->JSONBody['msg']}!"
    ]);
});
     
$app->run();