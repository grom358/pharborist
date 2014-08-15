<?php
namespace Pharborist\Builder;

class MethodBuilder {
  protected $name;

  protected $body;

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

  public function setBody($body) {
    $this->body = trim($body, "\n\r") . "\n";
    return $this;
  }

  public function __toString() {
    $template = $this->visibility . ' ';
    $template .= 'function ' . $this->name . '(';
    $template .= ") {\n";
    $template .= $this->body;
    $template .= str_repeat(' ', 2) . '}';
    return $template;
  }
}
