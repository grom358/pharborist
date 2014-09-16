<?php
namespace Pharborist;

/**
 * A class member.
 */
class ClassMemberNode extends ParentNode {
  /**
   * @var Node
   */
  protected $name;

  /**
   * @var Node
   */
  protected $value;

  /**
   * Creates a new class member.
   *
   * @param string $name
   *  The name of the member, with or without the leading $.
   * @param \Pharborist\ExpressionNode $value
   *  The default value of the member, if any.
   * @param string $visibility
   *  The member's visibility. Can be public, private, or protected. Defaults to
   *  public.
   *
   * @return ClassMemberListNode
   *
   * @todo Not all expressions can be default values, but I forget what sorts of
   * expressions are valid for this. Will need better sanity checking here.
   */
  public static function create($name, ExpressionNode $value = NULL, $visibility = 'public') {
    $code = $visibility . ' $' . ltrim($name, '$');
    if ($value instanceof ExpressionNode) {
      $code .= ' = ' . $value;
    }
    return Parser::parseSnippet('class Foo { ' . $code . '; }')->getBody()->firstChild()->remove();
  }

  /**
   * @return Node
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return Node
   */
  public function getValue() {
    return $this->value;
  }
}
