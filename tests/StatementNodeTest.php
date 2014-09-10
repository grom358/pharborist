<?php

namespace Pharborist;

class StatementNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetLineCount() {
    $text = <<<END
class Foobar {

  protected \$name;

  protected \$age;

}
END;
    $this->assertEquals(5, Parser::parseSnippet($text)->getLineCount());
  }
}
