<?php
/*
  Samples
  
  $app->get('/', 'WebApp\Controller\HomeController')->setName('HOME');
  $app->post('/login', 'WebApp\Controller\LoginPostController')->setName('LOGIN_POST');
  
  $app->group('', function($app) {
    $app->get('/reserved-area', 'WebApp\Controller\ReservedAreaController')->setName('RESERVED_AREA');
  })->add('WebApp\Middleware\AuthMiddleware');
*/
