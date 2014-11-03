<?php
namespace Pharborist\Functions;

use Pharborist\DocCommentTrait;
use Pharborist\Token;
use Pharborist\TokenNode;

trait FunctionTrait {
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
        /** @var \Pharborist\Functions\FunctionDeclarationNode|\Pharborist\Objects\ClassMethodNode|\Pharborist\Objects\InterfaceMethodNode $this */
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
