pharborist
==========

![Pharborist logo](./docs/logo_128px.png "Pharborist logo")

A PHP library to query and transform PHP source code via tree operations.

# Roadmap
* [ ] Tests with 100% code coverage
* [x] Integration with a third party library for PHPDoc comments
* [ ] API to ease querying and transforming of the syntax tree
* [ ] Build a PHP source code formatter using the library (Partially completed).
* [ ] Build a PHP code check using the library

Below is an example of how the API might look once its more developed:

```php
// Add use declaration if it does not already exist. Use UtilityString alias if conflict
$alias = $tree->ensureUseDeclaration('Drupal\Component\Utility\String', 'UtilityString');
// Find all calls to check_plain and rename them to use String::checkPlain
$function_calls = $tree->find(Filter::functionCall('check_plain'));
foreach ($function_calls as $call) {
  $class_method_call = ClassMethodCallNode::create($alias, 'check_plain', $call->getArgumentList());
  $call->replaceWith($class_method_call);
}
```

# Usage
```php
require_once 'vendor/autoload.php';

use Pharborist\Parser;
use Pharborist\Namespaces\NamespaceNode;
use Pharborist\Filter;

$filename = $argv[1];
$tree = Parser::parseFile($filename);

// check there only one namespace declaration
$namespaces = $tree->children(Filter::isInstanceOf('\Pharborist\Namespaces\NamespaceNode'));
if ($namespaces->count() > 1) {
  die('More then one namespace at line ' . $namespaces[1]->getLineNumber() . PHP_EOL);
}
```
[![Build Status](https://travis-ci.org/grom358/pharborist.png?branch=master)](https://travis-ci.org/grom358/pharborist)
