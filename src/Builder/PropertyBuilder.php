<?php
namespace Pharborist\Builder;

class PropertyBuilder {
  protected $name;

  protected $visibility;

  protected $static;

  public function __construct($methodName) {
    $this->name = $methodName;
    $this->visibility = 'public';
    $this->static = false;
  }

  public function setVisibility($visibility) {
    $this->visibility = $visibility;
    return $this;
  }

  public function setStatic($static = TRUE) {
    $this->static = $static;
    return $this;
  }

  public function __toString() {
    $template = $this->visibility . ' ';
    $template .= $this->name . ';';
    return $template;
  }
}
