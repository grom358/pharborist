<?php
namespace Pharborist;

/**
 * Used by the parser to hold partial matches.
 */
class PartialNode extends ParentNode {
  protected $properties = array(
    'docComment' => NULL,
  );
}
