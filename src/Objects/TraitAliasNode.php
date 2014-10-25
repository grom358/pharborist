<?php
namespace Pharborist\Objects;

use Pharborist\Namespaces\NameNode;
use Pharborist\TokenNode;

/**
 * A trait alias, e.g. `B::bigTalk as talk;`
 */
class TraitAliasNode extends TraitAdaptationStatementNode {
  /**
   * @var NameNode|TraitMethodReferenceNode
   */
  protected $traitMethodReference;

  /**
   * @var TokenNode
   */
  protected $visibility;

  /**
   * @var TokenNode
   */
  protected $alias;

  /**
   * @return NameNode|TraitMethodReferenceNode
   */
  public function getTraitMethodReference() {
    return $this->traitMethodReference;
  }

  /**
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->visibility;
  }

  /**
   * @return TokenNode
   */
  public function getAlias() {
    return $this->alias;
  }
}
