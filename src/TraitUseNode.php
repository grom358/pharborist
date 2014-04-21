<?php
namespace Pharborist;

/**
 * A trait use declaration.
 */
class TraitUseNode extends ClassStatementNode {
  /**
   * @var CommaListNode
   */
  protected $traits;

  /**
   * @var StatementBlockNode
   */
  protected $adaptations;

  /**
   * @return NameNode[]
   */
  public function getTraits() {
    return $this->traits->getItems();
  }

  /**
   * @return TraitAdaptationStatementNode[]
   */
  public function getAdaptations() {
    return $this->adaptations->getStatements();
  }
}
