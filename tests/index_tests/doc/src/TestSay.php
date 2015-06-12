<?php
namespace Example;

/**
 * Test class
 */
class TestSay extends Base implements Say {
  public function say() {
    return 'test';
  }
}
