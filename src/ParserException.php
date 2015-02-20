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
    parent::__construct("Error at {$position->getLineNumber()}:{$position->getColumnNumber()} in {$position->getFilename()}: $message");
  }
}
