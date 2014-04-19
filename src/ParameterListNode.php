<?php
namespace Pharborist;

/**
 * Function/method parameters
 */
class ParameterListNode extends ParentNode {
  public function getParameters() {
    return $this->childrenByInstance('\Pharborist\ParameterNode');
  }
}
