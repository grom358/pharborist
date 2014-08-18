<?php
namespace Pharborist;

/**
 * A class method.
 */
class ClassMethodNode extends ClassStatementNode {
  use FunctionTrait;

  /**
   * @var TokenNode
   */
  protected $abstract;

  /**
   * @var TokenNode
   */
  protected $final;

  /**
   * @var TokenNode
   */
  protected $static;

  /**
   * @var TokenNode
   */
  protected $visibility;

  /**
   * @param string $method_name
   * @return ClassMethodNode
   */
  public static function create($method_name) {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet("class Property {public function {$method_name}() {}}")->firstChild();
    $method_node = $class_node->getBody()->firstChild()->remove();
    return $method_node;
  }

  /**
   * Create method from function declaration.
   *
   * @param FunctionDeclarationNode $function_node
   * @return ClassMethodNode
   */
  public static function fromFunction(FunctionDeclarationNode $function_node) {
    $method = static::create($function_node->getName()->getText());
    $function_node = clone $function_node;
    $method->getParameterList()->replaceWith(clone $function_node->getParameterList());
    $body = clone $function_node->getBody();
    $body->addIndent(Settings::get('formatter.indent'));
    $method->getBody()->replaceWith($body);
    return $method;
  }

  /**
   * @return TokenNode
   */
  public function getAbstract() {
    return $this->abstract;
  }

  /**
   * @return TokenNode
   */
  public function getFinal() {
    return $this->final;
  }

  /**
   * @return TokenNode
   */
  public function getStatic() {
    return $this->static;
  }

  /**
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->visibility;
  }

  protected function childInserted(Node $node) {
    static $visibilityTypes = [T_PUBLIC, T_PROTECTED, T_PRIVATE];
    if ($node instanceof TokenNode) {
      if ($node->getType() === '&') {
        $this->reference = $node;
      }
      elseif (in_array($node->getType(), $visibilityTypes)) {
        $this->visibility = $node;
      }
      elseif ($node->getType() === T_STATIC) {
        $this->static = $node;
      }
      elseif ($node->getType() === T_ABSTRACT) {
        $this->abstract = $node;
      }
      elseif ($node->getType() === T_FINAL) {
        $this->final = $node;
      }
    }
  }
}
