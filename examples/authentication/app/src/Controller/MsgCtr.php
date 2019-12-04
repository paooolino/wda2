<?php
namespace WebApp\Controller;

class MsgCtr {
  private $view;
  
  public function __construct($view, $MessageModel) {
    $this->view = $view;
  }
  
  public function __invoke($request, $response, $args) {  
    return $this->view->render($response, 'message.php', [
      // add data here
    ]);
  }
}