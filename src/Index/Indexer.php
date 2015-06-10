<?php
namespace Pharborist\Index;

use Pharborist\Constants\ConstantDeclarationNode;
use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Objects\ClassMethodNode;
use Pharborist\Objects\InterfaceMethodNode;
use Pharborist\Objects\InterfaceNode;
use Pharborist\Objects\SingleInheritanceNode;
use Pharborist\Objects\TraitAliasNode;
use Pharborist\Objects\TraitNode;
use Pharborist\Objects\TraitPrecedenceNode;
use Pharborist\Parser;
use Pharborist\RootNode;
use Pharborist\VisitorBase;
use Pharborist\Objects\ClassNode;
use phpDocumentor\Reflection\DocBlock;

/**
 * Creates/updates project index.
 */
class Indexer extends VisitorBase {

  /**
   * @var string
   */
  private $baseDir;

  /**
   * @var Error[]
   */
  private $errors;

  /**
   * @var FileSet
   */
  private $fileSet;

  /**
   * @var FileIndex[]
   */
  private $files;

  /**
   * @var ClassIndex[]
   */
  private $classes;

  /**
   * @var TraitIndex[]
   */
  private $traits;

  /**
   * @var InterfaceIndex[]
   */
  private $interfaces;

  /**
   * @var ConstantIndex[]
   */
  private $constants;

  /**
   * @var FunctionIndex[]
   */
  private $functions;

  /**
   * Classes defined by the current file being processed.
   *
   * @var string[]
   */
  private $fileClasses;

  /**
   * Traits defined by the current file being processed.
   *
   * @var string[]
   */
  private $fileTraits;

  /**
   * Interfaces defined by the current file being processed.
   *
   * @var string[]
   */
  private $fileInterfaces;

  /**
   * Constants defined by the current file being processed.
   *
   * @var string[]
   */
  private $fileConstants;

  /**
   * Functions defined by the current file being processed.
   *
   * @var string[]
   */
  private $fileFunctions;

  /**
   * Setup indexer.
   *
   * @param string $dir
   *   Directory for index.
   */
  public function __construct($dir) {
    $loaded = FALSE;
    $this->baseDir = $dir;
    if (file_exists($dir . '/index.json')) {
      // Load existing index.
      $projectIndex = ProjectIndex::load($dir);
      $this->fileSet = $projectIndex->getFileSet();
      $this->files = $projectIndex->getFiles();
      $this->classes = $projectIndex->getClasses();
      foreach ($this->classes as $classIndex) {
        $classIndex->clear();
      }
      $this->traits = $projectIndex->getTraits();
      foreach ($this->traits as $traitIndex) {
        $traitIndex->clear();
      }
      $this->interfaces = $projectIndex->getInterfaces();
      foreach ($this->interfaces as $interfaceIndex) {
        $interfaceIndex->clear();
      }
      $this->constants = $projectIndex->getConstants();
      $this->functions = $projectIndex->getFunctions();
      $loaded = TRUE;
    }
    if (!$loaded) {
      $this->fileSet = new FileSet();
      $this->files = [];
      $this->classes = [];
      $this->traits = [];
      $this->interfaces = [];
      $this->constants = [];
      $this->functions = [];
    }

    $this->fileClasses = [];
    $this->fileTraits = [];
    $this->fileInterfaces = [];
    $this->fileConstants = [];
    $this->fileFunctions = [];
  }

  /**
   * Get file set for current index.
   *
   * @return FileSet
   */
  public function getFileSet() {
    return $this->fileSet;
  }

  /**
   * Check if filename requires indexing.
   *
   * @param string $filename
   * @return bool
   */
  protected function indexRequired($filename) {
    if (!isset($this->files[$filename])) {
      return TRUE;
    }
    $fileIndex = $this->files[$filename];
    // If file has been modified, indexing is required.
    $lastModified = filemtime($filename);
    if ($lastModified === FALSE || $lastModified >= $fileIndex->getLastIndexed()) {
      return TRUE;
    }
    // If file contents have changed, indexing is required.
    $currentHash = md5_file($filename);
    if ($currentHash === FALSE || $currentHash !== $fileIndex->getHash()) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Remove all definitions that belong to a file.
   *
   * @param FileIndex $fileIndex
   */
  protected function fileRemoved(FileIndex $fileIndex) {
    foreach ($fileIndex->getClasses() as $classFqn) {
      unset($this->classes[$classFqn]);
    }
    foreach ($fileIndex->getTraits() as $traitFqn) {
      unset($this->traits[$traitFqn]);
    }
    foreach ($fileIndex->getInterfaces() as $interfaceFqn) {
      unset($this->interfaces[$interfaceFqn]);
    }
    foreach ($fileIndex->getConstants() as $constantFqn) {
      unset($this->constants[$constantFqn]);
    }
    foreach ($fileIndex->getFunctions() as $functionFqn) {
      unset($this->functions[$functionFqn]);
    }
    unset($this->files[$fileIndex->getFilename()]);
  }

  /**
   * Process file if new or updated.
   *
   * @param string $filename
   *   File to process.
   */
  protected function processFile($filename) {
    // Process file if indexing is required.
    if ($this->indexRequired($filename)) {
      if (isset($this->files[$filename])) {
        $this->fileRemoved($this->files[$filename]);
      }

      /** @var RootNode $tree */
      $tree = Parser::parseFile($filename);
      $tree->acceptVisitor($this);

      $hash = md5_file($filename);
      $this->files[$filename] = new FileIndex(
        $filename,
        time(),
        $hash,
        $this->fileClasses,
        $this->fileTraits,
        $this->fileInterfaces,
        $this->fileConstants,
        $this->fileFunctions
      );

      $this->fileClasses = [];
      $this->fileTraits = [];
      $this->fileInterfaces = [];
      $this->fileConstants = [];
      $this->fileFunctions = [];
    }
  }

  /**
   * @param ClassIndex|TraitIndex $index
   *   Class or trait.
   *
   * @return array
   *   Properties and methods from traits.
   */
  private function resolveTraits($index) {
    $traits = $index->getTraits();
    $traitPrecedences = $index->getTraitPrecedences();
    $traitAliases = $index->getTraitAliases();
    // Collect properties and methods from traits.
    $traitProperties = [];
    $traitMethods = [];
    foreach ($traitPrecedences as $methodName => $traitPrecedence) {
      $traitFqn = $traitPrecedence->getOwnerTrait();
      if (!isset($this->traits[$traitFqn])) {
        // Missing trait error already raised.
      }
      elseif (!in_array($traitFqn, $traits)) {
        $this->errors[] = new Error($index->getPosition(), sprintf(
          "Required trait %s wasn't added in %s %s at %s:%d",
          $traitFqn,
          $index instanceof ClassIndex ? 'class' : 'trait',
          $index->getName(),
          $index->getPosition()->getFilename(),
          $index->getPosition()->getLineNumber()
        ));
      }
      else {
        $trait = $this->traits[$traitFqn];
        $traitMethods = $trait->getOwnMethods();
        if (!isset($traitMethods[$traitPrecedence->getMethodName()])) {
          $this->errors[] = new Error($traitPrecedence->getPosition(), sprintf(
            "Trait %s missing method %s at %s:%d",
            $traitFqn,
            $traitPrecedence->getMethodName(),
            $traitPrecedence->getPosition()->getFilename(),
            $traitPrecedence->getPosition()->getLineNumber()
          ));
        }
        else {
          $method = $traitMethods[$traitPrecedence->getMethodName()];
          $traitMethods[$method->getName()] = $method;
        }
      }
    }
    foreach ($traitAliases as $traitAlias) {
      $traitFqn = $traitAlias->getOwnerTrait();
      if (!isset($this->traits[$traitFqn])) {
        // @todo Missing trait.
      }
      else {
        $trait = $this->traits[$traitFqn];
        $method = $trait->getOwnMethods()[$traitAlias->getMethodName()];
        $aliasedMethod = new MethodIndex(
          $method->getPosition(),
          $traitAlias->getAliasName(),
          $method->getOwner(),
          $traitAlias->getAliasVisibility() ?: $method->getVisibility(),
          $method->isFinal(),
          $method->isStatic(),
          $method->isAbstract(),
          $method->getParameters(),
          $method->getReturnTypes()
        );
        // @todo check alias doesn't conflict?
        $traitMethods[$traitAlias->getAliasName()] = $aliasedMethod;
      }
    }
    // @todo check for method conflicts
    foreach ($traits as $traitFqn) {
      if (isset($this->traits[$traitFqn])) {
        $trait = $this->traits[$traitFqn];
        foreach ($trait->getOwnMethods() as $method) {
          if (!isset($traitMethods[$method->getName()])) {
            $traitMethods[$method->getName()] = $method;
          }
        }
      }
    }
    $index
      ->setTraitProperties($traitProperties)
      ->setTraitMethods($traitMethods);
  }

  /**
   * @param InterfaceIndex $interfaceIndex
   */
  protected function inheritInterface($interfaceIndex, &$inheritedConstants, &$inheritedMethods) {
    foreach ($interfaceIndex->getExtends() as $parentFqn) {
      if (isset($this->interfaces[$parentFqn])) {
        $parentInterfaceIndex = $this->interfaces[$parentFqn];
        $this->inheritInterface($parentInterfaceIndex, $inheritedConstants, $inheritedMethods);
        // Inherit constants.
        foreach ($parentInterfaceIndex->getOwnConstants() as $constantIndex) {
          if (isset($inheritedConstants[$constantIndex->getName()])) {
            // @todo error
          }
          else {
            $inheritedConstants[$constantIndex->getName()] = $constantIndex;
          }
        }
        // Inherit methods.
        foreach ($parentInterfaceIndex->getOwnMethods() as $methodIndex) {
          if (isset($inheritedMethods[$methodIndex->getName()])) {
            // @todo check if method is compatible
          }
          else {
            $inheritedMethods[$methodIndex->getName()] = $methodIndex;
          }
        }
      }
    }
  }

  /**
   * Create index.
   *
   * @return ProjectIndex
   */
  public function index() {
    $this->errors = [];
    $deletedFiles = array_flip(array_keys($this->files));

    // Process files.
    $oldDir = getcwd();
    chdir($this->baseDir);
    foreach ($this->fileSet->scan() as $filename) {
      $this->processFile($filename);
      unset($deletedFiles[$filename]);
    }
    chdir($oldDir);

    // Handle files that no longer exist.
    foreach ($deletedFiles as $filename => $dummy) {
      $fileIndex = $this->files[$filename];
      $this->fileRemoved($fileIndex);
    }

    // Post process classes.
    foreach ($this->classes as $classFqn => $classIndex) {
      $parentFqn = $classIndex->getExtends();
      if ($parentFqn) {
        if (!isset($this->classes[$parentFqn])) {
          $this->errors[] = new Error($classIndex->getPosition(), sprintf(
            "Class %s at %s:%d extends missing class %s",
            $classIndex->getName(),
            $classIndex->getPosition()->getFilename(),
            $classIndex->getPosition()->getLineNumber(),
            $parentFqn
          ));
        }
        else {
          $this->classes[$parentFqn]->addSubclass($classFqn);
        }
      }
      foreach ($classIndex->getImplements() as $interfaceFqn) {
        if (!isset($this->interfaces[$interfaceFqn])) {
          $this->errors[] = new Error($classIndex->getPosition(), sprintf(
            "Class %s at %s:%d implements missing interface %s",
            $classIndex->getName(),
            $classIndex->getPosition()->getFilename(),
            $classIndex->getPosition()->getLineNumber(),
            $interfaceFqn
          ));
        }
        else {
          $this->interfaces[$interfaceFqn]->addImplementedBy($classFqn);
        }
      }
      foreach ($classIndex->getTraits() as $traitFqn) {
        if (!isset($this->traits[$traitFqn])) {
          $this->errors[] = new Error($classIndex->getPosition(), sprintf(
            "Class %s at %s:%d uses missing trait %s",
            $classIndex->getName(),
            $classIndex->getPosition()->getFilename(),
            $classIndex->getPosition()->getLineNumber(),
            $traitFqn
          ));
        }
        else {
          $this->traits[$traitFqn]->addUsedByClass($classFqn);
        }
      }
      $this->resolveTraits($classIndex);
      $parents = [];
      $inheritedConstants = [];
      $inheritedProperties = [];
      $inheritedMethods = [];
      while ($parentFqn) {
        $parents[] = $parentFqn;
        if (isset($this->classes[$parentFqn])) {
          /** @var ClassIndex $parentClass */
          $parentClass = $this->classes[$parentFqn];
          $parentFqn = $parentClass->getExtends();
        }
        else {
          $parentFqn = NULL;
        }
      }
      $parents = array_reverse($parents);
      $ownConstants = $classIndex->getOwnConstants();
      $ownProperties = $classIndex->getOwnProperties();
      $ownMethods = $classIndex->getOwnMethods();
      // Inherited constants/properties/methods.
      foreach ($parents as $parentFqn) {
        if (isset($this->classes[$parentFqn])) {
          $parentClass = $this->classes[$parentFqn];
          foreach ($parentClass->getOwnConstants() as $constantIndex) {
            if (!isset($ownConstants[$constantIndex->getName()])) {
              $inheritedConstants[$constantIndex->getName()] = $constantIndex;
            }
          }
          foreach ($parentClass->getOwnProperties() as $propertyIndex) {
            if (!isset($ownProperties[$propertyIndex->getName()]) && $propertyIndex->getVisibility() !== 'private') {
              $inheritedProperties[$propertyIndex->getName()] = $propertyIndex;
            }
          }
          foreach ($parentClass->getOwnMethods() as $methodIndex) {
            if (!isset($ownMethods[$methodIndex->getName()]) && $methodIndex->getVisibility() !== 'private') {
              $inheritedMethods[$methodIndex->getName()] = $methodIndex;
            }
          }
        }
      }
      // Inherit interface constants.
      foreach ($classIndex->getImplements() as $interfaceFqn) {
        if (isset($this->interfaces[$interfaceFqn])) {
          $interfaceIndex = $this->interfaces[$interfaceFqn];
          $this->inheritInterface($interfaceIndex, $inheritedConstants, $inheritedMethods);
        }
      }
      $this->classes[$classFqn]
        ->setParents($parents)
        ->setInheritedConstants($inheritedConstants)
        ->setInheritedProperties($inheritedProperties)
        ->setInheritedMethods($inheritedMethods);
    }

    // Post process traits.
    foreach ($this->traits as $traitFqn => $traitIndex) {
      foreach ($traitIndex->getTraits() as $dependentFqn) {
        if (!isset($this->traits[$dependentFqn])) {
          $this->errors[] = new Error($traitIndex->getPosition(), sprintf(
            "Trait %s at %s:%d uses missing trait %s",
            $traitIndex->getName(),
            $traitIndex->getPosition()->getFilename(),
            $traitIndex->getPosition()->getLineNumber(),
            $dependentFqn
          ));
        }
        else {
          $this->traits[$dependentFqn]->addUsedByTrait($traitFqn);
        }
      }
      $this->resolveTraits($traitIndex);
    }

    // Post process interfaces.
    foreach ($this->interfaces as $interfaceFqn => $interfaceIndex) {
      foreach ($interfaceIndex->getExtends() as $parentFqn) {
        if (!isset($this->interfaces[$parentFqn])) {
          $this->errors[] = new Error($interfaceIndex->getPosition(), sprintf(
            "Interface %s at %s:%d extends missing interface %s",
            $interfaceIndex->getName(),
            $interfaceIndex->getPosition()->getFilename(),
            $interfaceIndex->getPosition()->getLineNumber(),
            $parentFqn
          ));
        }
        else {
          $this->interfaces[$parentFqn]->addExtendedBy($interfaceFqn);
        }
      }
      $inheritedConstants = [];
      $inheritedMethods = [];
      $this->inheritInterface($interfaceIndex, $inheritedConstants, $inheritedMethods);
      $this->interfaces[$interfaceFqn]
        ->setInheritedConstants($inheritedConstants)
        ->setInheritedMethods($inheritedMethods);
    }

    // Sort errors.
    usort($this->errors, function(Error $a, Error $b) {
      $aPos = $a->getPosition();
      $bPos = $b->getPosition();
      $cmp = strnatcasecmp($aPos->getFilename(), $bPos->getFilename());
      if ($cmp !== 0) {
        return $cmp;
      }
      $cmp = $aPos->getLineNumber() - $bPos->getLineNumber();
      if ($cmp !== 0) {
        return $cmp;
      }
      $cmp = $aPos->getColumnNumber() - $bPos->getColumnNumber();
      if ($cmp !== 0) {
        return $cmp;
      }
      return strnatcasecmp($a->getMessage(), $b->getMessage());
    });

    // Get error messages.
    $errors = [];
    foreach ($this->errors as $error) {
      $errors[] = $error->getMessage();
    }

    // Create index.
    $projectIndex = new ProjectIndex(
      $this->fileSet,
      $this->files,
      $this->classes,
      $this->traits,
      $this->interfaces,
      $this->constants,
      $this->functions,
      $errors
    );

    // Save index.
    $projectIndex->save($this->baseDir);

    return $projectIndex;
  }

  private function processClassOrTrait(SingleInheritanceNode $node, SingleInheritanceIndex $index) {
    $traits = [];
    $traitPrecedences = [];
    $traitAliases = [];
    foreach ($node->getTraitUses() as $traitUse) {
      foreach ($traitUse->getTraits() as $nameNode) {
        $traits[] = $nameNode->getAbsolutePath();
      }
      foreach ($traitUse->getAdaptations() as $traitAdaptation) {
        $adaptationPosition = FilePosition::fromNode($traitAdaptation);
        if ($traitAdaptation instanceof TraitPrecedenceNode) {
          $methodReference = $traitAdaptation->getTraitMethodReference();
          $traitNames = [];
          foreach ($traitAdaptation->getTraitNames() as $nameNode) {
            $traitNames[] = $nameNode->getAbsolutePath();
          }
          $methodName = $methodReference->getMethodReference()->getText();
          if (isset($traitPrecedences[$methodName])) {
            /** @var TraitPrecedenceIndex $existingRule */
            $existingRule = $traitPrecedences[$methodName];
            $this->errors[] = new Error($traitAdaptation->getSourcePosition(), sprintf(
              "Trait precedence at %s:%d conflicts with existing rule at %s:%d",
              $traitAdaptation->getSourcePosition()->getFilename(),
              $traitAdaptation->getSourcePosition()->getLineNumber(),
              $existingRule->getPosition()->getFilename(),
              $existingRule->getPosition()->getLineNumber()
            ));
          }
          else {
            $traitPrecedences[$methodName] = new TraitPrecedenceIndex(
              $adaptationPosition,
              $methodReference->getTraitName()
                ->getAbsolutePath() . '::' . $methodName,
              $methodReference->getTraitName()->getAbsolutePath(),
              $methodName,
              $traitNames
            );
          }
        }
        elseif ($traitAdaptation instanceof TraitAliasNode) {
          $methodReference = $traitAdaptation->getTraitMethodReference();
          $visibility = $traitAdaptation->getVisibility();
          $aliasName = $traitAdaptation->getAlias()->getText();
          if (isset($traitAliases[$aliasName])) {
            /** @var TraitAliasIndex $existingAlias */
            $existingAlias = $traitAliases[$aliasName];
            $this->errors[] = new Error($traitAdaptation->getSourcePosition(), sprintf(
              "Trait alias %s at %s:%d conflicts with existing alias at %s:%d",
              $aliasName,
              $traitAdaptation->getSourcePosition()->getFilename(),
              $traitAdaptation->getSourcePosition()->getLineNumber(),
              $existingAlias->getPosition()->getFilename(),
              $existingAlias->getPosition()->getLineNumber()
            ));
          }
          else {
            $traitAliases[$aliasName] = new TraitAliasIndex(
              $adaptationPosition,
              $methodReference->getTraitName()->getAbsolutePath() . '::' . $methodReference->getMethodReference()->getText(),
              $methodReference->getTraitName()->getAbsolutePath(),
              $methodReference->getMethodReference()->getText(),
              $aliasName,
              $visibility ? $visibility->getText() : NULL
            );
          }
        }
      }
    }
    $ownProperties = [];
    foreach ($node->getProperties() as $propertyNode) {
      $visibility = $propertyNode->getVisibility();
      $propertyName = ltrim($propertyNode->getName()->getText(), '$');
      $ownProperties[$propertyName] = new PropertyIndex(
        FilePosition::fromNode($propertyNode),
        $propertyName,
        $index->getName(),
        $visibility === 'var' ? 'public' : $visibility->getText(),
        $propertyNode->getTypes()
      );
    }
    $ownMethods = [];
    foreach ($node->getMethods() as $methodNode) {
      $name = $methodNode->getName()->getText();
      $visibility = $methodNode->getVisibility();
      $ownMethods[$name] = new MethodIndex(
        FilePosition::fromNode($methodNode),
        $name,
        $index->getName(),
        $visibility ? $visibility->getText() : 'public',
        $methodNode->isFinal(),
        $methodNode->isStatic(),
        $methodNode->isAbstract(),
        $this->getParameters($methodNode),
        $methodNode->getReturnTypes()
      );
    }
    $index
      ->setTraits($traits)
      ->setTraitPrecedences($traitPrecedences)
      ->setTraitAliases($traitAliases)
      ->setOwnProperties($ownProperties)
      ->setOwnMethods($ownMethods);
  }

  /**
   * @param FunctionDeclarationNode|InterfaceMethodNode|ClassMethodNode $routineNode
   *
   * @return ParameterIndex[]
   */
  private function getParameters($routineNode) {
    $parameters = [];
    foreach ($routineNode->getParameters() as $parameterNode) {
      $typeHint = $parameterNode->getTypeHint();
      $value = $parameterNode->getValue();
      $parameters[] = new ParameterIndex(
        FilePosition::fromNode($parameterNode),
        $parameterNode->getName(),
        $parameterNode->getTypes(),
        $typeHint ? $typeHint->getText() : NULL,
        $value ? $value->getText() : NULL,
        $parameterNode->isReference(),
        $parameterNode->isVariadic()
      );
    }
    return $parameters;
  }

  public function visitClassNode(ClassNode $classNode) {
    $classFqn = $classNode->getName()->getAbsolutePath();
    $extends = $classNode->getExtends();
    $extends = $extends ? $extends->getAbsolutePath() : NULL;
    $implements = [];
    foreach ($classNode->getImplements() as $nameNode) {
      $implements[] = $nameNode->getAbsolutePath();
    }
    $constants = [];
    foreach ($classNode->getConstants() as $constantNode) {
      $constants[] = new ConstantIndex(
        FilePosition::fromNode($constantNode),
        $constantNode->getName(),
        $classFqn
      );
    }
    $classIndex = new ClassIndex(
      FilePosition::fromNode($classNode),
      $classFqn,
      $classNode->isFinal(),
      $classNode->isAbstract(),
      $extends,
      $implements,
      $constants
    );
    $this->processClassOrTrait($classNode, $classIndex);
    $this->classes[$classFqn] = $classIndex;
    $this->fileClasses[] = $classFqn;
  }

  public function visitTraitNode(TraitNode $traitNode) {
    $traitFqn = $traitNode->getName()->getAbsolutePath();
    $traitIndex = new TraitIndex(
      FilePosition::fromNode($traitNode),
      $traitFqn
    );
    $this->processClassOrTrait($traitNode, $traitIndex);
    $this->traits[$traitFqn] = $traitIndex;
    $this->fileTraits[] = $traitFqn;
  }

  public function visitInterfaceNode(InterfaceNode $interfaceNode) {
    $interfaceFqn = $interfaceNode->getName()->getAbsolutePath();
    $extends = [];
    foreach ($interfaceNode->getExtends() as $nameNode) {
      $extends[] = $nameNode->getAbsolutePath();
    }
    $constants = [];
    foreach ($interfaceNode->getConstants() as $constantNode) {
      $constants[$constantNode->getName()->getBaseName()] = new ConstantIndex(
        FilePosition::fromNode($constantNode),
        $constantNode->getName()->getBaseName(),
        $interfaceFqn
      );
    }
    $methods = [];
    foreach ($interfaceNode->getMethods() as $methodNode) {
      $parameters = $this->getParameters($methodNode);
      $methodName = $methodNode->getName()->getText();
      $methods[$methodName] = new MethodIndex(
        FilePosition::fromNode($methodNode),
        $methodName,
        $interfaceFqn,
        $methodNode->getVisibility()->getText(),
        FALSE,
        $methodNode->isStatic(),
        FALSE,
        $parameters,
        $methodNode->getReturnTypes()
      );
    }
    $this->interfaces[$interfaceFqn] = new InterfaceIndex(
      FilePosition::fromNode($interfaceNode),
      $interfaceFqn,
      $extends,
      $constants,
      $methods
    );
    $this->fileInterfaces[] = $interfaceFqn;
  }

  public function visitConstantDeclarationNode(ConstantDeclarationNode $constantDeclarationNode) {
    $constantFqn = $constantDeclarationNode->getName()->getAbsolutePath();
    $this->constants[$constantFqn] = new ConstantIndex(
      FilePosition::fromNode($constantDeclarationNode),
      $constantFqn
    );
    $this->fileConstants[] = $constantFqn;
  }

  public function visitFunctionDeclarationNode(FunctionDeclarationNode $functionDeclarationNode) {
    $functionFqn = $functionDeclarationNode->getName()->getAbsolutePath();
    $this->functions[$functionFqn] = new FunctionIndex(
      FilePosition::fromNode($functionDeclarationNode),
      $functionFqn,
      $this->getParameters($functionDeclarationNode),
      $functionDeclarationNode->getReturnTypes()
    );
    $this->fileFunctions[] = $functionFqn;
  }

}
