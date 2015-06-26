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
   * @param TokenNode[] $tokens
   */
  public function __construct(array $tokens) {
    $this->tokens = $tokens;
    $this->length = count($tokens);
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
    return $this->position + 1 < $this->length;
  }

  /**
   * @return int
   */
  public function getLineNumber() {
    if ($this->length === 0) {
      return 1;
    }
    $token = $this->current();
    if ($token === NULL) {
      $token = $this->tokens[$this->length - 1];
      return $token->getLineNumber();
    }
    return $token->getLineNumber();
  }

  /**
   * @return int
   */
  public function getColumnNumber() {
    if ($this->length === 0) {
      return 1;
    }
    $token = $this->current();
    if ($token === NULL) {
      $token = $this->tokens[$this->length - 1];
      return $token->getColumnNumber() + $token->getByteLength();
    }
    return $token->getColumnNumber();
  }
}
