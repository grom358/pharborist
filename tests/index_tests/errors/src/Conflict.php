<?php
namespace Example;

trait A {
  public function sayA() {
    echo 'A', PHP_EOL;
  }
}

trait B {
  public function sayB() {
    echo 'B', PHP_EOL;
  }
}

trait C {
  public function say() {
    echo 'C', PHP_EOL;
  }
}

trait D {
  public function say() {
    echo 'D', PHP_EOL;
  }
}

trait Conflict {
  use A, B {
    A::sayA as conflictMethod;
    B::sayB as conflictMethod;
  }
  use C, D;
}

trait ConflictPrecedence {
  use C, D {
    C::say insteadof D;
    D::say insteadof C;
  }
}

trait MissingRequiredTrait {
  use D {
    C::say insteadof D;
  }
}

trait AnotherMissingRequiredTrait {
  use D {
    D::say insteadof A;
  }
}

trait MissingExplicit {
  use C, D;
}
