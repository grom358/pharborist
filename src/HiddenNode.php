<?php
namespace Pharborist;

/**
 * Whitespace and comment nodes are not significant to the grammar, so mark
 * them as hidden.
 *
 * @internal
 */
class HiddenNode extends TokenNode {

}
