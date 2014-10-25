<?php
namespace Pharborist;

/**
 * Position in source.
 */
class SourcePosition {
  /**
   * The line number.
   * @var int
   */
  protected $lineNo;

  /**
   * The column number.
   * @var int
   */
  protected $colNo;

  /**
   * Constructor.
   *
   * @param integer $line_no
   * @param integer $col_no
   */
  public function __construct($line_no, $col_no) {
    $this->lineNo = $line_no;
    $this->colNo = $col_no;
  }

  /**
   * Get the line number.
   * @return int
   */
  public function getLineNumber() {
    return $this->lineNo;
  }

  /**
   * Get the column number.
   * @return int
   */
  public function getColumnNumber() {
    return $this->colNo;
  }
}
