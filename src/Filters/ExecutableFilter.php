<?php

namespace Pharborist\Filters;

use Pharborist\CommentNode;
use Pharborist\LineCommentBlockNode;
use Pharborist\NodeInterface;
use Pharborist\WhitespaceNode;

/**
 * Passes executable nodes only (anything that isn't white space or a comment).
 */
class ExecutableFilter implements FilterInterface {

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
