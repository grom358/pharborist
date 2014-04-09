<?php
namespace Pharborist;

/**
 * foreach control structure.
 */
class ForeachNode extends StatementNode {
  protected $properties = array(
    'onEach' => NULL,
    'key' => NULL,
    'value' => NULL,
    'body' => NULL,
  );

  /**
   * @return Node
   */
  public function getOnEach() {
    return $this->properties['onEach'];
  }

  /**
   * @return Node
   */
  public function getKey() {
    return $this->properties['key'];
  }

  /**
   * @return Node
   */
  public function getValue() {
    return $this->properties['value'];
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
