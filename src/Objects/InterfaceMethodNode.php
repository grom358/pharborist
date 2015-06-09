<?php
namespace Pharborist\Objects;

use Pharborist\Node;
use Pharborist\Parser;
use Pharborist\StatementNode;
use Pharborist\TokenNode;

/**
 * An interface method.
 */
class InterfaceMethodNode extends StatementNode implements InterfaceStatementNode {
  use MethodTrait;

  /**
   * @param string $method_name
   * @return InterfaceMethodNode
   */
  public static function create($method_name) {
    /** @var InterfaceNode $interface_node */
    $interface_node = Parser::parseSnippet("interface Method {public function {$method_name}();}");
    $method_node = $interface_node->getStatements()[0]->remove();
    return $method_node;
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
    }
  }
}
