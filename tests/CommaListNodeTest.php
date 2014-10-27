<?php
namespace Pharborist;

class CommaListNodeTest extends \PHPUnit_Framework_TestCase {
  /**
   * Create mock Node.
   * @param $text
   * @return Node
   */
  protected function createNode($text) {
    $mock = $this->getMockForAbstractClass('\Pharborist\Node');
    $mock->expects($this->any())
      ->method('getText')
      ->will($this->returnValue($text));
    return $mock;
  }

  public function testGet() {
    $list = new CommaListNode();
    $this->assertCount(0, $list->getItems());
    $list->append([
      $this->createNode('$a'),
      Token::comma(),
      Token::space(),
      $this->createNode('$b'),
      Token::comma(),
      Token::space(),
      $this->createNode('$c')
    ]);
    $items = $list->getItems();
    $this->assertCount(3, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $this->assertEquals('$b', $items[1]->getText());
    $this->assertEquals('$c', $items[2]->getText());

    $this->assertEquals('$a', $list->getItem(0)->getText());
    $this->assertEquals('$b', $list->getItem(1)->getText());
    $this->assertEquals('$c', $list->getItem(2)->getText());
  }

  public function testPrepend() {
    $list = new CommaListNode();
    $list->prependItem($this->createNode('$b'));
    $items = $list->getItems();
    $this->assertCount(1, $items);
    $this->assertEquals('$b', $items[0]->getText());
    $list->prependItem($this->createNode('$a'));
    $items = $list->getItems();
    $this->assertCount(2, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $this->assertEquals('$b', $items[1]->getText());
  }

  public function testAppend() {
    $list = new CommaListNode();
    $list->appendItem($this->createNode('$a'));
    $items = $list->getItems();
    $this->assertCount(1, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $list->appendItem($this->createNode('$b'));
    $items = $list->getItems();
    $this->assertCount(2, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $this->assertEquals('$b', $items[1]->getText());
  }

  public function testInsert() {
    $list = new CommaListNode();
    $list->insertItem($this->createNode('$c'), 0);
    $items = $list->getItems();
    $this->assertCount(1, $items);
    $this->assertEquals('$c', $items[0]->getText());
    $list->insertItem($this->createNode('$a'), 0);
    $items = $list->getItems();
    $this->assertCount(2, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $this->assertEquals('$c', $items[1]->getText());
    $list->insertItem($this->createNode('$b'), 1);
    $items = $list->getItems();
    $this->assertCount(3, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $this->assertEquals('$b', $items[1]->getText());
    $this->assertEquals('$c', $items[2]->getText());
  }

  public function testRemove() {
    $list = new CommaListNode();
    $list->appendItem($this->createNode('$a'));
    $list->appendItem($this->createNode('$b'));
    $list->appendItem($this->createNode('$c'));
    $list->removeItem(1);
    $items = $list->getItems();
    $this->assertCount(2, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $this->assertEquals('$c', $items[1]->getText());
    $list->removeItem(1);
    $items = $list->getItems();
    $this->assertCount(1, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $list->removeItem(0);
    $items = $list->getItems();
    $this->assertCount(0, $items);
  }

  public function testPop() {
    $list = new CommaListNode();
    $list->appendItem($this->createNode('$a'));
    $list->appendItem($this->createNode('$b'));
    $list->appendItem($this->createNode('$c'));
    $this->assertEquals('$c', $list->pop()->getText());
    $items = $list->getItems();
    $this->assertCount(2, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $this->assertEquals('$b', $items[1]->getText());
    $this->assertEquals('$b', $list->pop()->getText());
    $items = $list->getItems();
    $this->assertCount(1, $items);
    $this->assertEquals('$a', $items[0]->getText());
    $this->assertEquals('$a', $list->pop()->getText());
    $items = $list->getItems();
    $this->assertCount(0, $items);
    $this->assertNull($list->pop());
  }

  public function testShift() {
    $list = new CommaListNode();
    $list->appendItem($this->createNode('$a'));
    $list->appendItem($this->createNode('$b'));
    $list->appendItem($this->createNode('$c'));
    $this->assertEquals('$a', $list->shift()->getText());
    $items = $list->getItems();
    $this->assertCount(2, $items);
    $this->assertEquals('$b', $items[0]->getText());
    $this->assertEquals('$c', $items[1]->getText());
    $this->assertEquals('$b', $list->shift()->getText());
    $items = $list->getItems();
    $this->assertCount(1, $items);
    $this->assertEquals('$c', $items[0]->getText());
    $this->assertEquals('$c', $list->shift()->getText());
    $items = $list->getItems();
    $this->assertCount(0, $items);
    $this->assertNull($list->shift());
  }

  public function testToArrayNode() {
    $list = new CommaListNode();
    $list->appendItem(Node::fromValue('foo'));
    $list->appendItem(Node::fromValue('baz'));
    $list->appendItem(Node::fromValue(30));
    $array = $list->toArrayNode();
    $this->assertInstanceOf('\Pharborist\Types\ArrayNode', $array);
    /** @var \PHarborist\Types\ScalarNode[] $elements */
    $elements = $array->getElements();
    $this->assertCount(3, $elements);
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $elements[0]);
    $this->assertEquals('foo', $elements[0]->toValue());
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $elements[1]);
    $this->assertEquals('baz', $elements[1]->toValue());
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $elements[2]);
    $this->assertEquals(30, $elements[2]->toValue());
    $this->assertEquals("['foo', 'baz', 30]", $array->getText());
  }
}
