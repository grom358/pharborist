<?php
namespace Pharborist\Functions;

use Pharborist\Objects\ClassMethodNode;
use Pharborist\Objects\ClassNode;
use Pharborist\Namespaces\NameNode;
use Pharborist\Node;
use Pharborist\Parser;
use Pharborist\StatementBlockNode;
use Pharborist\StatementNode;
use Pharborist\TokenNode;

/**
 * A function declaration.
 */
class FunctionDeclarationNode extends StatementNode {
  use FunctionTrait;

  /**
   * Create a function declaration.
   *
   * @param NameNode|string $function_name
   *   The function name.
   * @param array $parameters
   *   (Optional) List of parameters.
   *
   * @return FunctionDeclarationNode
   */
  public static function create($function_name, $parameters = NULL) {
    /** @var FunctionDeclarationNode $function */
    $function = Parser::parseSnippet("function $function_name() {}");
    if (is_array($parameters)) {
      foreach ($parameters as $parameter) {
        if (is_string($parameter)) {
          $parameter = ParameterNode::create($parameter);
        }
        $function->appendParameter($parameter);
      }
    }
    return $function;
  }

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * Set the name of the declared function.
   *
   * @param string $name
   *   New function name.
   * @return $this
   */
  public function setName($name) {
    /** @var TokenNode $function_name */
    $function_name = $this->getName()->firstChild();
    $function_name->setText($name);
    return $this;
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * Creates a class method from this function and add it to the given
   * class definition.
   *
   * @param \Pharborist\Objects\ClassNode $class
   *  The class to add the new method to.
   *
   * @return \Pharborist\Objects\ClassMethodNode
   *  The newly created method.
   */
  public function cloneAsMethodOf(ClassNode $class) {
    $clone = ClassMethodNode::fromFunction($this);
    $class->appendMethod($clone);
    return $clone;
  }

  protected function childInserted(Node $node) {
    if ($node instanceof TokenNode && $node->getType() === '&') {
      $this->reference = $node;
    }
  }
}
