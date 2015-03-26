<?php
namespace Pharborist\Functions;

use Pharborist\DocCommentTrait;
use Pharborist\ParenTrait;
use Pharborist\Token;
use Pharborist\TokenNode;

trait FunctionTrait {
  use ParameterTrait;
  use DocCommentTrait;
  use ParenTrait;

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

  /**
   * Get the return type of the function as defined by the doc comment.
   *
   * @return string[]
   *   The types as defined by phpdoc standard. Default is ['void'].
   */
  public function getReturnTypes() {
    $types = ['void'];
    $doc_comment = $this->getDocComment();
    if (!$doc_comment) {
      return $types;
    }
    $return_tag = $doc_comment->getReturn();
    if (!$return_tag) {
      return $types;
    }
    return $return_tag->getTypes();
  }
}
