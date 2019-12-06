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
    
  private function do_request($uri, $method, $post_params=[]) {
    $settings = require __DIR__ . '/../../settings.php';
    $this->beConstructedWith($settings);
    $container = $this->getContainer();
    
    $app = $this;
    require __DIR__ . '/../../app/dependencies.php';
    require __DIR__ . '/../../app/middleware.php';
    require __DIR__ . '/../../app/routes.php';     
    
    $env = Environment::mock([
      'SCRIPT_NAME' => '/index.php',
      'REQUEST_URI' => $uri,
      'REQUEST_METHOD' => $method,
    ]);
    
    $uri = Uri::createFromEnvironment($env);
    $headers = Headers::createFromEnvironment($env);
    $cookies = [];
    $serverParams = $env->all();
    $body = new RequestBody();
    $req = new Request($method, $uri, $headers, $cookies, $serverParams, $body);
    if (!empty($post_params)) {
      $req = $req->withParsedBody($post_params);
    }
    $res = new Response();
    $resOut = $this->process($req, $res);
    
    return $resOut;
  }

  function it_shows_form_in_home() {
    $res = $this->do_request('/', 'GET');

    $res->getBody()->__toString()->shouldContain("<form");
  }
  
  function it_should_redirect_to_home_if_not_logged() {
    $res = $this->do_request('/profilo', 'GET');

    $res->getStatusCode()->shouldBe(302);
    $res->getHeaderLine('Location')->shouldBe('/');
  }
  
  function it_should_authenticate_and_redirect() {
    $res = $this->do_request('/login', 'POST', [
      "U" => "demo",
      "P" => "demo"
    ]);
    
    $res->getStatusCode()->shouldBe(302);
    $res->getHeaderLine('Set-Cookie')->shouldStartWith('token=');
    $res->getHeaderLine('Location')->shouldBe('/profilo');
  }
  
  function it_should_not_authenticate() {
    $res = $this->do_request('/login', 'POST', [
      "U" => "demo",
      "P" => "wrong password"
    ]);
    
    $res->getStatusCode()->shouldBe(302);
    $res->getHeaderLine('Location')->shouldBe('/message/login-failed');
  }
}
