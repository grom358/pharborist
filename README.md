pharborist
==========

A PHP library to query and transform PHP source code via tree operations.

# Roadmap
The aim is to have an API to query and transform PHP source code with the same ease as using jQuery to modify HTML. An immediate short term goal is to use the library to replace deprecated calls in Drupal 8 core in the March 20-23 disruptive patch window. For example, https://drupal.org/node/2089331

The following features are also planned:
* Tests with 100% code coverage
* Integration with a third party library (suggestions welcomed) for extracting PHPDoc comments
* API to ease querying and transforming of the syntax tree
* Build a PHP source code formatter using the library
* Build a PHP code check using the library

Below is an example of how the API might look once its more developed:

```php
// Add use declaration if it does not already exist. Use UtilityString alias if conflict
$alias = $tree->ensureUseDeclaration('Drupal\Component\Utility\String', 'UtilityString');
// Find all calls to check_plain and rename them to use String::checkPlain
$tree->getCallsToFunction('check_plain')->renameTo($alias . '::checkPlain');
```

# Usage
```
require_once 'vendor/autoload.php';

use Pharborist\Tokenizer;
use Pharborist\TokenIterator;
use Pharborist\Parser;

$tokenizer = new Tokenizer();
$parser = new Parser();
$source = file_get_contents($filename);
$tokens = $tokenizer->getAll($source);
$tree = $parser->buildTree(new TokenIterator($tokens));

// check there only one namespace declaration
$namespace_count = 0;
foreach ($tree->children as $top_level_statement) {
  if ($top_level_statement instanceof NamespaceNode) {
    $namespace_count++;
    if ($namespace_count > 1) {
      die('More then one namespace at line ' . $top_level_statement->getSourcePosition());
    }
  }
}
```
