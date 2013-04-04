<?php

class DbConfig { 
  private static $instance; 

  private $params;

  private function __construct() {
    $this->params = parse_ini_file("db.ini");
  } 

  public function getParams() {
    return $this->params;
  }

  public static function getInstance() { 
    if(!self::$instance) { 
      self::$instance = new self(); 
    } 

    return self::$instance; 
  } 
} 

?>