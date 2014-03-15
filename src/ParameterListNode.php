<?php
namespace Pharborist;

/**
 * List of parameters.
 * @package Pharborist
 */
class ParameterListNode extends ListNode {
  /**
   * @var Node[]
   */
  public $parameters;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->parameters = &$this->items;
  }
}
