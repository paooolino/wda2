<?php

namespace spec\Slim;

use PhpSpec\ObjectBehavior;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\Cookie;

class AppSpec extends ObjectBehavior {
  
  function let() {
    $settings = require __DIR__ . '/../../settings.php';
    $this->beConstructedWith($settings);
    $container = $this->getContainer();
    
    $app = $this;
    require __DIR__ . '/../../app/dependencies.php';
    require __DIR__ . '/../../app/middleware.php';
    require __DIR__ . '/../../app/routes.php';   
  }
  
  private function do_request($uri, $method, $post_params=[], $cookies=[]) {  
    $env = Environment::mock([
      'SCRIPT_NAME' => '/index.php',
      'REQUEST_URI' => $uri,
      'REQUEST_METHOD' => $method,
    ]);
    
    $uri = Uri::createFromEnvironment($env);
    $headers = Headers::createFromEnvironment($env);
    $serverParams = $env->all();
    $body = new RequestBody();
    $req = new Request($method, $uri, $headers, $cookies, $serverParams, $body);
    if (!empty($post_params)) {
      $req = $req->withParsedBody($post_params);
    }
    if (!empty($cookies)) {
      foreach($cookies as $k => $v) {
        $req = FigRequestCookies::set($req, Cookie::create($k, $v));
      }
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
    
    $cookie = FigResponseCookies::get($res->getWrappedObject(), 'token');
    $cookies = [];
    $cookies[$cookie->getName()] = $cookie->getValue();
    
    $res = $this->do_request('/profilo', 'GET', [], $cookies);
    
    $res->getStatusCode()->shouldBe(200);
    $res->getBody()->__toString()->shouldContain("<h1>Profilo utente</h1>");
  }
  
  function it_should_not_authenticate_wrong_credentials() {
    $res = $this->do_request('/login', 'POST', [
      "U" => "demo",
      "P" => "wrong password"
    ]);
    
    $res->getStatusCode()->shouldBe(302);
    $res->getHeaderLine('Location')->shouldBe('/message/login-failed');
  }
  
  function it_should_redirect_home_if_invalid_cookie() {
    $res = $this->do_request('/profilo', 'GET', [], [
      "token" => "malformed token value"
    ]);
    
    echo $res->getBody()->getWrappedObject();
    $res->getStatusCode()->shouldBe(302);
    $res->getHeaderLine('Location')->shouldBe('/');
  }
}
