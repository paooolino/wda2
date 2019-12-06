<?php
namespace WebApp\Middleware;

class AuthMiddleware {
  private $router;
  private $login;
  
  public function __construct($router, $login) {
    $this->router = $router;
    $this->login = $login;
  }
  
  public function __invoke($request, $response, $next) {
    $result = $this->login->checkAuth($request);
    
    if (!$result) {
      return $response->withRedirect($this->router->pathFor("MESSAGE", [
        "id" => "login-fail"
      ]));
    }
    
    return $next($request, $response);
  } 
}