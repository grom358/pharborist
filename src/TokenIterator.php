<?php
namespace Pharborist;

/**
 * Iterator over tokens that supports peek.
 */
class TokenIterator {
  /**
   * The token array.
   * @var array
   */
  private $tokens;

  /**
   * Current position in array.
   * @var int
   */
  private $position;

  /**
   * The length of token array.
   * @var int
   */
  private $length;

  /**
   * The filename that tokens belong to.
   * @var string
   */
  private $filename;

  /**
   * @param TokenNode[] $tokens
   */
  public function __construct(array $tokens, $filename = NULL) {
    $this->tokens = $tokens;
    $this->length = count($tokens);
    $this->filename = $filename;
    $this->position = 0;
  }

  /**
   * Return the current token.
   * @return TokenNode
   */
  public function current() {
    if ($this->position >= $this->length) {
      return NULL;
    }
    return $this->tokens[$this->position];
  }

  /**
   * Peek ahead.
   * @param int $offset Offset from current position.
   * @return TokenNode
   */
  public function peek($offset) {
    if ($this->position + $offset >= $this->length) {
      return NULL;
    }
    return $this->tokens[$this->position + $offset];
  }

  /**
   * Move to the next token and return it.
   * @return TokenNode
   */
  public function next() {
    $this->position++;
    if ($this->position >= $this->length) {
      $this->position = $this->length;
      return NULL;
    }
    return $this->tokens[$this->position];
  }

  /**
   * Return TRUE if there are more tokens.
   * @return bool
   */
  public function hasNext() {
    return $this->position < $this->length;
  }

  /**
   * Return the source position.
   * @return SourcePosition
   */
  public function getSourcePosition() {
    if ($this->length === 0) {
      return new SourcePosition($this->filename, 1, 1, 0);
    }
    $token = $this->current();
    if ($token === NULL) {
      $token = $this->tokens[$this->length - 1];
      $source_position = $token->getSourcePosition();
      $filename = $source_position->getFilename();
      $line_no = $source_position->getLineNumber();
      $col_no = $source_position->getColumnNumber();
      $byte_offset = $source_position->getByteOffset();
      $length = strlen($token->getText());
      return new SourcePosition($filename, $line_no, $col_no + $length, $byte_offset + $length);
    }
    return $token->getSourcePosition();
  }
}
