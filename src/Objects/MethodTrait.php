<?php
namespace Pharborist\Objects;

use Pharborist\Filter;
use Pharborist\Functions\FunctionTrait;
use Pharborist\Token;
use Pharborist\TokenNode;

/**
 * Trait used by any class method, including abstract methods.
 *
 * @see ClassMethodNode
 */
trait MethodTrait {
  use VisibilityTrait;
  use FunctionTrait;

  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var TokenNode
   */
  protected $static;

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string|TokenNode $name
   *
   * @return $this
   */
  public function setName($name) {
    if (is_string($name)) {
      $name = Token::identifier($name);
    }
    $this->name->replaceWith($name);
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getStatic() {
    return $this->static;
  }

  /**
   * @param boolean $is_static
   * @return $this
   */
  public function setStatic($is_static) {
    if ($is_static) {
      if (!isset($this->static)) {
        // Insert before T_FUNCTION.
        $function_token = $this->name->previous()->previous();
        $function_token->before([
          Token::_static(),
          Token::space(),
        ]);
      }
    }
    else {
      if (isset($this->static)) {
        // Remove whitespace after static keyword.
        $this->static->next()->remove();
        // Remove static keyword.
        $this->static->remove();
      }
    }
    return $this;
  }

  /**
   * @return ClassNode|TraitNode
   */
  public function getClass() {
    return $this->closest(Filter::isInstanceOf('\Pharborist\Objects\ClassNode', '\Pharborist\Objects\TraitNode'));
  }

  /**
   * @see \Pharborist\NameResolutionInterface::getFullyQualifiedName()
   */
  public function getFullyQualifiedName() {
    return $this->getClass()->getFullyQualifiedName() . '::' . $this->name->getText();
  }

  /**
   * @see \Pharborist\NameResolutionInterface::getQualifiedName()
   */
  public function getQualifiedName() {
    return $this->getClass()->getQualifiedName() . '::' . $this->name->getText();
  }

  /**
   * @see \Pharborist\NameResolutionInterface::getUnqualifiedName()
   */
  public function getUnqualifiedName() {
    return $this->getClass()->getUnqualifiedName() . '::' . $this->name->getText();
  }
}
