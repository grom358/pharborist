<?php
namespace Pharborist\Objects;

use Pharborist\Filter;
use Pharborist\NameResolutionInterface;
use Pharborist\Parser;
use Pharborist\Token;
use Pharborist\TokenNode;

/**
 * Class declaration.
 */
class ClassNode extends SingleInheritanceNode implements NameResolutionInterface {
  /**
   * @var TokenNode
   */
  protected $abstract;

  /**
   * @var TokenNode
   */
  protected $final;

  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @param $class_name
   * @return ClassNode
   */
  public static function create($class_name) {
    $class_node = Parser::parseSnippet("class $class_name {}")->remove();
    return $class_node;
  }

  /**
   * @return TokenNode
   */
  public function getAbstract() {
    return $this->abstract;
  }

  /**
   * @param boolean $is_abstract
   * @return $this
   */
  public function setAbstract($is_abstract) {
    if ($is_abstract) {
      if (!isset($this->abstract)) {
        $this->abstract = Token::_abstract();
        $this->prepend([
          $this->abstract,
          Token::space(),
        ]);
        $this->setFinal(FALSE);
      }
    }
    else {
      if (isset($this->abstract)) {
        // Remove whitespace.
        $this->abstract->next()->remove();
        // Remove abstract.
        $this->abstract->remove();
      }
    }
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getFinal() {
    return $this->final;
  }

  /**
   * @param boolean $is_final
   * @return $this
   */
  public function setFinal($is_final) {
    if ($is_final) {
      if (!isset($this->final)) {
        $this->final = Token::_final();
        $this->prepend([
          $this->final,
          Token::space(),
        ]);
        $this->setAbstract(FALSE);
      }
    }
    else {
      if (isset($this->final)) {
        // Remove whitespace.
        $this->final->next()->remove();
        // Remove final.
        $this->final->remove();
      }
    }
    return $this;
  }

  public function getFullyQualifiedName() {
    return '\\' . $this->getQualifiedName();
  }

  public function getQualifiedName() {
    $ns = $this->closest(Filter::isInstanceOf('\Pharborist\Namespaces\NamespaceNode'));
    $name = $ns ? $ns->getName()->getText() . '\\' : '';
    return $name . $this->getUnqualifiedName();
  }

  public function getUnqualifiedName() {
    return $this->name->getText();
  }
}
