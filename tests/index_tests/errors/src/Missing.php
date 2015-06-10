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
