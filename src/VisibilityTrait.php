<?php
namespace Pharborist;

trait VisibilityTrait {
  /**
   * @var TokenNode
   */
  protected $visibility;

  /**
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->visibility;
  }

  /**
   * @param TokenNode $visibility
   * @return $this
   */
  public function setVisibility($visibility) {
    if ($visibility === NULL) {
      // Remove whitespace after visibility keyword.
      $this->visibility->next()->remove();
      // Remove visibility keyword.
      $this->visibility->remove();
    }
    else {
      if (isset($this->visibility)) {
        $this->visibility->replaceWith($visibility);
      }
      else {
        $this->prepend([
          $visibility,
          Token::space(),
        ]);
        $this->visibility = $visibility;
      }
    }
  }
}
