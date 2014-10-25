<?php
namespace Pharborist\Functions;

use Pharborist\DocCommentTrait;
use Pharborist\Namespaces\NameNode;
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
   * @var \Pharborist\Namespaces\NameNode
   */
  protected $name;

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

  /**
   * @return NameNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string|NameNode $name
   * @return $this
   */
  public function setName($name) {
    if (is_string($name)) {
      $name = NameNode::create($name);
    }
    $this->name->replaceWith($name);
    $this->name = $name;
    return $this;
  }
}
