<?php
namespace Pharborist;

/**
 * A trait use declaration.
 */
class TraitUseNode extends StatementNode {
  protected $properties = array(
    'traits' => NULL,
    'adaptations' => NULL,
  );

  /**
   * @return NamespacePathNode[]
   */
  public function getTraits() {
    /** @var CommaListNode $traits */
    $traits = $this->properties['traits'];
    return $traits->getItems();
  }

  /**
   * @return (TraitPrecedenceNode|TraitAliasNode)[]
   */
  public function getAdaptations() {
    /** @var StatementBlockNode $statement_block */
    $statement_block = $this->properties['adaptations'];
    return $statement_block->getStatements();
  }
}
