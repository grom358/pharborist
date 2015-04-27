<?php
namespace Pharborist;

use Pharborist\Filters\ClassFilter;
use Pharborist\Filters\ClassMethodCallFilter;
use Pharborist\Filters\Combinator\AllCombinator;
use Pharborist\Filters\Combinator\AnyCombinator;
use Pharborist\Filters\FunctionCallFilter;
use Pharborist\Filters\FunctionDeclarationFilter;
use Pharborist\Filters\NodeTypeFilter;

/**
 * Factory for creating common callback filters.
 */
class Filter {
  /**
   * Callback returns true if any of the callbacks pass.
   *
   * @param callable[] $filters
   * @return callable
   */
  public static function any($filters) {
    $combinator = new AnyCombinator();

    foreach ($filters as $filter) {
      $combinator->add($filter);
    }

    return $combinator;
  }

  /**
   * Callback returns true if all of the callbacks pass.
   *
   * @param callable[] $filters
   * @return callable
   */
  public static function all($filters) {
    $combinator = new AllCombinator();

    foreach ($filters as $filter) {
      $combinator->add($filter);
    }

    return $combinator;
  }

  /**
   * Callback to filter for nodes of certain types.
   *
   * @param string $class_name ...
   *  At least one fully-qualified Pharborist node type to search for.
   *
   * @return callable
   */
  public static function isInstanceOf($class_name) {
    return new NodeTypeFilter(func_get_args());
  }

  /**
   * Callback to filter for specific function declaration.
   *
   * @param string $function_name ...
   *  At least one function name to search for.
   *
   * @return callable
   */
  public static function isFunction($function_name) {
    return new FunctionDeclarationFilter(func_get_args());
  }

  /**
   * Callback to filter for calls to a function.
   *
   * @param string $function_name ...
   *  At least one function name to search for.
   *
   * @return callable
   */
  public static function isFunctionCall($function_name) {
    return new FunctionCallFilter(func_get_args());
  }

  /**
   * Callback to filter for specific class declaration.
   *
   * @param string $class_name ...
   *  At least one class name to search for.
   *
   * @return callable
   */
  public static function isClass($class_name) {
    return new ClassFilter(func_get_args());
  }

  /**
   * Callback to filter for calls to a class method.
   * @param string $class_name
   * @param string $method_name
   * @return callable
   */
  public static function isClassMethodCall($class_name, $method_name) {
    return new ClassMethodCallFilter($class_name, $method_name);
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

  /**
   * Callback to test if match to given node.
   *
   * @param Node $match
   *
   * @return callable
   */
  public static function is(Node $match) {
    return function ($node) use ($match) {
      return $node === $match;
    };
  }
}
