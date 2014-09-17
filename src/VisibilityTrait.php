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
      $this->removeVisibility();
    }
    else {
      if ($visibility === 'private' || $visibility === T_PRIVATE) {
        $visibility = Token::_private();
      }
      elseif ($visibility === 'protected' || $visibility === T_PROTECTED) {
        $visibility = Token::_protected();
      }
      elseif ($visibility === 'public' || $visibility === T_PUBLIC) {
        $visibility = Token::_public();
      }

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
    return $this;
  }
}
