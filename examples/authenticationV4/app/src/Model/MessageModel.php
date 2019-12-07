<?php
namespace WebApp\Model;

class MessageModel {
  
  private $messages;
  private $defaultMessage;
  
  public function __construct() {
    $this->messages = [
      "login-failed" => [
        "title" => "Errore",
        "body" => "Credenziali errate."
      ]
    ];
    
    $this->defaultMessage = [
      "title" => "Errore sconosciuto",
      "body" => "Si è verificato un errore non gestito."
    ];
  }
  
  public function get($id) {  
    if (!isset($this->messages[$id]))
      return $this->defaultMessage;
    return $this->messages[$id];
  }
}