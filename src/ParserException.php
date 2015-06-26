<?php
namespace Pharborist;

/**
 * An exception occurred in parsing.
 */
class ParserException extends \Exception {
  /**
   * @param string $filename
   * @param int $lineNo
   * @param int $colNo
   * @param string $message
   */
  public function __construct($filename, $lineNo, $colNo, $message) {
    $details = 'Error at line ' . $lineNo;
    $details .= ':' . $colNo;
    if ($filename) {
      $details .= ' in file ' . $filename;
    }
    $message = "$details: $message";
    parent::__construct($message);
  }
}
