<?php
namespace Pharborist;

/**
 * A trait alias.
 *
 * For example, B::bigTalk as talk;
 */
class TraitAliasNode extends ParentNode {
  /**
   * @var Node
   */
  public $methodReference;

  /**
   * @var Node
   */
  public $visibility;

  /**
   * @var Node
   */
  public $alias;
}
