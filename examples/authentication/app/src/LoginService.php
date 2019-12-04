<?php
namespace WebApp;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;

class LoginService {
  private $token;
  
  /**
   * The password used for encryption function.
   */
  const PASSWORD_ENCRYPTION = "Qof}zzW7;NAt(#l&BBxWI0T=9XR]dL8-)z~g_J+pFPbMdzuQPf({E_ysC?_{lHvq";
  
  /**
   * The authentication cookie name.
   */
  const AUTH_COOKIE_NAME = "token";
  
  /**
   * Validity of session for generated access token
   */
  const TOKEN_TIME_LIMIT = 60 * 30;
  
  public function __construct() {
    // do nothing
  }

  public function setAuthToken($infos, $response) {
    $token = $this->createToken(time(), $infos);
    
    $this->token = $token;
    $response = $this->setAuthCookie($token, $response);
    
    return $response;
  }
  
  private function createToken($time_info, $user_info) {
    $token = $this->encrypt(json_encode([
      "t" => $time_info,
      "u" => $user_info
    ]));
    $token = base64_encode($token);
    
    return $token;
  }
  
  private function setAuthCookie($token, $response) {
    $response = FigResponseCookies::set($response, 
      SetCookie::create(self::AUTH_COOKIE_NAME)
        ->withValue($token)
        ->withPath('/')
    );
    
    return $response;
  }
  
  private function encrypt($s) {
    return openssl_encrypt($s, "AES-256-ECB", self::PASSWORD_ENCRYPTION);
  }
  
  private function decrypt($s) {
    return openssl_decrypt($s, "AES-256-ECB", self::PASSWORD_ENCRYPTION);
  }
}