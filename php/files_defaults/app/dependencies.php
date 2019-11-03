<?php

$container['view'] = function ($c) {
  $templatePath = __DIR__ . '/../templates/' . $c->settings["templateName"];
  return new Slim\Views\PhpRenderer($templatePath, [
    // common data available in templates
    "router" => $c->router
  ]);
};

//
// Services
//

/*
$container['app'] = function ($c) {
  return new WebApp\App();
};
*/

//
// Middlewares
//

/*
$container['WebApp\Middleware\Sample'] = function ($c) {
  return new WebApp\Middleware\Sample($c->app);
};
*/

//
// Controllers
//

/*
$container['WebApp\Controller\Sample'] = function ($c) {
  return new WebApp\Controller\Sample($c->app);
};
*/

//
// Models
//

/*
$container['WebApp\Model\Sample'] = function ($c) {
  return new WebApp\Model\Sample($c->app);
};
*/
