<?php

namespace spec\Slim;

use PhpSpec\ObjectBehavior;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;

class AppSpec extends ObjectBehavior {
    
  private function do_request($uri, $method) {
    $settings = require __DIR__ . '/../../settings.php';
    $this->beConstructedWith($settings);
    $container = $this->getContainer();
    
    $app = $this;
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
    $resOut = $this->process($req, $res);
    
    return $resOut->getBody()->__toString();
  }

  function it_shows_form_in_home() {
    $html = $this->do_request('/', 'GET');

    $html->shouldContain("<form");
  }

}
