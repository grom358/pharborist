<?php
namespace Example;

class BaseObject implements StringObject {
  use ObjectUtil;

  public function toString() {
    return 'Base';
  }

  public function sayHello() {
    echo 'hello', PHP_EOL;
  }
}
