<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

class Error {

  /**
   * @var FilePosition|SourcePosition
   */
  protected $position;

  /**
   * @var string
   */
  protected $message;

  /**
   * @param FilePosition|SourcePosition $position
   * @param string $message
   */
  public function __construct($position, $message) {
    $this->position = $position;
    $this->message = $message;
  }

  /**
   * @return FilePosition|SourcePosition
   */
  public function getPosition() {
    return $this->position;
  }

  /**
   * @return string
   */
  public function getMessage() {
    return $this->message;
  }

}
