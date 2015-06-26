<?php
namespace Pharborist\Objects;

use Pharborist\Parser;
use Pharborist\StatementNode;

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
}
