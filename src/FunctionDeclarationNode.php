<?php
namespace Pharborist;

/**
 * A function declaration.
 */
class FunctionDeclarationNode extends StatementNode {
  use FullyQualifiedNameTrait;
  use FunctionTrait;
}
