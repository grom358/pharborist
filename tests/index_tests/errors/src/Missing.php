<?php
namespace Example;

class Missing extends MissingClass implements MissingInterface {
  use MissingTrait;
}

trait T {
  use MissingTrait;
}

interface I extends MissingInterface {

}

interface Speaker {
  public function speak();
}

class Communicator implements Speaker {}

abstract class AbstractClass {
  abstract public function test();
}

class MissingAbstract extends AbstractClass {

}
