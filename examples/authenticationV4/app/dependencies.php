<?php

$container->set('router', $app->getRouteCollector()->getRouteParser());

$container->set('view', function ($c) {
  $templatePath = __DIR__ . '/../templates/default';
  $templateUrl = "";
  return new Slim\Views\PhpRenderer($templatePath, [
    // common data available in templates
    "templateUrl" => $templateUrl,
    "router" => $c->get('router')
  ]);
});


//
// Services
//

$container->set('app', function ($c) {
  return new WebApp\AppService();
});

$container->set('login', function ($c) {
  return new WebApp\LoginService();
});


//
// Middlewares
//

$container->set('WebApp\Middleware\AuthMiddleware', function ($c) {
  return new WebApp\Middleware\AuthMiddleware($c->get('router'), $c->get('login'));
});


//
// Controllers
//

$container->set('WebApp\Controller\HomeCtr', function ($c) {
  return new WebApp\Controller\HomeCtr($c->get('view'));
});

$container->set('WebApp\Controller\LoginPostCtr', function ($c) {
  return new WebApp\Controller\LoginPostCtr($c->get('router'), $c->get('login'), $c->get('UserModel'));
});

$container->set('WebApp\Controller\MsgCtr', function ($c) {
  return new WebApp\Controller\MsgCtr($c->get('view'), $c->get('MessageModel'));
});

$container->set('WebApp\Controller\ProfiloCtr', function ($c) {
  return new WebApp\Controller\ProfiloCtr($c->get('view'), $c->get('UserModel'));
});


//
// Models
//

$container->set('UserModel', function ($c) {
  return new WebApp\Model\UserModel();
});

$container->set('MessageModel', function ($c) {
  return new WebApp\Model\MessageModel();
});

