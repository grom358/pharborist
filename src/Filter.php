<?php
namespace Pharborist;

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
    return function ($node) use ($filters) {
      foreach ($filters as $filter) {
        if ($filter($node)) {
          return TRUE;
        }
      }
      return FALSE;
    };
  }

  /**
   * Callback returns true if all of the callbacks pass.
   *
   * @param callable[] $filters
   * @return callable
   */
  public static function all($filters) {
    return function ($node) use ($filters) {
      foreach ($filters as $filter) {
        if (!$filter($node)) {
          return FALSE;
        }
      }
      return TRUE;
    };
  }

  /**
   * @param string $class_name
   * @return callable
   */
  public static function isInstanceOf($class_name) {
    $classes = func_get_args();

    return function ($node) use ($classes) {
      foreach ($classes as $class) {
        if ($node instanceof $class) {
          return TRUE;
        }
      }
      return FALSE;
    };
  }

  /**
   * Callback to filter for specific function declaration.
   * @param string $function_name
   * @return callable
   */
  public static function isFunction($function_name) {
    return function ($node) use ($function_name) {
      if ($node instanceof FunctionDeclarationNode) {
        return $node->getName()->getText() === $function_name;
      }
      return FALSE;
    };
  }

  /**
   * Callback to filter for calls to a function.
   * @param string $function_name
   * @return callable
   */
  public static function isFunctionCall($function_name) {
    return function ($node) use ($function_name) {
      if ($node instanceof FunctionCallNode) {
        return $node->getName()->getText() === $function_name;
      }
      return FALSE;
    };
  }

  /**
   * Callback to filter for specific class declaration.
   * @param string $class_name
   * @return callable
   */
  public static function isClass($class_name) {
    return function ($node) use ($class_name) {
      if ($node instanceof ClassNode) {
        return $node->getName()->getText() === $class_name;
      }
      return FALSE;
    };
  }

  /**
   * Callback to filter for calls to a class method.
   * @param string $class_name
   * @param string $method_name
   * @return callable
   */
  public static function isClassMethodCall($class_name, $method_name) {
    return function ($node) use ($class_name, $method_name) {
      if ($node instanceof ClassMethodCallNode) {
        $class_matches = $node->getClassName()->getText() === $class_name;
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
}
