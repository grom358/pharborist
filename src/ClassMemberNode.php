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
