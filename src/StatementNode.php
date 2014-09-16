<?php
namespace Pharborist;

/**
 * Base class for any statement.
 *
 * <p>A statement is a single executable unit of PHP code. You can think of a
 * statement as a single &quot;sentence&quot; of code, usually ending with
 * a semicolon. A single statement usually (but not always!) occupies a single
 * line.</p>
 * <p>Here's an example of a perfectly valid statement:</p>
 * <pre><code>echo "Let's not go to Camelot. 'Tis a silly place.\n";</code></pre>
 * <p>Statements can contain other statements, or a block of statements surrounded
 * by curly braces. A single statement is usually made up of one or more
 * expressions.</p>
 * <p>Declarations are also statements. For instance, if/elseif/else and switch/case
 * structures are statements, including all of their blocks. So is are class and function
 * declarations. The body of the class or function is a statement block, but it's
 * contained by the class or function declaration, which is a statement.</p>
 */
abstract class StatementNode extends ParentNode {
  /**
   * Gets the number of lines spanned by this statement.
   *
   * @return integer
   *  Always returns at least one, because any statement will be at least
   *  one line long.
   */
  public function getLineCount() {
    $count = 1;

    $this
      ->find(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))
      ->each(function(WhitespaceNode $node) use (&$count) {
        $count += $node->getNewlineCount();
      });

    return $count;
  }

  /**
   * Creates a commented-out version of this statement.
   *
   * @return \Pharborist\CommentNode|\Pharborist\LineCommentBlockNode
   */
  public function toComment() {
    return CommentNode::create($this->getText());
  }

  /**
   * Adds a line comment block above the statement.
   *
   * @param \Pharborist\LineCommentBlockNode|string $comment
   *  The comment to add.
   *
   * @return $this
   *
   * @throws \InvalidArgumentException
   */
  public function addCommentAbove($comment) {
    if ($comment instanceof LineCommentBlockNode) {
      $this->before($comment);
    }
    elseif (is_string($comment)) {
      $this->addCommentAbove(LineCommentBlockNode::create($comment));
    }
    else {
      throw new \InvalidArgumentException();
    }
    return $this;
  }
}
