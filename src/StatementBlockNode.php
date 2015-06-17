<?php
namespace Pharborist;

use Pharborist\Namespaces\UseDeclarationNode;

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
   * Get the use declarations of this statement block.
   *
   * @return NodeCollection|UseDeclarationNode[]
   *   Use declarations.
   */
  public function getUseDeclarations() {
    $declarations = [];
    /** @var \Pharborist\Namespaces\UseDeclarationBlockNode[] $use_blocks */
    $use_blocks = $this->children(Filter::isInstanceOf('\Pharborist\Namespaces\UseDeclarationBlockNode'));
    foreach ($use_blocks as $use_block) {
      foreach ($use_block->getDeclarationStatements() as $use_statement) {
        $declarations = array_merge($declarations, $use_statement->getDeclarations()->toArray());
      }
    }
    return new NodeCollection($declarations, FALSE);
  }

  /**
   * Return mapping of class names to fully qualified names.
   *
   * @return array
   *   Associative array of namespace alias to fully qualified names.
   */
  public function getClassAliases() {
    $mappings = array();
    foreach ($this->getUseDeclarations() as $use_declaration) {
      if ($use_declaration->isClass()) {
        $mappings[$use_declaration->getBoundedName()] = $use_declaration->getName()->getAbsolutePath();
      }
    }
    return $mappings;
  }

  /**
   * Append statement to block.
   *
   * @param StatementNode $statementNode
   *   Statement to append.
   * @return $this
   */
  public function appendStatement(StatementNode $statementNode) {
    if (!$this->isEmpty() && $this->firstToken()->getType() === '{') {
      $this->lastChild()->before($statementNode);
    }
    else {
      $this->appendChild($statementNode);
    }
    return $this;
  }
}
