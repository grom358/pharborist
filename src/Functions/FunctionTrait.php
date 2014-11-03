<?php
namespace Pharborist\Functions;

use Pharborist\DocCommentTrait;
use Pharborist\Namespaces\IdentifierNameTrait;
use Pharborist\Token;
use Pharborist\TokenNode;

trait FunctionTrait {
  use IdentifierNameTrait;
  use ParameterTrait;
  use DocCommentTrait;

  /**
   * @var TokenNode
   */
  protected $reference;

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * @param boolean $is_reference
   * @return $this
   */
  public function setReference($is_reference) {
    if ($is_reference) {
      if (!isset($this->reference)) {
        $this->name->before(Token::reference());
      }
    }
    else {
      if (isset($this->reference)) {
        $this->reference->remove();
      }
    }
    return $this;
  }
}
