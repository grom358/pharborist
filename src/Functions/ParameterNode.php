<?php
namespace Pharborist\Functions;

use Pharborist\ExpressionNode;
use Pharborist\Filter;
use Pharborist\Namespaces\NameNode;
use Pharborist\Node;
use Pharborist\ParentNode;
use Pharborist\Token;
use Pharborist\TokenNode;
use Pharborist\Variables\VariableNode;

/**
 * A function parameter.
 */
class ParameterNode extends ParentNode {
  /**
   * @var Node
   */
  protected $typeHint;

  /**
   * @var TokenNode
   */
  protected $reference;

  /**
   * @var TokenNode
   */
  protected $variadic;

  /**
   * @var VariableNode
   */
  protected $name;

  /**
   * @var ExpressionNode
   */
  protected $value;

  /**
   * Create a parameter node.
   *
   * @param string $parameter_name
   *   Parameter name, eg. $parm
   * @return ParameterNode
   */
  public static function create($parameter_name) {
    $parameter_name = '$' . ltrim($parameter_name, '$');
    $parameter_node = new ParameterNode();
    $parameter_node->append(new VariableNode(T_VARIABLE, $parameter_name));
    return $parameter_node;
  }

  /**
   * {@inheritdoc}
   */
  protected function childInserted(Node $node) {
    if ($node instanceof TokenNode) {
      if ($node->getType() === T_ARRAY || $node->getType() === T_CALLABLE) {
        $this->typeHint = $node;
      }
      elseif ($node->getType() === '&') {
        $this->reference = $node;
      }
      elseif ($node->getType() === T_ELLIPSIS) {
        $this->variadic = $node;
      }
      elseif ($node instanceof VariableNode) {
        $this->name = $node;
      }
      elseif ($node instanceof ExpressionNode) {
        $this->value = $node;
      }
    }
    elseif ($node instanceof NameNode) {
      $this->typeHint = $node;
    }
    elseif ($node instanceof ExpressionNode) {
      $this->value = $node;
    }
  }

  /**
   * Returns the function/method which defines this parameter.
   *
   * @return FunctionDeclarationNode|\Pharborist\Objects\ClassMethodNode|\Pharborist\Objects\InterfaceMethodNode|AnonymousFunctionNode|NULL
   */
  public function getFunction() {
    return $this->closest(Filter::isInstanceOf(
      'Pharborist\Functions\FunctionDeclarationNode',
      'Pharborist\Objects\ClassMethodNode',
      'Pharborist\Objects\InterfaceMethodNode',
      'Pharborist\Functions\AnonymousFunctionNode'
    ));
  }

  /**
   * @return Node
   */
  public function getTypeHint() {
    return $this->typeHint;
  }

  /**
   * @param string|Node $type_hint
   * @return $this
   */
  public function setTypeHint($type_hint) {
    if (is_string($type_hint)) {
      $type = $type_hint;
      $type_hint = new NameNode();
      $type_hint->append(Token::identifier($type));
    }
    if (isset($this->typeHint)) {
      $this->typeHint->replaceWith($type_hint);
    }
    else {
      $this->prepend([
        $type_hint,
        Token::space(),
      ]);
    }
    return $this;
  }

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
   * @return TokenNode
   */
  public function getVariadic() {
    return $this->variadic;
  }

  /**
   * @param boolean $is_variadic
   * @return $this
   */
  public function setVariadic($is_variadic) {
    if ($is_variadic) {
      if (!isset($this->variadic)) {
        $this->name->before(Token::splat());
      }
    }
    else {
      if (isset($this->variadic)) {
        $this->variadic->remove();
      }
    }
  }

  /**
   * @return bool
   */
  public function isOptional() {
    return isset($this->value);
  }

  /**
   * @return bool
   */
  public function isRequired() {
    return !isset($this->value);
  }

  /**
   * @return VariableNode
   */
  public function getVariable() {
    return $this->name;
  }

  /**
   * @return string
   *  The parameter name, without the leading $.
   */
  public function getName() {
    return ltrim($this->getVariable()->getText(), '$');
  }

  /**
   * @param string $name
   *  The name of the parameter, with or without the leading $.
   * @param boolean $rewrite
   *  If TRUE, every reference to the parameter in the function body will be changed
   *  to reflect the new name.
   *
   * @return $this
   */
  public function setName($name, $rewrite = FALSE) {
    $original_name = $this->name->getText();

    $this->name->setName($name);

    if ($rewrite) {
      $this
        ->getFunction()
        ->find(Filter::isInstanceOf('\Pharborist\Variables\VariableNode'))
        ->filter(function(VariableNode $node) use ($original_name) {
          return $node->getText() === $original_name;
        })
        ->each(function(VariableNode $node) use ($name) {
          $node->setText('$' . $name);
        });
    }

    return $this;
  }

  /**
   * @return ExpressionNode
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @param ExpressionNode|NULL $node
   * @return $this
   */
  public function setValue($node) {
    if ($node === NULL) {
      if (isset($this->value)) {
        $this->value->previousUntil(Filter::isInstanceOf('\Pharborist\Variables\VariableNode'))->remove();
        $this->value->remove();
      }
    }
    else {
      if (isset($this->value)) {
        /** @var Node $node */
        $this->value->replaceWith($node);
      }
      else {
        $this->append([
          Token::space(),
          Token::assign(),
          Token::space(),
          $node,
        ]);
      }
    }
    return $this;
  }
}
