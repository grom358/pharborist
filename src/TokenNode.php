<?php
namespace Pharborist;

/**
 * A node wrapper around a token.
 * @package Pharborist
 */
class TokenNode extends Node {
  /**
   * @var Token
   */
  public $token;

  /**
   * @param Token $token
   */
  public function __construct(Token $token) {
    $this->token = $token;
  }

  /**
   * @return SourcePosition
   */
  public function getSourcePosition() {
    return new SourcePosition($this->token->lineNo, $this->token->colNo);
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->token->text;
  }
}
