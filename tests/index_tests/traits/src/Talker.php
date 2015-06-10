<?php
namespace Example;

class Talker {
  use A, B {
    B::smallTalk insteadof A;
    A::bigTalk insteadof B;
    B::bigTalk as private talk;
  }
}
