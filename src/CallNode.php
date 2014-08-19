<?php
namespace Pharborist;

/**
 * A function/method call.
 */
abstract class CallNode extends ParentNode {
  use ArgumentTrait;
}
