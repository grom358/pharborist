<?php
namespace Example;

trait SayWorld {
  public function sayHello() {
    parent::sayHello();
    echo 'World!';
  }
}
