<?php
namespace Pharborist\Objects;

use Pharborist\CommaListNode;
use Pharborist\Namespaces\NameNode;
use Pharborist\NodeCollection;

/**
 * A trait precedence declaration.
 *
 * For example, A::bigTalk insteadof B;
 */
class TraitPrecedenceNode extends TraitAdaptationStatementNode {
  /**
   * @var TraitMethodReferenceNode
   */
  protected $traitMethodReference;

  /**
   * @var CommaListNode
   */
  protected $traitNames;

  /**
   * @return TraitMethodReferenceNode
   */
  public function getTraitMethodReference() {
    return $this->traitMethodReference;
  }

  /**
   * @return NodeCollection|NameNode[]
   */
  public function getTraitNames() {
    return $this->traitNames->getItems();
  }
}
