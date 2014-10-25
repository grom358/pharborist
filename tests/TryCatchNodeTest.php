<?php

namespace Pharborist;

class TryCatchNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCatches() {
    /** @var \Pharborist\Exceptions\TryCatchNode $tryCatch */
    $tryCatch = Parser::parseSnippet('try { foo(); } catch (\InvalidArgumentException $e) {}');
    $this->assertTrue($tryCatch->catches('\InvalidArgumentException'));
    $this->assertFalse($tryCatch->catches('\DomainException'));
  }
}
