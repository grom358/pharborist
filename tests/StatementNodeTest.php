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
    $this->assertEquals(7, Parser::parseSnippet($text)->getLineCount());
    // What, you haven't seen Spaceballs?
    $this->assertEquals(1, Parser::parseSnippet('$combination = 12345;')->getLineCount());


    $text = <<<'END'
db_delete('variable')
  ->condition('name', 'cron_last')
  ->execute();
END;
    $this->assertEquals(3, Parser::parseSnippet($text)->getLineCount());
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
    $comment = Parser::parseSnippet($original)->toComment();
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
    // of the TopNode, not part of the original statement.
    $expected = <<<END
<?php // variable_get()
// Verboten in Drupal 8.
// This is a haiku.
$original
END;
    $node = Parser::parseSnippet($original)->addCommentAbove($comment);
    $this->assertEquals($expected, $node->parent()->getText());
  }
}
