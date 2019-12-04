<?php

namespace spec\Slim;

use PhpSpec\ObjectBehavior;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;

class AppSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
      $settings = require __DIR__ . '/../../settings.php';
      $app = new App($settings);
      $container = $app->getContainer();
      require __DIR__ . '/../../app/dependencies.php';
      require __DIR__ . '/../../app/middleware.php';
      require __DIR__ . '/../../app/routes.php';      
    
      $env = Environment::mock([
        'SCRIPT_NAME' => '/index.php',
        'REQUEST_URI' => '/',
        'REQUEST_METHOD' => 'GET',
      ]);
      
      $uri = Uri::createFromEnvironment($env);
      $headers = Headers::createFromEnvironment($env);
      $cookies = [];
      $serverParams = $env->all();
      $body = new RequestBody();
      $req = new Request('GET', $uri, $headers, $cookies, $serverParams, $body);
      $res = new Response();
      $resOut = $app->process($req, $res);
      
      echo (string)$resOut->getBody();
      //$this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $resOut);
      //$this->assertEquals('2', (string)$resOut->getBody());
      //  $this->shouldHaveType(App::class);
    }
}
