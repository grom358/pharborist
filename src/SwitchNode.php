<?php
namespace Pharborist;

/**
 * A switch control structure.
 */
class SwitchNode extends StatementNode {
  protected $properties = array(
    'switchOn' => NULL,
    'cases' => array(),
  );

  /**
   * @return Node
   */
  public function getSwitchOn() {
    return $this->properties['switchOn'];
  }

  /**
   * @return CaseNode[]
   */
  public function getCases() {
    /** @var StatementBlockNode $cases */
    $cases = $this->properties['cases'];
    return $cases->getStatements();
  }
}
