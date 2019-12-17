<?php
namespace WebApp\Middleware;

class AuthMiddleware {
  private $router;
  private $login;
  
  public function __construct($router, $login) {
    $this->router = $router;
    $this->login = $login;
  }
  
  public function __invoke($request, $handler) {
    $response = $handler->handle($request);
    
    $result = $this->login->checkAuth($request);
    if (!$result) {
      return $response->withRedirect($this->router->urlFor("HOME"));
    }
    
    return $response;
  } 
}