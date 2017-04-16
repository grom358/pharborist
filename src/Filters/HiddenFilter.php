<?php

namespace Pharborist\Filters;

use Pharborist\CommentNode;
use Pharborist\LineCommentBlockNode;
use Pharborist\NodeInterface;
use Pharborist\WhitespaceNode;

/**
 * Only passes white space and comments.
 */
class HiddenFilter implements FilterInterface {

  /**
   * {@inheritdoc}
   */
  public function __invoke(NodeInterface $node) {
    return (
      $node instanceof WhitespaceNode
      ||
      $node instanceof CommentNode
      ||
      $node instanceof LineCommentBlockNode
    );
  }

}
