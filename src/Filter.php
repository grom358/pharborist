<?php
namespace Pharborist;

use Pharborist\Filters\Combinator\AllCombinator;
use Pharborist\Filters\Combinator\AnyCombinator;
use Pharborist\Filters\ExecutableFilter;
use Pharborist\Filters\NewLineFilter;
use Pharborist\Filters\NodeTypeFilter;
use Pharborist\Filters\SingleNodeFilter;
use Pharborist\Namespaces\NameNode;
use Pharborist\Objects\ClassMethodCallNode;

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
  public static function any(array $filters) {
    return (new AnyCombinator())->addMultiple($filters);
  }

  /**
   * Callback returns true if all of the callbacks pass.
   *
   * @param callable[] $filters
   * @return callable
   */
  public static function all(array $filters) {
    return (new AllCombinator())->addMultiple($filters);
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
    return new SingleNodeFilter('Pharborist\Functions\FunctionDeclarationNode', func_get_args());
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
    return new SingleNodeFilter('Pharborist\Functions\FunctionCallNode', func_get_args());
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
    return new SingleNodeFilter('Pharborist\Objects\ClassNode', func_get_args());
  }

  /**
   * Callback to filter for calls to a class method.
   *
   * @param string $class_name
   *   Fully qualified class name or expression string.
   * @param string $method_name
   *   Method name or expression string.
   * @return callable
   *   Filter callable.
   */
  public static function isClassMethodCall($class_name, $method_name) {
    return function ($node) use ($class_name, $method_name) {
      if ($node instanceof ClassMethodCallNode) {
        $call_class_name_node = $node->getClassName();
        $call_class_name = $call_class_name_node instanceof NameNode ? $call_class_name_node->getAbsolutePath() : $call_class_name_node->getText();
        $class_matches = $call_class_name === $class_name;
        $method_matches = $node->getMethodName()->getText() === $method_name;
        return $class_matches && $method_matches;
      }
      return FALSE;
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

  /**
   * Callback to test if match to given token type.
   *
   * @param int|string $type
   *   Token type.
   *
   * @return callable
   */
  public static function isTokenType($type) {
    $types = func_get_args();
    return function ($node) use ($types) {
      return $node instanceof TokenNode && in_array($node->getType(), $types);
    };
  }

  /**
   * Callback to skip whitespace and comments.
   *
   * @return callable
   */
  public static function isNotHidden() {
    return new ExecutableFilter();
  }

  /**
   * Callback to match whitespace containing newlines.
   *
   * @return callable
   */
  public static function isNewline() {
    static $callback = NULL;
    if (!$callback) {
      $callback = new NewLineFilter();
    }
    return $callback;
  }
}
