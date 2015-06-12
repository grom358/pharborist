<?php
namespace Example;

class ClassA {
  use A {
    smallTalk as small;
    bigTalk as big;
  }
}
