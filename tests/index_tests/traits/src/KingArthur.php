<?php
namespace Example;

class KingArthur extends Person {
  use Ni;

  public function __construct() {
    parent::__construct('King Arthur');
  }

  public function sayHello() {
    return parent::sayHello();
  }

  public function quote() {
    return 'Look, you stupid bastard, you\'ve got no arms left!';
  }
}
