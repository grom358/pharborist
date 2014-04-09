<?php
namespace Pharborist;

/**
 * A goto statement.
 */
class GotoStatementNode extends StatementNode {
  protected $properties = array(
    'label' => NULL,
  );

  /**
   * @var Node
   */
  public function getLabel() {
    return $this->properties['label'];
  }
}
