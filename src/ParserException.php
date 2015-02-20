<?php
namespace Pharborist;

/**
 * An exception occurred in parsing.
 */
class ParserException extends \Exception {
  /**
   * @param SourcePosition $position
   * @param string $message
   */
  public function __construct($position, $message) {
    $details = 'Error at line ' . $position->getLineNumber();
    $details .= ':' . $position->getColumnNumber();
    if ($position->getFilename()) {
      $details .= ' in file ' . $position->getFilename();
    }
    $message = "$details: $message";
    parent::__construct($message);
  }
}
