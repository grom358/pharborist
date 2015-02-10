<?php
namespace Pharborist;

/**
 * Position in source.
 */
class SourcePosition {
  /**
   * The file name.
   * @var string
   */
  protected $filename;

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
   * @param string $filename
   * @param integer $line_no
   * @param integer $col_no
   */
  public function __construct($filename, $line_no, $col_no) {
    $this->filename = $filename;
    $this->lineNo = $line_no;
    $this->colNo = $col_no;
  }

  /**
   * Get the file name.
   * @return string
   */
  public function getFilename() {
    return $this->filename;
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
