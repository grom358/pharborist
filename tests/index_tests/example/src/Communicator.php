<?php
namespace Example;

class Communicator extends Person implements PublicSpeaker {
  use Ni;

  public function speak() {
    return 'Hello my name is ' . $this->name;
  }

  public function getSubject() {
    return 'Monty Python and Holy Grail';
  }

  public function testTypeHint(callable $hint) {

  }
}
