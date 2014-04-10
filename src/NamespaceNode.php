<?php
namespace Pharborist;

/**
 * Namespace declaration.
 */
class NamespaceNode extends StatementNode {
  protected $properties = array(
    'name' => NULL,
    'body' => NULL,
  );

  /**
   * @return Node
   */
  public function getName() {
    return $this->properties['name'];
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
