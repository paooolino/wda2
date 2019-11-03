<?php
namespace WebApp\Controller;

class {{classname}} {
  private $view;
  
  public function __construct($view) {
    $this->view = $view;
  }
  
  public function __invoke($request, $response, $args) {  
    return $this->view->render($response, 'template.php', [
      // add data here
    ]);
  }
}