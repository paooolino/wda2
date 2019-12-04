<?php
namespace WebApp\Controller;

class ProfiloCtr {
  private $view;
  private $UserModel;
  
  public function __construct($view, $UserModel) {
    $this->view = $view;
    $this->UserModel = $UserModel;
  }
  
  public function __invoke($request, $response, $args) {  
    return $this->view->render($response, 'profile.php', [
      // add data here
    ]);
  }
}