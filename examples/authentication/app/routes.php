<?php

$app->get('/', 'WebApp\Controller\HomeCtr')->setName('HOME');
$app->post('/login', 'WebApp\Controller\LoginPostCtr')->setName('LOGIN_POST');
$app->get('/message/{id}', 'WebApp\Controller\MsgCtr')->setName('MESSAGE');

$app->group('', function($app) {
  $app->get('/profilo', 'WebApp\Controller\ProfiloCtr')->setName('PROFILO');
})->add('WebApp\Middleware\AuthMiddleware');

