<?php
namespace Pharborist\Index;

class NamespaceIndex extends BaseIndex {

  use ClassContainerTrait;
  use ConstantContainerTrait;
  use FunctionContainerTrait;
  use InterfaceContainerTrait;
  use TraitContainerTrait;

}
