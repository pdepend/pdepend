# Upgrading


## From PDepend 2 to PDepend 3

Here we try to provide the needed changes to upgrade from version 2 to version 3. Most of the breaking changes only affect projects that embed PDepend as a library (custom analyzers, visitors or report generators). For plain command line use the upgrade is mostly a matter of converting `pdepend.xml` to `pdepend.yml`.

### PHP version

The minimum PHP version is changed from 5.3.7 to 8.1. PHP 8.1 through 8.5 are tested.

A 64 bit PHP build is now required (`php-64bit` in `composer.json`), because NPath complexity is computed with native integers instead of the bcmath based `MathUtil` fallback.

The supported Symfony components are 5.4, 6, 7 and 8. On Windows the `sockets` extension is required for parallel parsing.

PDepend 3 pulls in three new runtime dependencies: `symfony/yaml` for the YAML configuration, and `react/child-process` plus `fidry/cpu-core-counter` for parallel parsing.

### Configuration file

The configuration file is now YAML by default. PDepend looks for the following files in the current working directory, in order of priority, and uses the first one it finds:

- the file passed to `--configuration=<file>`
- `pdepend.yml`, `pdepend.yml.dist`
- `pdepend.php`
- `pdepend.xml`, `pdepend.xml.dist`

The loader is picked from the file extension, so an XML configuration still works, but only with Symfony 7 or older. Symfony 8 removed `XmlFileLoader`, and PDepend then fails with:

```
XML config is not supported when using Symfony 8+.
```

Convert your configuration now, even if you are not on Symfony 8 yet. While you are still on Symfony 7 or older, PDepend can do the conversion for you:

```
pdepend --migrate-configuration
```

This reads `pdepend.xml` (or `pdepend.xml.dist`, or the file given with `--configuration=<file>`) and writes `pdepend.yml` (or `pdepend.yml.dist`) next to it. It never overwrites an existing file and leaves the XML file in place, delete it once you have checked the result. Configuration files that define their own services or parameters are refused and have to be converted by hand.

If you are already on Symfony 8 you will have to convert the file by hand. What was previously:

```xml
<?xml version="1.0"?>
<symfony:container xmlns:symfony="http://symfony.com/schema/dic/services"
    xmlns="http://pdepend.org/schema/dic/pdepend">
    <config>
        <image-convert>
            <font-family>Arial</font-family>
            <font-size>12</font-size>
        </image-convert>
        <cache>
            <driver>file</driver>
            <location>/tmp/pdepend</location>
            <ttl>2592000</ttl>
        </cache>
        <parser>
            <nesting>65536</nesting>
        </parser>
    </config>
</symfony:container>
```

Is now:

```yaml
pdepend:
  image-convert:
    font-family: Arial
    font-size: 12
  cache:
    driver: file
    location: /tmp/pdepend
    ttl: 2592000
  parser:
    nesting: 65536
```

The setting names are unchanged. Two cache defaults changed, see [Cache location](#cache-location). The shipped sample file is now `pdepend.yml.dist` instead of `pdepend.xml.dist`.

To start a new configuration instead, `pdepend --generate-configuration` asks for the relevant information (with defaults) and writes a `pdepend.yml` to the current working directory. This works with every supported Symfony version.

### Parallel parsing

PDepend 3 parses files in parallel worker processes. This is enabled by default when running `pdepend` from the command line and uses every core it detects. The new `--threads` option sets the number of worker processes:

```
pdepend --threads=1 src/
```

`--threads=1` restores the single process behaviour of PDepend 2. Parallel parsing is skipped automatically when there is only one file to parse, and on Windows when the `sockets` extension is not loaded.

`--worker` is an internal option used by PDepend to start its own worker processes. Do not pass it yourself.

Parse errors and progress output from the workers are collected and reported by the parent process, so `ProcessListener` callbacks are no longer strictly ordered file by file during parsing.

Projects that construct `PDepend\Engine` themselves stay single process until they call `Engine::setMainScript()` with the script the worker processes should re-execute. `Engine::setThreads()` and `Engine::setWorkerCommandName()` are optional on top of that. Without `setThreads()` the detected core count is used.

### Cache location

The parser cache moved from the home directory to the XDG cache directory:

|         | PDepend 2                                                          | PDepend 3                                          |
|---------|--------------------------------------------------------------------|----------------------------------------------------|
| Default | `~/.pdepend`                                                       | `$XDG_CACHE_HOME/pdepend`, else `~/.cache/pdepend` |
| Driver  | `file`, or `memory` on PHP builds with the serialize reference bug | always `file`                                      |

The old `~/.pdepend` directory is no longer used and can be deleted. The first run after upgrading re-parses everything.

`PDepend\Util\FileUtil` changed accordingly:

| PDepend 2                                | PDepend 3                        |
|------------------------------------------|----------------------------------|
| `FileUtil::getUserHomeDir()`             | `FileUtil::getUserCacheDir()`    |
| `FileUtil::getUserHomeDirOrSysTempDir()` | `FileUtil::getDefaultCacheDir()` |

### Removed command line options

- `--optimization` has been removed. It printed "Option --optimization is ambiguous." and did nothing since PDepend 2.0.
- The workaround banner ("Your PHP version requires some workaround") is gone together with `PDepend\Util\Workarounds`. None of the workarounds applied to PHP 8.1 or newer.

### Internal API changes

These changes only affect you if you have written custom analyzers, visitors or report generators, or extended PDepend classes. Class and interface names are unchanged. What changed is their signatures.

#### Native types on every interface

Every method in the public API now declares native parameter and return types. Any class implementing a PDepend interface or extending a PDepend base class must be updated to match, or PHP will refuse to load it.

The interfaces affected include `PDepend\Metrics\Analyzer`, `PDepend\Report\ReportGenerator`, `PDepend\Source\ASTVisitor\ASTVisitor`, `PDepend\Source\AST\ASTNode`, `PDepend\Source\AST\ASTArtifact`, `PDepend\Source\Tokenizer\Tokenizer`, `PDepend\Source\Builder\Builder`, `PDepend\ProcessListener` and `PDepend\Input\Filter`.

- `ASTArtifact` now extends `ASTNode`. In PDepend 2 the `extends` was commented out and both interfaces declared overlapping methods.
- `Analyzer::analyze()` takes an `ASTArtifactList` instead of an untyped `$namespaces`, and returns `void`.
- `Engine::getExceptions()` returns `Throwable[]` instead of `ParserException[]`, because the parser now catches every `Throwable` raised while parsing a file. Code that type hinted `ParserException` when iterating the result needs to relax that hint.

#### `getName()` is replaced by `getImage()`

The long deprecated `getName()` has been removed from artifacts. Every artifact (classes, interfaces, traits, enums, methods, functions, namespaces, parameters, properties and compilation units) exposes its name through `getImage()`, which is what `ASTNode` has always used.

```php
// PDepend 2
$class->getName();

// PDepend 3
$class->getImage();
```

`setName()` is still available on `AbstractASTArtifact`. `ASTNamedArgument::getName()` is unrelated and still exists.

#### Visitor pattern

In PDepend 2 you called `accept()` on a node and passed it a visitor, and `AbstractASTVisitor::__call()` forwarded unknown `visitXxx()` calls to `visit($node, $data)`. Both are gone.

- `ASTNode::accept()` and `ASTArtifact::accept()` have been removed.
- `ASTVisitor::__call()` has been removed.
- `ASTVisitor::visit($node, $data)` has been replaced by `visit(ASTNode $node): void`, which now means "descend into this node's children".
- `ASTVisitor::dispatch(ASTNode $node): void` is new. It routes a node to the matching `visitClass()`, `visitMethod()`, etc. method.

```php
// PDepend 2
$namespace->accept($visitor);

// PDepend 3
$visitor->dispatch($namespace);
```

`dispatch()` matches on the node's exact class, so nodes that are not one of the ten artifact types (including subclasses such as `ASTAnonymousClass`) fall through to `visit()` and have their children dispatched instead. If you rely on a custom node subclass being routed to a dedicated method, override `dispatch()`.

All `visitXxx()` methods now return `void`. Visitors that accumulated a return value through the visit chain need to collect into the visitor instead.

#### `ProcessListener`

`PDepend\ProcessListener` no longer receives the builder and tokenizer, and now reports the total number of files up front:

```php
// PDepend 2
public function startParseProcess(Builder $builder);
public function endParseProcess(Builder $builder);
public function startFileParsing(Tokenizer $tokenizer);
public function endFileParsing(Tokenizer $tokenizer);

// PDepend 3
public function startParseProcess(int $fileCount): void;
public function endParseProcess(): void;
public function startFileParsing(): void;
public function endFileParsing(): void;
```

Listeners that used `$tokenizer->getSourceFile()` to report the file currently being parsed no longer can, because with parallel parsing the parent process does not own a tokenizer. Use `$fileCount` from `startParseProcess()` to drive a progress indicator instead.

#### Parser

`AbstractPHPParser` went from 130 `protected` methods to 28. The rest are now `private`. The per version parser classes for PHP 5.3 through 8.1 have been removed and their behaviour folded into `AbstractPHPParser` (feature level 8.0) and `PHPParserVersion82`:

| PDepend 2                                     | PDepend 3                                     |
|-----------------------------------------------|-----------------------------------------------|
| `PHPParserVersion53` to `PHPParserVersion81`  | removed                                       |
| `PHPParserVersion82` to `PHPParserVersion85`  | `PHPParserVersion85` is the newest            |
| `PHPParserGeneric extends PHPParserVersion83` | `PHPParserGeneric extends PHPParserVersion85` |

`throwUnexpectedTokenException()` has been removed. Use `throw $this->getUnexpectedTokenException($token)` instead.

If you subclassed the parser to hook into a specific `parseXxx()` method, check whether the method still exists and is still `protected` before upgrading.

#### Metric value types

`getNodeMetrics()` and `getProjectMetrics()` now return properly typed values. The one that changes shape is NPath complexity, which was an arbitrary precision decimal string from bcmath and is now a native integer:

```php
// PDepend 2
$metrics['npath'] === '17'

// PDepend 3
$metrics['npath'] === 17
```

Strict comparisons against strings will fail. Values beyond `PHP_INT_MAX` are no longer represented exactly, which is why a 64 bit build is now required.

#### Removed classes and methods

- `PDepend\Util\MathUtil` has been removed. Use native integer arithmetic instead.
- `PDepend\Util\Workarounds` has been removed. It is no longer needed on PHP 8.1 or newer.
- `PDepend\Metrics\AnalyzerIterator` has been removed. Iterate the analyzer list directly instead.
- `PDepend\Source\AST\ASTStringIndexExpression` and `Builder::buildAstStringIndexExpression()` have been removed. The `$string{0}` syntax was removed in PHP 8.0.
- `PDepend\Source\Language\PHP\PHPParserVersion53` to `PHPParserVersion81` have been removed. Use `AbstractPHPParser` or `PHPParserVersion82` instead.
- `Lazy\PDepend\DependencyInjection\Configuration.strong.php` and `Configuration.weak.php` have been removed. Use `PDepend\DependencyInjection\Configuration` instead.
- `Engine::TOKEN_STORAGE` and `Engine::PARSER_STORAGE` have been removed. They were unused.
- `ASTCompilationUnit::free()` and `AbstractASTCallable::free()` have been removed. They were unused.
- `AbstractASTArtifact::getDocComment()` and `setDocComment()` have been removed. Use `getComment()` and `setComment()` instead.
- `AbstractASTArtifact::getName()` and friends have been removed. Use `getImage()` instead.
- `AbstractPHPParser::throwUnexpectedTokenException()` has been removed. Use `getUnexpectedTokenException()` instead.

### Package layout

The repository moved to the conventional layout. This only matters if you reference files inside the package by path. The PSR-4 namespace root is declared in `composer.json` and class names did not change.

| PDepend 2               | PDepend 3            |
|-------------------------|----------------------|
| `src/main/php/PDepend/` | `src/`               |
| `src/main/resources/`   | `resources/`         |
| `src/bin/pdepend`       | `bin/pdepend`        |
| `src/test/php/PDepend/` | `tests/php/PDepend/` |
| `src/conf/`             | `conf/`              |
| `src/site/`             | `site/`              |

`vendor/bin/pdepend` is unaffected. The DI service definitions moved from `resources/services.xml` to `resources/services.php`, and the XSD schema for the XML configuration (`src/main/resources/schema/configuration.xsd`) has been dropped.
