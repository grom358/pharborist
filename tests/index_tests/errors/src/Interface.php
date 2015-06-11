<?php
namespace Example;

interface InterfaceA {
  const MSG = 'A';
}

interface InterfaceB extends InterfaceA {
  const MSG = 'B';
}

class ConflictClassWithInterfaceConstant implements InterfaceA {
  const MSG = 'Test';
}

interface InterfaceC {
  const MSG = 'C';
}

class ConflictInterfaceConstants implements InterfaceA, InterfaceC {}

interface InterfaceD extends InterfaceA {}

class ConflictInheritInterfaceConstants implements InterfaceD, InterfaceC {}

interface InterfaceX {
  public function say();
}

interface InterfaceY {
  public function say($msg);
}

interface ConflictMethodInterface extends InterfaceX, InterfaceY {}

class ConflictInterfaceMethods implements InterfaceX, InterfaceY {
  public function say($msg) {
    echo 'test', PHP_EOL;
  }
}
