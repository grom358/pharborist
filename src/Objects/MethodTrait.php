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

  public function getClassNode() {
    /** @var ClassMethodNode $this */
    return $this->closest(Filter::isInstanceOf('\Pharborist\Objects\ClassNode'));
  }

  public function getFullyQualifiedName() {
    return $this->getClassNode()->getFullyQualifiedName() . '::' . $this->name->getText();
  }

  public function getQualifiedName() {
    return $this->getClassNode()->getQualifiedName() . '::' . $this->name->getText();
  }

  public function getUnqualifiedName() {
    return $this->getClassNode()->getUnqualifiedName() . '::' . $this->name->getText();
  }

  /**
   * @return string
   */
  public function getQualifiedRelativeName() {
    $full_name = $this->getFullyQualifiedName();
    $ns_name = $this->getNamespace()->getFullyQualifiedName();
    return substr($full_name, strlen($ns_name));
  }
}
