<?php
namespace Example;

class PrivateHelloWorld {
  use HelloWorld {
    sayHello as private;
  }
}
