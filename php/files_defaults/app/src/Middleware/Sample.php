<?php
namespace WebApp\Middleware;

class {{classname}} {
    
  public function __construct() {
    //
  }
  
  public function __invoke($request, $response, $next) {
    return $next($request, $response);
  } 
}