<?php
namespace Pharborist;

trait ParenTrait {
  /**
   * @var TokenNode
   */
  protected $openParen;

  /**
   * @var TokenNode
   */
  protected $closeParen;

  /**
   * @return TokenNode
   */
  public function getOpenParen() {
    return $this->openParen;
  }

  /**
   * @return TokenNode
   */
  public function getCloseParen() {
    return $this->closeParen;
  }
}
