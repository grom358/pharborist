<?php
namespace Pharborist;

/**
 * A block of statements.
 */
class StatementBlockNode extends ParentNode {
  protected function _getStatements() {
    $matches = [];
    $child = $this->head;
    while ($child) {
      if ($child instanceof StatementNode) {
        $matches[] = $child;
      }
      elseif ($child instanceof StatementBlockNode) {
        $matches = array_merge($matches, $child->_getStatements());
      }
      $child = $child->next;
    }
    return $matches;
  }

  /**
   * @return NodeCollection|StatementNode[]
   */
  public function getStatements() {
    return new NodeCollection($this->_getStatements(), FALSE);
  }

  /**
   * Return mapping of class names to fully qualified names.
   *
   * @return array
   *   Associative array of namespace alias to fully qualified names.
   */
  public function getClassAliases() {
    $mappings = array();
    /** @var \Pharborist\Namespaces\UseDeclarationBlockNode[] $use_blocks */
    $use_blocks = $this->children(Filter::isInstanceOf('\Pharborist\Namespaces\UseDeclarationBlockNode'));
    foreach ($use_blocks as $use_block) {
      foreach ($use_block->getDeclarationStatements() as $use_statement) {
        if ($use_statement->importsClass()) {
          foreach ($use_statement->getDeclarations() as $use_declaration) {
            $mappings[$use_declaration->getBoundedName()] = $use_declaration->getName()->getAbsolutePath();
          }
        }
      }
    }
    return $mappings;
  }
}
