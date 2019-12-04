<?php
namespace WebApp\Controller;

class HomeCtr {
  private $view;
  
  public function __construct($view) {
    $this->view = $view;
  }
  
  public function __invoke($request, $response, $args) {  
    return $this->view->render($response, 'home.php', [
      // add data here
    ]);
  }
}