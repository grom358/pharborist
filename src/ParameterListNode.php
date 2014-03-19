<?php
namespace Pharborist;

/**
 * List of parameters.
 * @package Pharborist
 */
class ParameterListNode extends ListNode {
  /**
   * @var ParameterNode[]
   */
  public $parameters;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->parameters = &$this->items;
  }
}
