<?php

$app->get('/', WebApp\Controller\HomeCtr::class)->setName('HOME');
$app->post('/login', WebApp\Controller\LoginPostCtr::class)->setName('LOGIN_POST');
$app->get('/message/{id}', WebApp\Controller\MsgCtr::class)->setName('MESSAGE');

$app->group('', function($app) {
  $app->get('/profilo', WebApp\Controller\ProfiloCtr::class)->setName('PROFILO');
})->add(WebApp\Middleware\AuthMiddleware::class);
