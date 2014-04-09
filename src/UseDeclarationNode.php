<?php
namespace Pharborist;

/**
 * Use declaration.
 */
class UseDeclarationNode extends ParentNode {
  protected $properties = array(
    'namespacePath' => NULL,
    'alias' => NULL,
  );

  /**
   * @return Node
   */
  public function getNamespacePath() {
    return $this->properties['namespacePath'];
  }

  /**
   * @return Node
   */
  public function getAlias() {
    return $this->properties['alias'];
  }
}
