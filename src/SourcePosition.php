<?php
namespace Pharborist;

/**
 * Position in source.
 * @package Pharborist
 */
class SourcePosition {
  /**
   * The line number.
   * @var int
   */
  public $lineNo;

  /**
   * The column number.
   * @var int
   */
  public $colNo;

  /**
   * Constructor.
   */
  public function __construct($line_no, $col_no) {
    $this->lineNo = $line_no;
    $this->colNo = $col_no;
  }
}
