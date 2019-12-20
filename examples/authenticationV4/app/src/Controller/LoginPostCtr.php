<?php
namespace WebApp\Controller;

use Dflydev\FigCookies\FigRequestCookies;

class LoginPostCtr {
  private $app;
  private $login;
  private $UserModel;
  
  public function __construct(\WebApp\AppService $app, \WebApp\LoginService $login, \WebApp\Model\UserModel $UserModel) {
    $this->app = $app;
    $this->login = $login;
    $this->UserModel = $UserModel;
  }
  
  public function __invoke($request, $response, $args) {  
    $post = $request->getParsedBody();
    
    // verifica se esiste un utente con U e P passate in POST
    $user = $this->UserModel->getUser($post["U"], $post["P"]);
    if (!$user)
      return $response->withRedirect($this->app->router->urlFor("MESSAGE", [
        "id" => "login-failed"
      ]));

    // in caso positivo, setta il token (nel cookie e nell'attributo token del service login)
    $response = $this->login->setAuthToken($user, $response);
    
    return $response->withRedirect($this->app->router->urlFor("PROFILO"));
  }
}