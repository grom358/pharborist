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
   * Processed traits.
   *
   * @var string[]
   */
  private $processedTraits;

  /**
   * Processed interfaces.
   *
   * @var string[]
   */
  private $processedInterfaces;

  /**
   * Processed classes.
   *
   * @var string[]
   */
  private $processedClasses;

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

      try {
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
      }
      catch (\Exception $e) {
        $position = new FilePosition($filename, 1, 1, 0, 0);
        $this->errors[] = new Error($position, $e->getMessage());
      }

      $this->fileClasses = [];
      $this->fileTraits = [];
      $this->fileInterfaces = [];
      $this->fileConstants = [];
      $this->fileFunctions = [];
    }
  }

  /**
   * Resolve trait uses.
   *
   * @param ClassIndex|TraitIndex $index
   *   Class or trait.
   *
   * @return array
   *   Properties and methods from traits.
   */
  private function resolveTraitUses($index) {
    $traitProperties = [];
    $traitMethods = [];
    $traitPrecedences = $index->getTraitPrecedences();
    $traitAliases = $index->getTraitAliases();

    // Filter to traits that exist.
    /** @var TraitIndex[] $traits */
    $traits = [];
    foreach ($index->getTraits() as $traitFqn) {
      if (isset($this->traits[$traitFqn])) {
        $traits[$traitFqn] = $this->traits[$traitFqn];
      }
    }

    // Process properties.
    foreach ($traits as $traitFqn => $traitIndex) {
      foreach ($traitIndex->getProperties() as $propertyName => $propertyIndex) {
        if (isset($traitProperties[$propertyName])) {
          /** @var PropertyIndex $existingPropertyIndex */
          $existingPropertyIndex = $traitProperties[$propertyName];
          if ($existingPropertyIndex->compatibleWith($propertyIndex)) {
            $this->errors[] = new Error($index->getPosition(), sprintf(
              "Trait property %s::\$%s defines the same property %s::\$%s at %s:%d",
              $traitFqn,
              $propertyName,
              $existingPropertyIndex->getOwner(),
              $existingPropertyIndex->getName(),
              $index->getPosition()->getFilename(),
              $index->getPosition()->getLineNumber()
            ));
          }
          else {
            $this->errors[] = new Error($index->getPosition(), sprintf(
              "Trait property %s::\$%s conflicts with existing property %s::\$%s at %s:%d",
              $traitFqn,
              $propertyName,
              $existingPropertyIndex->getOwner(),
              $existingPropertyIndex->getName(),
              $index->getPosition()->getFilename(),
              $index->getPosition()->getLineNumber()
            ));
          }
        }
        else {
          $traitProperties[$propertyName] = $propertyIndex;
        }
      }
    }

    // Apply trait precedence rules.
    $resolvedConflict = [];
    foreach ($traitPrecedences as $traitPrecedenceIndex) {
      $methodName = $traitPrecedenceIndex->getMethodName();
      $ownerTraitFqn = $traitPrecedenceIndex->getOwnerTrait();
      if (!isset($traits[$ownerTraitFqn])) {
        $this->errors[] = new Error($traitPrecedenceIndex->getPosition(), sprintf(
          "Required trait %s wasn't added to %s %s at %s:%d",
          $ownerTraitFqn,
          $index instanceof ClassIndex ? 'class' : 'trait',
          $index->getName(),
          $traitPrecedenceIndex->getPosition()->getFilename(),
          $traitPrecedenceIndex->getPosition()->getLineNumber()
        ));
      }
      else {
        $trait = $traits[$ownerTraitFqn];
        if (!$trait->hasMethod($methodName)) {
          $this->errors[] = new Error($traitPrecedenceIndex->getPosition(), sprintf(
            "A precedence rule was defined for %s::%s but this method does not exist at %s:%d",
            $ownerTraitFqn,
            $methodName,
            $traitPrecedenceIndex->getPosition()->getFilename(),
            $traitPrecedenceIndex->getPosition()->getLineNumber()
          ));
        }
        else {
          $resolvedConflict[$ownerTraitFqn][$methodName] = $ownerTraitFqn;
          foreach ($traitPrecedenceIndex->getTraits() as $traitFqn) {
            if (!isset($traits[$traitFqn])) {
              $this->errors[] = new Error($traitPrecedenceIndex->getPosition(), sprintf(
                "Required trait %s wasn't added to %s %s at %s:%d",
                $traitFqn,
                $index instanceof ClassIndex ? 'class' : 'trait',
                $index->getName(),
                $traitPrecedenceIndex->getPosition()->getFilename(),
                $traitPrecedenceIndex->getPosition()->getLineNumber()
              ));
            }
            $trait = $this->traits[$traitFqn];
            if (!$trait->hasMethod($methodName)) {
              $this->errors[] = new Error($traitPrecedenceIndex->getPosition(), sprintf(
                "A precedence rule was defined for %s::%s but this method does not exist at %s:%d",
                $traitFqn,
                $methodName,
                $traitPrecedenceIndex->getPosition()->getFilename(),
                $traitPrecedenceIndex->getPosition()->getLineNumber()
              ));
            }
            else {
              $resolvedConflict[$traitFqn][$methodName] = $ownerTraitFqn;
            }
          }
          $traitMethods[$methodName] = $trait->getMethod($methodName);
        }
      }
    }

    // Validate trait aliases.
    $aliases = [];
    foreach ($traitAliases as $traitAliasIndex) {
      $methodName = $traitAliasIndex->getMethodName();
      $traitFqn = $traitAliasIndex->getOwnerTrait();
      if (!isset($traits[$traitFqn])) {
        $this->errors[] = new Error($traitAliasIndex->getPosition(), sprintf(
          "Required trait %s wasn't added to %s %s at %s:%d",
          $traitFqn,
          $index instanceof ClassIndex ? 'class' : 'trait',
          $index->getName(),
          $traitAliasIndex->getPosition()->getFilename(),
          $traitAliasIndex->getPosition()->getLineNumber()
        ));
      }
      else {
        $trait = $traits[$traitFqn];
        if (!$trait->hasMethod($methodName)) {
          $this->errors[] = new Error($traitAliasIndex->getPosition(), sprintf(
            "An alias was defined for %s::%s but this method does not exist at %s:%d",
            $traitFqn,
            $methodName,
            $traitAliasIndex->getPosition()->getFilename(),
            $traitAliasIndex->getPosition()->getLineNumber()
          ));
        }
        else {
          $aliases[$traitFqn][$methodName] = $traitAliasIndex;
        }
      }
    }

    // Import trait methods.
    foreach ($traits as $traitFqn => $traitIndex) {
      foreach ($traitIndex->getMethods() as $methodName => $methodIndex) {
        // Apply alias if it exists.
        if (isset($aliases[$traitFqn][$methodName])) {
          /** @var TraitAliasIndex $traitAliasIndex */
          $traitAliasIndex = $aliases[$traitFqn][$methodName];
          $ownerTraitIndex = $traits[$traitAliasIndex->getOwnerTrait()];
          $methodIndex = $ownerTraitIndex->getMethod($methodName);
          $aliasedMethodIndex = new MethodIndex(
            $methodIndex->getPosition(),
            $traitAliasIndex->getAliasName(),
            $methodIndex->getOwner(),
            $traitAliasIndex->getAliasVisibility() ?: $methodIndex->getVisibility(),
            $methodIndex->isFinal(),
            $methodIndex->isStatic(),
            $methodIndex->isAbstract(),
            $methodIndex->getParameters(),
            $methodIndex->getReturnTypes()
          );
          $traitMethods[$traitAliasIndex->getAliasName()] = $aliasedMethodIndex;
        }
        // Add method if not hidden by precedence rule.
        if (!isset($resolvedConflict[$traitFqn][$methodName])) {
          if (isset($traitMethods[$methodName])) {
            /** @var MethodIndex $conflictMethodIndex */
            $conflictMethodIndex = $traitMethods[$methodName];
            $this->errors[] = new Error($index->getPosition(), sprintf(
              'Trait method %s::%s has not been applied, because it has collisions with %s::%s at %s:%d',
              $traitFqn,
              $methodName,
              $conflictMethodIndex->getOwner(),
              $methodName,
              $index->getPosition()->getFilename(),
              $index->getPosition()->getLineNumber()
            ));
          }
          else {
            $traitMethods[$methodName] = $methodIndex;
          }
        }
      }
    }

    $index
      ->setTraitProperties($traitProperties)
      ->setTraitMethods($traitMethods);
  }

  /**
   * Process trait.
   *
   * @param string $traitFqn
   *   Fully qualified trait name.
   * @param TraitIndex $traitIndex
   *   Indexed trait.
   */
  protected function processTrait($traitFqn, TraitIndex $traitIndex) {
    if (isset($this->processedTraits[$traitFqn])) {
      return;
    }
    foreach ($traitIndex->getTraits() as $dependentFqn) {
      if (!isset($this->traits[$dependentFqn])) {
        $this->errors[] = new Error($traitIndex->getPosition(), sprintf(
          "Trait %s uses missing trait %s at %s:%d",
          $traitIndex->getName(),
          $dependentFqn,
          $traitIndex->getPosition()->getFilename(),
          $traitIndex->getPosition()->getLineNumber()
        ));
      }
      else {
        $dependentTrait = $this->traits[$dependentFqn];
        $dependentTrait->addUsedByTrait($traitFqn);
        $this->processTrait($dependentFqn, $dependentTrait);
      }
    }
    $this->resolveTraitUses($traitIndex);
    $this->processedTraits[$traitFqn] = $traitFqn;
  }

  /**
   * Process interface.
   *
   * @param string $interfaceFqn
   *   Fully qualified interface name.
   * @param InterfaceIndex $interfaceIndex
   *   Indexed interface.
   */
  protected function processInterface($interfaceFqn, InterfaceIndex $interfaceIndex) {
    if (isset($this->processedInterfaces[$interfaceFqn])) {
      return;
    }
    foreach ($interfaceIndex->getExtends() as $parentFqn) {
      if (!isset($this->interfaces[$parentFqn])) {
        $this->errors[] = new Error($interfaceIndex->getPosition(), sprintf(
          "Interface %s extends missing interface %s at %s:%d",
          $interfaceIndex->getName(),
          $parentFqn,
          $interfaceIndex->getPosition()->getFilename(),
          $interfaceIndex->getPosition()->getLineNumber()
        ));
      }
      else {
        $parentInterfaceIndex = $this->interfaces[$parentFqn];
        $parentInterfaceIndex->addExtendedBy($interfaceFqn);
        $this->processInterface($parentFqn, $parentInterfaceIndex);
      }
    }
    $ownConstants = $interfaceIndex->getOwnConstants();
    $ownMethods = $interfaceIndex->getOwnMethods();
    $inheritedConstants = [];
    $inheritedMethods = [];
    foreach ($interfaceIndex->getExtends() as $parentFqn) {
      if (isset($this->interfaces[$parentFqn])) {
        $parentInterfaceIndex = $this->interfaces[$parentFqn];
        foreach ($parentInterfaceIndex->getConstants() as $constantName => $constantIndex) {
          if (isset($ownConstants[$constantName])) {
            $this->errors[] = new Error($interfaceIndex->getPosition(), sprintf(
              "Cannot inherit previously-inherited or override constant %s from interface %s at %s:%d",
              $constantName,
              $parentInterfaceIndex->getName(),
              $interfaceIndex->getPosition()->getFilename(),
              $interfaceIndex->getPosition()->getLineNumber(),
              $parentFqn
            ));
          }
          else {
            $inheritedConstants[$constantName] = $constantIndex;
          }
        }
        foreach ($parentInterfaceIndex->getMethods() as $methodName => $methodIndex) {
          if (isset($ownMethods[$methodName])) {
            $existingMethodIndex = $ownMethods[$methodName];
          } elseif (isset($inheritedMethods[$methodName])) {
            $existingMethodIndex = $inheritedMethods[$methodName];
          } else {
            $existingMethodIndex = NULL;
          }
          if ($existingMethodIndex) {
            if (!$existingMethodIndex->compatibleWith($methodIndex)) {
              $this->errors[] = new Error($interfaceIndex->getPosition(), sprintf(
                "Declaration of %s::%s() must be compatible with %s::%s() at %s:%d",
                $methodIndex->getOwner(),
                $methodName,
                $existingMethodIndex->getOwner(),
                $methodName,
                $interfaceIndex->getPosition()->getFilename(),
                $interfaceIndex->getPosition()->getLineNumber(),
                $parentFqn
              ));
            }
          }
          else {
            $inheritedMethods[$methodName] = $methodIndex;
          }
        }
      }
    }
    $this->interfaces[$interfaceFqn]
      ->setInheritedConstants($inheritedConstants)
      ->setInheritedMethods($inheritedMethods);
    $this->processedInterfaces[$interfaceFqn] = $interfaceFqn;
  }

  /**
   * Process class.
   *
   * @param string $classFqn
   *   Fully qualified class name.
   * @param ClassIndex $classIndex
   *   Indexed class.
   */
  protected function processClass($classFqn, ClassIndex $classIndex) {
    if (isset($this->processedClasses[$classFqn])) {
      return;
    }
    $parentFqn = $classIndex->getExtends();
    if ($parentFqn) {
      if (!isset($this->classes[$parentFqn])) {
        $this->errors[] = new Error($classIndex->getPosition(), sprintf(
          "Class %s extends missing class %s at %s:%d",
          $classIndex->getName(),
          $parentFqn,
          $classIndex->getPosition()->getFilename(),
          $classIndex->getPosition()->getLineNumber()
        ));
      }
      else {
        $parentClassIndex = $this->classes[$parentFqn];
        $parentClassIndex->addSubclass($classFqn);
        $this->processClass($parentFqn, $parentClassIndex);
      }
    }
    foreach ($classIndex->getImplements() as $interfaceFqn) {
      if (!isset($this->interfaces[$interfaceFqn])) {
        $this->errors[] = new Error($classIndex->getPosition(), sprintf(
          "Class %s implements missing interface %s at %s:%d",
          $classIndex->getName(),
          $interfaceFqn,
          $classIndex->getPosition()->getFilename(),
          $classIndex->getPosition()->getLineNumber()
        ));
      }
      else {
        $this->interfaces[$interfaceFqn]->addImplementedBy($classFqn);
      }
    }
    foreach ($classIndex->getTraits() as $traitFqn) {
      if (!isset($this->traits[$traitFqn])) {
        $this->errors[] = new Error($classIndex->getPosition(), sprintf(
          "Class %s uses missing trait %s at %s:%d",
          $classIndex->getName(),
          $traitFqn,
          $classIndex->getPosition()->getFilename(),
          $classIndex->getPosition()->getLineNumber()
        ));
      }
      else {
        $this->traits[$traitFqn]->addUsedByClass($classFqn);
      }
    }

    $this->resolveTraitUses($classIndex);

    $ownConstants = $classIndex->getOwnConstants();
    $ownProperties = $classIndex->getOwnProperties();
    $ownMethods = $classIndex->getOwnMethods();
    $traitProperties = $classIndex->getTraitProperties();
    $traitMethods = $classIndex->getTraitMethods();
    $inheritedConstants = [];
    $inheritedProperties = [];
    $inheritedMethods = [];
    if ($parentFqn && isset($this->classes[$parentFqn])) {
      $parentClassIndex = $this->classes[$parentFqn];
      foreach ($parentClassIndex->getConstants() as $constantName => $constantIndex) {
        if (!isset($ownConstants[$constantName])) {
          $inheritedConstants[$constantName] = $constantIndex;
        }
      }
      foreach ($parentClassIndex->getProperties() as $propertyName => $propertyIndex) {
        if (!isset($ownProperties[$propertyName]) && !isset($traitProperties[$propertyName]) && $propertyIndex->getVisibility() !== 'private') {
          $inheritedProperties[$propertyName] = $propertyIndex;
        }
      }
      foreach ($parentClassIndex->getMethods() as $methodName => $methodIndex) {
        if (!isset($ownMethods[$methodName]) && !isset($traitMethods[$methodName]) && $methodIndex->getVisibility() !== 'private') {
          $inheritedMethods[$methodName] = $methodIndex;
        }
      }
    }
    /** @var MethodIndex[] $methods */
    $methods = array_merge($inheritedMethods, $traitMethods, $ownMethods);
    if (!$classIndex->isAbstract()) {
      // Check no abstract methods in non-abstract class.
      foreach ($methods as $methodIndex) {
        if ($methodIndex->isAbstract()) {
          $this->errors[] = new Error($classIndex->getPosition(), sprintf(
            "Class %s does not implement method %s::%s() at %s:%d",
            $classFqn,
            $methodIndex->getOwner(),
            $methodIndex->getName(),
            $classIndex->getPosition()->getFilename(),
            $classIndex->getPosition()->getLineNumber(),
            $parentFqn
          ));
        }
      }
    }
    foreach ($classIndex->getImplements() as $interfaceFqn) {
      if (isset($this->interfaces[$interfaceFqn])) {
        $interfaceIndex = $this->interfaces[$interfaceFqn];
        // Inherit constants from interfaces.
        foreach ($interfaceIndex->getConstants() as $constantName => $constantIndex) {
          if (isset($ownConstants[$constantName]) || isset($inheritedConstants[$constantName])) {
            $this->errors[] = new Error($classIndex->getPosition(), sprintf(
              "Cannot inherit previously-inherited or override constant %s from interface %s at %s:%d",
              $constantName,
              $interfaceIndex->getName(),
              $classIndex->getPosition()->getFilename(),
              $classIndex->getPosition()->getLineNumber(),
              $parentFqn
            ));
          }
          else {
            $inheritedConstants[$constantName] = $constantIndex;
          }
        }
        // Check class implements interface correctly.
        foreach ($interfaceIndex->getMethods() as $methodName => $methodIndex) {
          if (!isset($methods[$methodName])) {
            $this->errors[] = new Error($classIndex->getPosition(), sprintf(
              "Class %s does not implement method %s::%s() at %s:%d",
              $classFqn,
              $interfaceFqn,
              $methodName,
              $classIndex->getPosition()->getFilename(),
              $classIndex->getPosition()->getLineNumber()
            ));
          }
          else {
            /** @var MethodIndex $classMethodIndex */
            $classMethodIndex = $methods[$methodName];
            if (!$classMethodIndex->compatibleWith($methodIndex)) {
              $this->errors[] = new Error($classMethodIndex->getPosition(), sprintf(
                "Declaration of %s::%s() must be compatible with %s::%s() at %s:%d",
                $classFqn,
                $methodName,
                $interfaceFqn,
                $methodName,
                $classMethodIndex->getPosition()->getFilename(),
                $classMethodIndex->getPosition()->getLineNumber()
              ));
            }
          }
        }
      }
    }

    $parents = [];
    while ($parentFqn) {
      $parents[] = $parentFqn;
      if (isset($this->classes[$parentFqn])) {
        $parentClassIndex = $this->classes[$parentFqn];
        $parentFqn = $parentClassIndex->getExtends();
      }
      else {
        $parentFqn = NULL;
      }
    }
    $parents = array_reverse($parents);

    $this->classes[$classFqn]
      ->setParents($parents)
      ->setInheritedConstants($inheritedConstants)
      ->setInheritedProperties($inheritedProperties)
      ->setInheritedMethods($inheritedMethods);
    $this->processedClasses[$classFqn] = $classFqn;
  }

  /**
   * Create index.
   *
   * Attempts to load and update existing index in base directory, otherwise
   * creates new index with file set.
   *
   * @param string $baseDir
   *   Base directory for project.
   * @param FileSet $fileSet
   *   (Optional) Set of files to be indexed. Parameter is required if no
   *   existing index is found.
   * @param bool $forceRebuild
   *   (Optional) Force complete index rebuild.
   *
   * @return ProjectIndex
   */
  public function index($baseDir, $fileSet = NULL, $forceRebuild = FALSE) {
    $this->baseDir = $baseDir;
    if (!$forceRebuild && ($projectIndex = ProjectIndex::load($baseDir))) {
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
    }
    else {
      if (!$fileSet instanceof FileSet) {
        throw new \InvalidArgumentException('Must provide file set since no existing index found.');
      }
      $this->fileSet = $fileSet;
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
    $this->errors = [];
    $this->processedTraits = [];
    $this->processedInterfaces = [];
    $this->processedClasses = [];
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

    // Post process traits.
    foreach ($this->traits as $traitFqn => $traitIndex) {
      $this->processTrait($traitFqn, $traitIndex);
    }

    // Post process interfaces.
    foreach ($this->interfaces as $interfaceFqn => $interfaceIndex) {
      $this->processInterface($interfaceFqn, $interfaceIndex);
    }

    // Post process classes.
    foreach ($this->classes as $classFqn => $classIndex) {
      $this->processClass($classFqn, $classIndex);
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
      return $a->getErrorNo() - $b->getErrorNo();
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
        $propertyNode->isStatic(),
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
      $constantName = $constantNode->getName()->getBaseName();
      $constants[$constantName] = new ConstantIndex(
        FilePosition::fromNode($constantNode),
        $constantName,
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
