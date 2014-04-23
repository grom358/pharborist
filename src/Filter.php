<?php
namespace Pharborist;

/**
 * Factory for creating common callback filters.
 */
class Filter {
  /**
   * @param string $class_name
   * @return callable
   */
  public static function isInstanceOf($class_name) {
    return function ($node) use ($class_name) {
      return $node instanceof $class_name;
    };
  }

  /**
   * Callback to filter comments.
   * @param bool $include_doc_comment
   * @return callable
   */
  public static function isComment($include_doc_comment = TRUE) {
    if ($include_doc_comment) {
      return function ($node) {
        if ($node instanceof LineCommentBlockNode) {
          return TRUE;
        }
        elseif ($node instanceof CommentNode) {
          return !($node->parent() instanceof LineCommentBlockNode);
        }
        else {
          return FALSE;
        }
      };
    }
    else {
      return function ($node) {
        if ($node instanceof LineCommentBlockNode) {
          return TRUE;
        }
        elseif ($node instanceof DocCommentNode) {
          return FALSE;
        }
        elseif ($node instanceof CommentNode) {
          return !($node->parent() instanceof LineCommentBlockNode);
        }
        else {
          return FALSE;
        }
      };
    }
  }
}
