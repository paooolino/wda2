<?php

$container['view'] = function ($c) {
  $templatePath = __DIR__ . '/../templates/default';
  $templateUrl = $c->request->getUri()->getBaseUrl();
  return new Slim\Views\PhpRenderer($templatePath, [
    // common data available in templates
    "templateUrl" => $templateUrl,
    "router" => $c->router
  ]);
};


//
// Services
//

$container['app'] = function ($c) {
  return new WebApp\AppService();
};

$container['login'] = function ($c) {
  return new WebApp\LoginService();
};


//
// Middlewares
//

$container['WebApp\Middleware\AuthMiddleware'] = function ($c) {
  return new WebApp\Middleware\AuthMiddleware();
};


//
// Controllers
//

$container['WebApp\Controller\HomeCtr'] = function ($c) {
  return new WebApp\Controller\HomeCtr($c->view);
};

$container['WebApp\Controller\LoginPostCtr'] = function ($c) {
  return new WebApp\Controller\LoginPostCtr($c->router, $c->login, $c->UserModel);
};

$container['WebApp\Controller\MsgCtr'] = function ($c) {
  return new WebApp\Controller\MsgCtr($c->view, $c->MessageModel);
};

$container['WebApp\Controller\ProfiloCtr'] = function ($c) {
  return new WebApp\Controller\ProfiloCtr($c->view, $c->UserModel);
};


//
// Models
//

$container['UserModel'] = function ($c) {
  return new WebApp\Model\UserModel();
};

$container['MessageModel'] = function ($c) {
  return new WebApp\Model\MessageModel();
};

