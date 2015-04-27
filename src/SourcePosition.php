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
   * The byte offset.
   *
   * Useful for file seeking to position (eg. fseek).
   * @var int
   */
  protected $byteOffset;

  /**
   * Constructor.
   *
   * @param string $filename
   * @param integer $line_no
   * @param integer $col_no
   * @param integer $byte_offset
   */
  public function __construct($filename, $line_no, $col_no, $byte_offset) {
    $this->filename = $filename;
    $this->lineNo = $line_no;
    $this->colNo = $col_no;
    $this->byteOffset = $byte_offset;
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

  /**
   * Get the byte offset.
   * @return int
   */
  public function getByteOffset() {
    return $this->byteOffset;
  }
}
