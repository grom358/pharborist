<?php
namespace Pharborist\Index;

use Pharborist\Node;
use Pharborist\SourcePosition;

/**
 * Position in source.
 *
 * Used to read element from file.
 */
class FilePosition {
  /**
   * The file name.
   *
   * @var string
   */
  protected $filename;

  /**
   * The line number.
   *
   * @var int
   */
  protected $lineNo;

  /**
   * The column number.
   *
   * @var int
   */
  protected $colNo;

  /**
   * The byte offset.
   *
   * Useful for file seeking to position (eg. fseek).
   *
   * @var int
   */
  protected $byteOffset;

  /**
   * Byte length.
   *
   * Length in bytes of element in source file.
   *
   * @var int
   */
  protected $byteLength;

  /**
   * Constructor.
   *
   * @param string $filename
   * @param int $line_no
   * @param int $col_no
   * @param int $byte_offset
   * @param int $byte_length
   */
  public function __construct($filename, $line_no, $col_no, $byte_offset, $byte_length) {
    $this->filename = $filename;
    $this->lineNo = $line_no;
    $this->colNo = $col_no;
    $this->byteOffset = $byte_offset;
    $this->byteLength = $byte_length;
  }

  /**
   * Create file position from source position.
   *
   * @param SourcePosition $position
   * @param int $byteLength
   *
   * @return FilePosition
   */
  public static function fromSourcePosition(SourcePosition $position, $byteLength) {
    return new FilePosition(
      $position->getFilename(),
      $position->getLineNumber(),
      $position->getColumnNumber(),
      $position->getByteOffset(),
      $byteLength
    );
  }

  /**
   * Create file position from node.
   *
   * @param Node $node
   *
   * @return FilePosition
   */
  public static function fromNode(Node $node) {
    $position = $node->getSourcePosition();
    return new FilePosition(
      $position->getFilename(),
      $position->getLineNumber(),
      $position->getColumnNumber(),
      $position->getByteOffset(),
      strlen($node->getText())
    );
  }

  /**
   * Get the file name.
   *
   * @return string
   */
  public function getFilename() {
    return $this->filename;
  }

  /**
   * Get the line number.
   *
   * @return int
   */
  public function getLineNumber() {
    return $this->lineNo;
  }

  /**
   * Get the column number.
   *
   * @return int
   */
  public function getColumnNumber() {
    return $this->colNo;
  }

  /**
   * Get the byte offset.
   *
   * @return int
   */
  public function getByteOffset() {
    return $this->byteOffset;
  }

  /**
   * Get the byte length.
   *
   * @return int
   */
  public function getByteLength() {
    return $this->byteLength;
  }

}
