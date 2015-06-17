<?php
namespace Pharborist\Functions;

use Pharborist\DocCommentTrait;
use Pharborist\ParenTrait;
use Pharborist\Token;
use Pharborist\TokenNode;
use Pharborist\Types;

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
        $this->reference = Token::reference();
        $this->name->before($this->reference);
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
   * Return TRUE if function has phpDoc return type.
   *
   * @return bool
   */
  public function hasReturnTypes() {
    $doc_comment = $this->getDocComment();
    if (!$doc_comment) {
      return FALSE;
    }
    $return_tag = $doc_comment->getReturn();
    if (!$return_tag) {
      return FALSE;
    }
    $types = $return_tag->getTypes();
    return !empty($types);
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
    $types = Types::normalize($return_tag->getTypes());
    if (empty($types)) {
      $types[] = 'void';
    }
    return $types;
  }

  public function matchReflector(\ReflectionFunctionAbstract $reflector) {
    $this->setReference($reflector->returnsReference());

    foreach ($reflector->getParameters() as $i => $parameter) {
      try {
        $this->getParameterAtIndex($i)->matchReflector($parameter);
      }
      catch (\OutOfBoundsException $e) {
        $this->appendParameter(ParameterNode::fromReflector($parameter));
      }
    }

    return $this;
  }
}
