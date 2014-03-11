<?php
namespace Pharborist;

/**
 * Iterator over tokens that supports marking and rewinding position.
 * @package Pharborist
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
   * Marked position in array.
   * @var int
   */
  private $mark;

  /**
   * @param Token[] $tokens
   */
  public function __construct(array $tokens) {
    $this->tokens = $tokens;
    $this->length = count($tokens);
    $this->position = 0;
  }

  /**
   * Mark position.
   */
  public function mark() {
    $this->mark = $this->position;
  }

  /**
   * Rewind to mark.
   */
  public function rewind() {
    $this->position = $this->mark;
  }

  /**
   * Return the current token.
   * @return Token
   */
  public function current() {
    if ($this->position >= $this->length) {
      return NULL;
    }
    return $this->tokens[$this->position];
  }

  /**
   * Move to the next token and return it.
   * @return Token
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
      return new SourcePosition(1, 1);
    }
    $token = $this->current();
    if ($token === NULL) {
      $token = $this->tokens[$this->length - 1];
    }
    return new SourcePosition($token->lineNo, $token->colNo);
  }

  /**
   * Return the current token type.
   * @return int|string
   */
  public function getTokenType() {
    $token = $this->current();
    return $token ?: $token->type;
  }

  /**
   * Return the current token text.
   * @return int|string
   */
  public function getTokenText() {
    $token = $this->current();
    return $token ?: $token->text;
  }
}
