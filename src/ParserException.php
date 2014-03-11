<?php
namespace Pharborist;

/**
 * An exception occurred in parsing.
 * @package Pharborist
 */
class ParserException extends \Exception {
  /**
   * @param SourcePosition $position
   * @param string $message
   */
  public function __construct($position, $message) {
    parent::__construct("Error at {$position->lineNo}:{$position->colNo}: $message");
  }
}
