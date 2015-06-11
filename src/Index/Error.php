<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

class Error {

  static private $counter = 1;

  /**
   * @var int
   */
  protected $errorNo;

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
    $this->errorNo = self::$counter++;
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

  /**
   * @return int
   */
  public function getErrorNo() {
    return $this->errorNo;
  }

}
