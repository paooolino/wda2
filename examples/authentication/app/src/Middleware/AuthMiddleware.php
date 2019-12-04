<?php
namespace WebApp\Middleware;

class AuthMiddleware {
    
  public function __construct() {
    //
  }
  
  public function __invoke($request, $response, $next) {
    return $next($request, $response);
  } 
}