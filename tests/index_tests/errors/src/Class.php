<?php
namespace Example;

interface Say {
  /**
   * @param string $name
   */
  public function say($name);
}

trait SayWorld {
  public function say($name) {
    echo 'Hello World!', PHP_EOL;
  }
}

class SayHello implements Say {
  use SayWorld;

  public function say($name) {
    echo 'Hello ', $name, PHP_EOL;
  }
}

class SayGreet extends SayHello {
  /**
   * @param array $name
   */
  public function say(array $name) {
    echo 'Greetings ', implode(', ', $name), PHP_EOL;
  }
}

trait TraitProperty {
  public $p = 't';
}

class PropertyA {
  public $p = 'a';
}

class PropertyB extends PropertyA {
  use TraitProperty;
  public $p = 'b';
}

trait TraitA {
  public $p;
}

trait TraitB {
  use TraitA;
}

class ClassTraitA {
  public $p;
}

class ClassTraitB extends ClassTraitA {
  use TraitB;
}

class DefaultValue {
  public function say($msg = 'hello') {}
}

abstract class TestX implements Say {}

class TestY extends TestX {}
