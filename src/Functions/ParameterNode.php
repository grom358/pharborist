<?php
namespace Pharborist\Functions;

use Pharborist\Constants\ConstantNode;
use Pharborist\ExpressionNode;
use Pharborist\Filter;
use Pharborist\Namespaces\NameNode;
use Pharborist\Node;
use Pharborist\ParentNode;
use Pharborist\Token;
use Pharborist\TokenNode;
use Pharborist\Types;
use Pharborist\Variables\VariableNode;

/**
 * A function parameter.
 */
class ParameterNode extends ParentNode {
  /**
   * @var NameNode|TokenNode
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
    $parameter_node->addChild(new VariableNode(T_VARIABLE, $parameter_name), 'name');
    return $parameter_node;
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
   * @return NameNode|TokenNode
   */
  public function getTypeHint() {
    return $this->typeHint;
  }

  /**
   * @param string|NameNode|TokenNode $type_hint
   * @return $this
   */
  public function setTypeHint($type_hint) {
    if (is_string($type_hint)) {
      $type = $type_hint;
      switch ($type) {
        case 'array':
          $type_hint = Token::_array();
          break;
        case 'callable':
          $type_hint = Token::_callable();
          break;
        default:
          $type_hint = NameNode::create($type);
          break;
      }
    }
    if ($type_hint) {
      if (isset($this->typeHint)) {
        $this->typeHint->replaceWith($type_hint);
      }
      else {
        $this->typeHint = $type_hint;
        $this->prepend([
          $this->typeHint,
          Token::space(),
        ]);
      }
    }
    else {
      $this->typeHint->remove();
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
   * @return bool
   */
  public function isReference() {
    return $this->reference !== NULL;
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
        $this->variadic = Token::splat();
        $this->name->before($this->variadic);
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
   *   TRUE if parameter is variadic.
   */
  public function isVariadic() {
    return isset($this->variadic);
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

  /**
   * Get the doc block tag associated with this parameter.
   *
   * @return null|\phpDocumentor\Reflection\DocBlock\Tag\ParamTag
   *   The parameter tag or null if not found.
   */
  public function getDocBlockTag() {
    $doc_comment = $this->getFunction()->getDocComment();
    return $doc_comment ? $doc_comment->getParameter($this->name->getText()) : NULL;
  }

  /**
   * Return TRUE if parameter has a phpDoc type.
   *
   * @return bool
   */
  public function hasDocTypes() {
    $doc_comment = $this->getFunction()->getDocComment();
    if (!$doc_comment) {
      return FALSE;
    }
    $param_tag = $doc_comment->getParameter($this->getName());
    if (!$param_tag) {
      return FALSE;
    }
    $types = $param_tag->getTypes();
    return !empty($types);
  }

  /**
   * Get the php doc types for parameter.
   *
   * @return string[]
   */
  public function getDocTypes() {
    // No type specified means type is mixed.
    $types = ['mixed'];
    // Use types from the doc comment if available.
    $doc_comment = $this->getFunction()->getDocComment();
    if (!$doc_comment) {
      return $types;
    }
    $param_tag = $doc_comment->getParameter($this->getName());
    if (!$param_tag) {
      return $types;
    }
    $types = Types::normalize($param_tag->getTypes());
    if (empty($types)) {
      $types[] = 'mixed';
    }
    return $types;
  }

  /**
   * Get the type of the parameter as defined by type hinting or doc comment.
   *
   * @return string[]
   *   The types as defined by phpdoc standard. Default is ['mixed'].
   */
  public function getTypes() {
    // If type hint is set then that is the type of the parameter.
    if ($this->typeHint) {
      if ($this->typeHint instanceof TokenNode) {
        if ($this->typeHint->getType() === T_ARRAY) {
          $docTypes = $this->getDocTypes();
          foreach ($docTypes as $docType) {
            if ($docType !== 'array' && substr($docType, -2) !== '[]') {
              return [$this->typeHint->getText()];
            }
          }
          return $docTypes;
        }
        else {
          return [$this->typeHint->getText()];
        }
      }
      else {
        return [$this->typeHint->getAbsolutePath()];
      }
    }
    return $this->getDocTypes();
  }

  public function matchReflector(\ReflectionParameter $reflector) {
    $this->setReference($reflector->isPassedByReference());

    // Match the reflector's type hint.
    if ($reflector->isArray()) {
      $this->setTypeHint('array');
    }
    elseif ($reflector->isCallable()) {
      $this->setTypeHint('callable');
    }
    elseif ($class = $reflector->getClass()) {
      $this->setTypeHint($class->getName());
    }

    // Match the reflector's default value, if there is one. It will be a
    // scalar value, an array of scalar values, or a constant.
    if ($reflector->isDefaultValueAvailable()) {
      if ($reflector->isDefaultValueConstant()) {
        $this->setValue(ConstantNode::create($reflector->getDefaultValueConstantName()));
      }
      else {
        $this->setValue(Node::fromValue($reflector->getDefaultValue()));
      }
    }
    return $this;
  }

  public static function fromReflector(\ReflectionParameter $reflector) {
    $node = static::create($reflector->getName());
    $node->matchReflector($reflector);
    return $node;
  }
}
