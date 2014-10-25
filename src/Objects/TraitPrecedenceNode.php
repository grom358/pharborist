<?php
namespace Pharborist\Objects;

use Pharborist\CommaListNode;
use Pharborist\Namespaces\NameNode;

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
   * @return \Pharborist\Namespaces\NameNode[]
   */
  public function getTraitNames() {
    return $this->traitNames->getItems();
  }
}
