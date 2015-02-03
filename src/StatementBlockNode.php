<?php
namespace Pharborist;

use Pharborist\Namespaces\UseDeclarationBlockNode;
use Pharborist\Namespaces\UseDeclarationNode;
use Pharborist\Namespaces\UseDeclarationStatementNode;

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
    $declarations = new NodeCollection();
    /** @var \Pharborist\Namespaces\UseDeclarationBlockNode[] $use_blocks */
    $use_blocks = $this->children(Filter::isInstanceOf('\Pharborist\Namespaces\UseDeclarationBlockNode'));
    foreach ($use_blocks as $use_block) {
      foreach ($use_block->getDeclarationStatements() as $use_statement) {
        $declarations->add($use_statement->getDeclarations());
      }
    }
    return $declarations;
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
   * Get the use declaration block.
   *
   * @return UseDeclarationBlockNode|NULL
   *   The use declaration block or NULL if does not exist.
   */
  public function getUseDeclarationBlock() {
    $use_blocks = $this->children(Filter::isInstanceOf('\Pharborist\Namespaces\UseDeclarationBlockNode'));
    if ($use_blocks->isEmpty()) {
      return NULL;
    }
    return $use_blocks->get(0);
  }

  /**
   * Add use declaration for class.
   *
   * @param string $class
   */
  public function useClass($class) {
    $use_block = $this->getUseDeclarationBlock();
    if (!$use_block) {
      // Create use declaration block.
      $use_block = new UseDeclarationBlockNode();
      if (!$this->isEmpty() && $this->firstToken()->getType() === '{') {
        // @todo indent statement
        $this->firstChild()->after([Token::newline(), $use_block]);
      }
      else {
        $this->prepend([Token::newline(), Token::newline(), $use_block]);
      }
    }
    else {
      // Append newline
      $use_block->appendChild(Token::newline());
    }
    $use_declaration_stmt = UseDeclarationStatementNode::create($class);
    $use_block->appendChild($use_declaration_stmt);
  }
}
