<?php
namespace Example;

class Base implements StringObject {
  use ObjectUtil;

  public function toString() {
    return 'Base';
  }
}
