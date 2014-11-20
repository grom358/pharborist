<?php
namespace Pharborist;

interface VisitorInterface {
  public function visitBegin(ParentNode $node);

  public function visitChild(Node $node);

  public function visitEnd(ParentNode $node);
}
