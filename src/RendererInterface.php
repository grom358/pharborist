<?php
namespace Pharborist;

interface RendererInterface {
  /**
   * Renders a node. This method acts like a central dispatcher to figure out
   * how to render the node, and it returns the finished output.
   *
   * @return string
   */
  public function render(NodeInterface $node);
}
