<?php   

require './vendor/autoload.php';

use SlimApi\View as View;
use SlimApi\Middleware as Middleware;

$app = new \Slim\Slim();
$app->config('debug', false);
$app->view((new View($app))->setEncodingJSON(JSON_PRETTY_PRINT));
$app->add(new Middleware($app));

$app->get('/', function() use ($app) {
    $app->render(200, [
        'msg' => 'Welcome to SlimAPI!'
    ]);
});
     
$app->run();