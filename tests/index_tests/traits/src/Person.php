<?php
namespace Example;

class Person {
  private $name;

  public function __construct($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function sayHello() {
    return 'Hello my name is ' . $this->name;
  }
}
