<?php
namespace Pharborist;

class StatementNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetLineCount() {
    $text = <<<'END'
class Foobar {

  protected $name;

  protected $age;

}
END;
    /** @var \Pharborist\StatementNode $node */
    $node = Parser::parseSnippet($text);
    $this->assertEquals(7, $node->getLineCount());
    // What, you haven't seen Spaceballs?
    $node = Parser::parseSnippet('$combination = 12345;');
    $this->assertEquals(1, $node->getLineCount());


    $text = <<<'END'
db_delete('variable')
  ->condition('name', 'cron_last')
  ->execute();
END;
    $node = Parser::parseSnippet($text);
    $this->assertEquals(3, $node->getLineCount());
  }

  public function testToComment() {
    $original = <<<'END'
class Foo {
  private $larry;
  private $moe;
  private $curly;
}
END;

    // The expected text needs the extra line, because PHP ignores the new line
    // before END for whatever reason. Weird, but the tests pass this way.
    $expected = <<<'END'
// class Foo {
//   private $larry;
//   private $moe;
//   private $curly;
// }

END;
    /** @var StatementNode $statement_node */
    $statement_node = Parser::parseSnippet($original);
    $comment = $statement_node->toComment();
    $this->assertEquals($expected, $comment->getText());
    $this->assertEquals($original, $comment->uncomment()->getText());
  }

  public function testAddCommentAbove() {
    $original = '$value = variable_get("my_variable", NULL);';
    $comment = <<<END
variable_get()
Verboten in Drupal 8.
This is a haiku.
END;
    // The <?php tag is there because the comment will be an immediate child
    // of the RootNode, not part of the original statement.
    $expected = <<<END
<?php // variable_get()
// Verboten in Drupal 8.
// This is a haiku.
$original
END;
    /** @var StatementNode $statement_node */
    $statement_node = Parser::parseSnippet($original);
    $node = $statement_node->addCommentAbove($comment);
    $this->assertEquals($expected, $node->parent()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidCommentAbove() {
    $original = '$value = variable_get("my_variable", NULL);';
    /** @var StatementNode $statement_node */
    $statement_node = Parser::parseSnippet($original);
    $statement_node->addCommentAbove(NULL);
  }
}
