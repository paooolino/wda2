<?php
namespace WebApp\Controller;

class LoginPostCtr {
  private $router;
  private $login;
  private $UserModel;
  
  public function __construct($router, $login, $UserModel) {
    $this->router = $router;
    $this->login = $login;
    $this->UserModel = $UserModel;
  }
  
  public function __invoke($request, $response, $args) {  
    $post = $request->getParsedBody();
    $user = $this->UserModel->getUser($post["U"], $post["P"]);

    if (!$user)
      return $response->withRedirect($this->router->pathFor("MESSAGE", [
        "id" => "login-failed"
      ]));

    $response = $this->login->setAuthToken($user, $response);
    
    return $response->withRedirect($this->router->pathFor("PROFILO"));
  }
}