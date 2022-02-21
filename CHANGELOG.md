pdepend-2.10.3 (2022/02/23)
==========================

- Added [\#574](https://github.com/pdepend/pdepend/pull/574): Support symfony 6
- Fixed [\#583](https://github.com/pdepend/pdepend/pull/583), [\#578](https://github.com/pdepend/pdepend/pull/578), [\#577](https://github.com/pdepend/pdepend/pull/577), [\#576](https://github.com/pdepend/pdepend/pull/576) : Add declare ReturnTypeWillChange to fix deprecation warnings.
- Fixed [\#575](https://github.com/pdepend/pdepend/pull/575): Fix deprecation warning in AbstractPHPParser::parseVarAnnotation.
- Fixed [\#579](https://github.com/pdepend/pdepend/pull/579): Fix deprecation messages in AbstractPHPParser::prepareCallable
- Changed [\#589](https://github.com/pdepend/pdepend/pull/589), [\#588](https://github.com/pdepend/pdepend/pull/588), [\#587](https://github.com/pdepend/pdepend/pull/587), [\#586](https://github.com/pdepend/pdepend/pull/586), [\#585](https://github.com/pdepend/pdepend/pull/585), [\#582](https://github.com/pdepend/pdepend/pull/582), [\#581](https://github.com/pdepend/pdepend/pull/581), [\#580](https://github.com/pdepend/pdepend/pull/580) : Internal code cleanup and PHPStan fixes.

pdepend-2.10.2 (2021/11/16)
==========================

- Added [\#568](https://github.com/pdepend/pdepend/pull/568): Support PHP 8.1 final constants.
- Fixed [\#572](https://github.com/pdepend/pdepend/pull/572): Allow "readonly" as part of a class name.
- Fixed [\#567](https://github.com/pdepend/pdepend/pull/567): Allow const, field, method named readonly.

pdepend-2.10.1 (2021/10/11)
==========================

- Added [\#563](https://github.com/pdepend/pdepend/pull/563): Support PHP 8.1 `never` return type.
- Added [\#565](https://github.com/pdepend/pdepend/pull/565): Support PHP 8.1 readonly properties.
- Added [\#561](https://github.com/pdepend/pdepend/pull/561): Support trailing comma in `isset()` and `unset()` lists.
- Fixed [\#562](https://github.com/pdepend/pdepend/pull/562): Allow any keyword as the name of an argument.
- Fixed [\#555](https://github.com/pdepend/pdepend/pull/555): Restore modifier after serialization.

pdepend-2.10.0 (2021/07/20)
==========================

- Added [\#534](https://github.com/pdepend/pdepend/pull/534): Support trailing comma in parameters list.
- Added [\#535](https://github.com/pdepend/pdepend/pull/535): Support trailing comma in closure list.
- Added [\#538](https://github.com/pdepend/pdepend/pull/538): Add named argument value as child to make it discoverable.
- Added [\#540](https://github.com/pdepend/pdepend/pull/540): Support throw expression.
- Added [\#543](https://github.com/pdepend/pdepend/pull/543): Support multiple expressions as match key.
- Fixed [\#530](https://github.com/pdepend/pdepend/pull/530): Automate release generation for the website.
- Fixed [\#537](https://github.com/pdepend/pdepend/pull/537): Match expression does not have namespace
- Fixed [\#541](https://github.com/pdepend/pdepend/pull/541): Fix support for union types
- Fixed [\#544](https://github.com/pdepend/pdepend/pull/544): Math throw entry may end on curly braces
- Fixed [\#547](https://github.com/pdepend/pdepend/pull/547): Add the phar to the website. The new URL is: https://pdepend.org/static/latest/pdepend.phar
- Fixed [\#550](https://github.com/pdepend/pdepend/pull/550): Allow multiple arguments in invocation
- Fixed [\#551](https://github.com/pdepend/pdepend/pull/551): Allow null and false in union type for typed properties
- Fixed [\#552](https://github.com/pdepend/pdepend/pull/552): Allow null-safe operator anywhere object operator is allowed

pdepend-2.9.1 (2021/04/15)
==========================

- Added [\#519](https://github.com/pdepend/pdepend/issues/519): Support PHP 7.2 trailing commas in grouped use declarations
- Added [\#518](https://github.com/pdepend/pdepend/issues/518): Support PHP 8 `static` type hint
- Added [\#522](https://github.com/pdepend/pdepend/issues/522): Support PHP 8 `null` and `false` type hint
- Added [\#516](https://github.com/pdepend/pdepend/issues/516): Support PHP 8 catch without variable
- Fixed [\#527](https://github.com/pdepend/pdepend/issues/527): Allow nested arrays in PHP 8 attributes

pdepend-2.9.0 (2021/03/11)
==========================

- Added [\#496](https://github.com/pdepend/pdepend/issues/496) Support PHP 8 Match expression
- Added [\#494](https://github.com/pdepend/pdepend/issues/494) Support PHP 8 Constructor property promotion
- Added [\#492](https://github.com/pdepend/pdepend/issues/492) Support PHP 8 Named arguments
- Added [\#493](https://github.com/pdepend/pdepend/issues/493) Support PHP 8 Attributes
- Added [\#495](https://github.com/pdepend/pdepend/issues/495) Support PHP 8 Union types
- Added [\#497](https://github.com/pdepend/pdepend/issues/497) Support PHP 8 Nullsafe operator
- Added [\#491](https://github.com/pdepend/pdepend/issues/491) Support PHP 8 tokens changes
- Fixed [\#490](https://github.com/pdepend/pdepend/pull/490) Preserve short open tags
- Fixed [\#480](https://github.com/pdepend/pdepend/pull/480), [\#486](https://github.com/pdepend/pdepend/pull/486) and [\#513](https://github.com/pdepend/pdepend/pull/513) PHPDoc blocks
- Changed [\#500](https://github.com/pdepend/pdepend/issues/500) Switch to GitHub Actions
- Changed [\#511](https://github.com/pdepend/pdepend/pull/511) Run PHPStan in GitHub actions
- Changed [\#489](https://github.com/pdepend/pdepend/pull/489) Add unit tests for Application class
- Removed [\#488](https://github.com/pdepend/pdepend/pull/488) Removed changes.xml legacy file

pdepend-2.8.0 (2020/05/25)
==========================

- Added [\#458](https://github.com/pdepend/pdepend/pull/458) Support trait insteadof overrides. (Issue [\#367](https://github.com/pdepend/pdepend/issues/367) and [\#320](https://github.com/pdepend/pdepend/issues/320) )
- Added [\#466](https://github.com/pdepend/pdepend/pull/466) Allow to configure file cache ttl in configuration file. (Issue [\#465](https://github.com/pdepend/pdepend/issues/465))
- Added [\#487](https://github.com/pdepend/pdepend/pull/484) Support arrow functions return type hints.
- Fixed [\#467](https://github.com/pdepend/pdepend/pull/467) Use the HOME env variable also for Windows. (Issue [\#447](https://github.com/pdepend/pdepend/issue/447) )
- Fixed [\#475](https://github.com/pdepend/pdepend/pull/475) Fixed [\#474](https://github.com/pdepend/pdepend/issues/474) typed property visibility 
- Fixed [\#476](https://github.com/pdepend/pdepend/pull/476) Fixed [\#473](https://github.com/pdepend/pdepend/issues/473) handle skipped variable in destructuring array
- Fixed [\#478](https://github.com/pdepend/pdepend/pull/478) Fixed [\#472](https://github.com/pdepend/pdepend/issues/472) Use the current token instead of the first inner one to determine the class start.
- Fixed [\#483](https://github.com/pdepend/pdepend/pull/483) Fixed bool flip in parseEscapedAstLiteralString()
- Fixed [\#479](https://github.com/pdepend/pdepend/pull/479) Fixed [\#299](https://github.com/pdepend/pdepend/issues/299) Class constant expression declaration.
- Changed: Tidelift language ( [\#460](https://github.com/pdepend/pdepend/pull/460), [\#461](https://github.com/pdepend/pdepend/pull/461) and [\#462](https://github.com/pdepend/pdepend/pull/462) )
- Changed: Interal cleanup with refactoring and adding missing PHPDoc ( [\#477](https://github.com/pdepend/pdepend/pull/477), [\#484](https://github.com/pdepend/pdepend/pull/484) and [\#485](https://github.com/pdepend/pdepend/pull/485) ) 

pdepend-2.7.1 (2020/02/12)
==========================

- Fixed [\#453](https://github.com/pdepend/pdepend/pull/453) Support for typed properties (nullable, array, FQN, scalar)
- Fixed [\#446](https://github.com/pdepend/pdepend/pull/446) Suppress E_NOTICE thrown by unserialize() 

pdepend-2.7.0 (2020/01/24)
==========================

- Added [\#362](https://github.com/pdepend/pdepend/pull/362) Support for php:// stream wrappers
- Added [\#427](https://github.com/pdepend/pdepend/pull/427) PHP 7.4 features support 

pdepend-2.6.1 (2019/12/21)
==========================

- Added [\#398](https://github.com/pdepend/pdepend/pull/398) Support for Symfony 5
- Fixed [\#299](https://github.com/pdepend/pdepend/pull/299) Array concatenation in constant declaration

pdepend-2.6.0 (2019/12/16)
==========================

- Added [\#383](https://github.com/pdepend/pdepend/pull/383) Support of PHP 7.1 syntax
- Fixed [\#386](https://github.com/pdepend/pdepend/pull/386) PHP 7 and parsing some return types
- Fixed [\#381](https://github.com/pdepend/pdepend/pull/381) Unexpected token error with invokable
- Fixed [\#365](https://github.com/pdepend/pdepend/pull/365) unable to call method on newly created and invoked class
- Removed not needed files from the export used by composer

pdepend-2.5.2 (2017/12/13)
==========================

This release contains a single bugfix for missing command options.

- Fix for GH355: Unknown option --jdepend-chart [\#356](https://github.com/pdepend/pdepend/pull/356) ([manuelpichler](https://github.com/manuelpichler))

pdepend-2.5.1 (2017/12/06)
==========================

This release contains many bugfixes, enables Scrutinizer and adds code coverage integration. HHVM support was dropped. Full support for missing language features for PHP <= 7.1 were added.

- Support for "yield from" in PHP 7.0 [\#350](https://github.com/pdepend/pdepend/pull/350) ([emirb](https://github.com/emirb))
- Support for constant visibility in interfaces in PHP 7.1 [\#349](https://github.com/pdepend/pdepend/pull/349) ([KacerCZ](https://github.com/KacerCZ))
- Support catch of multiple exception types in PHP 7.1 [\#348](https://github.com/pdepend/pdepend/pull/348) ([KacerCZ](https://github.com/KacerCZ))
- Added support for PHP 7.1 types - iterable and void [\#342](https://github.com/pdepend/pdepend/pull/342) ([KacerCZ](https://github.com/KacerCZ))
- Add Symfony 4 support [\#340](https://github.com/pdepend/pdepend/pull/340) ([surikman](https://github.com/surikman))
- Fix Scrutinizer config [\#336](https://github.com/pdepend/pdepend/pull/336) ([emirb](https://github.com/emirb))
- Fixed PHPUnit warnings [\#333](https://github.com/pdepend/pdepend/pull/333) ([KacerCZ](https://github.com/KacerCZ))
- Codecov explicit coverage file [\#332](https://github.com/pdepend/pdepend/pull/332) ([emirb](https://github.com/emirb))
- Add codecov.io integration [\#331](https://github.com/pdepend/pdepend/pull/331) ([emirb](https://github.com/emirb))
- Use uniqid\(\) in AbstractASTArtifact::getId\(\) instead of microtime\(\) [\#330](https://github.com/pdepend/pdepend/pull/330) ([KacerCZ](https://github.com/KacerCZ))
- Fix Iterator so it will not return directories [\#329](https://github.com/pdepend/pdepend/pull/329) ([KacerCZ](https://github.com/KacerCZ))
- Fix tests on Windows [\#328](https://github.com/pdepend/pdepend/pull/328) ([KacerCZ](https://github.com/KacerCZ))
- PHP 7.2 fixes [\#326](https://github.com/pdepend/pdepend/pull/326) ([emirb](https://github.com/emirb))
- Capitalize XML word [\#325](https://github.com/pdepend/pdepend/pull/325) ([bocharsky-bw](https://github.com/bocharsky-bw))
- Fix typo [\#321](https://github.com/pdepend/pdepend/pull/321) ([ravage84](https://github.com/ravage84))
- Fixes \#193 regression [\#319](https://github.com/pdepend/pdepend/pull/319) ([mdeboer](https://github.com/mdeboer))
- Remove running of PHP 5.3 build since dropped by Travis. [\#316](https://github.com/pdepend/pdepend/pull/316) ([niconoe-](https://github.com/niconoe-))
- Fix issue \#297 adding support for constant visibility into the parser for PHP 7.1 [\#314](https://github.com/pdepend/pdepend/pull/314) ([mmucklo](https://github.com/mmucklo))
- Remove references to HHVM [\#309](https://github.com/pdepend/pdepend/pull/309) ([ravage84](https://github.com/ravage84))
- fix count\(\): Parameter must be an array or an object that implements ... [\#303](https://github.com/pdepend/pdepend/pull/303) ([remicollet](https://github.com/remicollet))
- "a xml" -\> "an xml" [\#300](https://github.com/pdepend/pdepend/pull/300) ([bocharsky-bw](https://github.com/bocharsky-bw))

pdepend-2.5.0 (2017/01/19)
==========================

This release closes a parsing bug in PDepend 2.4.1, starts with the 
implementation of PHP 7.1 support and adds a new attribute for the 
fully-qualified-classname to the summary report.

- Fixed #282: Issue with grouped use statements when only a single 
  level namespace prefix was used. Fixed in commit #3e523f5.
- Implemented #294: Add support for PHP 7.1 optionals. Implemented in 
  commit #c5c53eb.
- Implemented #88: Fully qualified classname in summary report. 
  Implemented in commit #13e9cbc.

pdepend-2.4.1 (2017/01/11)
==========================

This release closes a bug within PDepend's parser when keywords are 
used as method or constant names in PHP 7.0

- Fixes an issue with methods or constants with keyword identifiers 
  called/accessed in PHP 7. Fixed in commit #8f07ac7.

pdepend-2.4.0 (2017/01/10)
==========================

This release implements language features like Anonymous Classes, 
Group use Declarations, Uniform Variable Syntax or Loosening Reserved 
Word Restrictions that were introduced with PHP 7.0, so that PDepend 
2.4 is now PHP 7.0 compatible.

- Fixed #281: PHP 7 - Anonymous Class - Internal parser state issues 
  Fixed in commit #00a61c6.
- Fixed #285: Parse the magic constant __TRAIT__ Fixed in commit 
  #b76e2b0.
- Fixed #210: Partial Class Namespace is Calculated Twice: in Global 
  and it's Own Namespace Fixed in commit #e81411f.
- Implemented #280: Refactor SymbolTable Implemented in commit 
  #1265259.
- Implemented #282: PHP 7 - Group use declarations Implemented in 
  commit #fd4aaca.
- Implemented #269: Unexpected token: :: (implicit object / method 
  usage) Implemented in commit #e611915.
- Implemented #204: Support for the ... operator in function calls 
  Implemented in commit #078e532.
- Implemented #290: Unexpected token: ARRAY (reserved keyword as a 
  class constant) Implemented in commit #d4bf7bb.

pdepend-2.3.2 (2016/11/23)
==========================

Bugfix release that closes a caching issue that was introduced in 
2.3.1.

- Fixed #276: Uncaught Error: Call to a member function type() on null 
  in Fixed in commit #48d8081.
- Allow list as method name under PHP 7 Fixed in commit #4968ed4.
- Fixed #277: serialize(): "comment" is returned from __sleep multiple 
  times in store in FileCacheDriver.php Fixed in commit #31cf053.

pdepend-2.3.1 (2016/11/23)
==========================



- Fixed #250: Updating ASTAnonymousClass to implement ASTNode, 
  retaining class behavior. Fixed in commit #2111906.

pdepend-2.3.0 (2016/11/22)
==========================

This release closes multiple bugs/issue and has merged several 
outstanding pull requests. Beside that it is now possible to pipe 
source through STDIN into pdepend.

- Fixed #263: Fix NPath calculations for the ternary operator. Fixed 
  in commit #df0e9c5.
- Fixed #260: Fix typos Fixed in commit #20b36c1.
- Fixed #259: DOMDocument file handling. Fixed in commit #fa2afc6.
- Fixed #247: Fix handling of use declarations with const and function 
  keywords. Fixed in commit #dc9128b.
- Fixed #240: Fix some typos from the website. Fixed in commit 
  #332672a.
- Fixed #249: Unexpected token: callable Fixed in commit #.
- Support for PHP's ** pow expression implemented. Implemented in 
  commit #bce6145.
- Implemented #262: Support stdin implemented. Implemented in commit 
  #3ef2328.
- Implemented #231: Apply the filter on files as well. Implemented in 
  commit #62d1406.

pdepend-2.2.6 (2016/10/04)
==========================

Bugfix release.

- Fixed #267: Fix UnexpectedTokenException on null coalesce operator 
  Fixed in commit #8e80aaa.

pdepend-2.2.4 (2016/03/10)
==========================

This releases closes a bug in PDepend's parsing code for PHP 7 return 
types, that caused a invalid state in the internal AST representation. 
This bug was issued in PHPMD's issue tracker first by user radmen.

- Cannot create new nodes, when internal state is frozen. #328 Fixed 
  in commit #ffe9957.

pdepend-2.2.3 (2016/02/22)
==========================

This release includes several pending pull requests from GitHub. 
Beside that this release adds support for complex expressions in 
property, constant and parameter declarations, introduced with PHP 
5.6.

- Fixed #226: Fixed division by zero issue. Fixed in commit #fb46614.
- Fixed #227: Fix support to files filters. Fixed in commit #4e150db.
- Fixed #230: Fix handling cygwin home folder location. Fixed in 
  commit #126c38a.
- Implemented #221: Add --quiet option. Implemented in commit 
  #9a710f7.
- Implemented #236: Switch to PSR-4 for autoloading Implemented in 
  commit #57b54bd.
- Implemented #238: Unexpected token errors for 5.6 "constant 
  expression" initializers. Implemented in commit #0087c94.

pdepend-2.2.2 (2015/10/16)
==========================

This release adds a new analyzer that can be used to visualize 
namespace dependencies.

- Implemented #221: Added line numbers to summary log. Implemented in 
  commit #a975553.
- Implemented #222: Calculate type dependencies. Implemented in commit 
  #8a924f6.

pdepend-2.2.1 (2015/09/24)
==========================

With this release we made a dependency downgrade, so that we can 
support more environments.

- Implemented #223: Minimum Symfony version downgraded to 2.3. 
  Implemented in commit #8601cc3.

pdepend-2.2.0 (2015/09/19)
==========================

This release contains beside several contributed additions and 
bugfixes better support for PHP 7 language constructs.

- Fixed #119: PDepend doesn't follow any symlinks to directories. 
  Fixed in commit #b80ae7e.
- Fixed #143: Truncated summary when analyzing ISO-8859-1 input. Fixed 
  in commit #d979462.
- Fixed #193: Cache conflict when executing pdepend in parallel. Fixed 
  in commit #a4e20ff.
- Fixed #197: Warning: DOMNode::cloneNode(): ID <id> already defined 
  in phar. Fixed in commit #2221f74.
- Fixed #201: ShellCheck warnings in scripts/compare.sh Fixed in 
  commit #.
- Fixed #209: PHP 5.6 constant syntax not supported. Fixed in commit 
  #1209b0e.
- Fixed #213: PHP 7: T_CHARACTER and T_BAD_CHARACTER are no longer 
  defined. Fixed in commit #1f5b051.
- Fixed #214: PHP 7: Return types not supported. Fixed in commit 
  #249932b.

pdepend-2.1.0 (2015/05/21)
==========================

This release introduces an analyzer for the Halstead metrics and the 
maintainability index, contributed by Matthias Mullie. Beside that we 
have closed several issues and bugs in PDepend's source code.

- Fixed #196: Fix Typo in phpDoc Fixed in commit #2b51fed.
- Fixed #200: Fix annotation-typo in AbstractPHPParser.php Fixed in 
  commit #776529d.
- Fixed #202: Support for variable arg list implemented Fixed in 
  commit #dff2547.
- Implemented #177: HHVM support Implemented in commit #48ee5d9.
- Implemented #185: Remove unused imports and order alphabetically 
  Implemented in commit #46d5fb.
- Implemented #198: Add analyzers for Halstead measures & 
  maintainability index Implemented in commit #3497862.

pdepend-2.0.6 (2015/03/02)
==========================

Concurrency issue in the file cache garbage collector fixed.

- Suppress exceptions when there are concurrency issues within the 
  garbage collector. Fixed in commit #3e31cc7.

pdepend-2.0.5 (2015/02/27)
==========================

This release just adds a simple garbage collector for PDepend's file 
cache

- Garbage collector for old cache files added. Implemented in commit 
  #56712b1.

pdepend-2.0.4 (2014/12/04)
==========================

This release closes some minor issues and incorporates several 
outstanding pull requests.

- Fixed #187: Unexpected token :?> with broken up switch statement 
  Fixed in commit #c12ee0e.
- Fixed #180: Unexpected token: <<, line: 5, col: 27 Fixed in commit 
  #4df5b9d.
- Fixed #179: Fixed display of duration longer than one hour Fixed in 
  commit #1288292.
- Fixed #176: Typo on website fixed. Fixed in commit #6a8e542.
- Fixed #175: Inconsistent indention in phpunit.dist.xml file fixed. 
  Fixed in commit #bc758e4.
- Fixed #174: Fix conflicting import: "Extension" is already in use in 
  the "PDepend\DependencyInjection" namespace. Fixed in commit 
  #e3e672b.
- Fixed #173: Fixing parsing True/False keywords in namespaces: Usage 
  of true and false keywords are allowed in namespace declarations in 
  PHP. Fixed in commit #d96e4e7.
- Fixed #170: invalid xml-report after parsing traits Fixed in commit 
  #1d1bec0.
- Fixed #167: Fix Invalid argument supplied for foreach() in 
  FileCacheDriver.php Fixed in commit #73d32f3.
- Fixed #165: Fix FileUtil::getUserHomeDir on Mac Fixed in commit 
  #4826c3f.
- Fixed #164: Empty yields raise Fatal error: When using empty yields 
  yield; the parser raises an fatal error. Fixed in commit #7ab0736.
- Fixed #163: File cache concurrency fix: Fixes concurrent run of 
  pdepend and phpmd. Fixed in commit #3955c07.
- Fixed #154: Invalid trait conflict errors: t is completely valid to 
  mix traits in PHP that have the same methods declared, as long as 
  only one of them is concrete (all the others must be abstract). 
  Fixed in commit #45ab1d3.
- Fixed #128: Problem when I use parent:: in trait Fixed in commit 
  #a73e6de.
- Implemented #177: HHVM support Implemented in commit #17da34b.

pdepend-2.0.3 (2014/10/08)
==========================

This is a bugfix release which closes some minor issues.

- Fixed #129: Windows+Composer install fails due to "path too long" 
  Fixed in commit #64f95c1.
- Fixed #172: Outdated news on the website
- Fixed #166: Added support for foreach with list statement (PHP 5.5) 
  Fixed in commit #a744af1.
- Fixed #171: The list usage in foreach loops reports an invalid token 
  Fixed in commit #a744af1.

pdepend-2.0.2 (2014/09/16)
==========================



- Fixed #160: include_once PDepend/Util/Coverage/CloverReport.php: 
  failed to open stream Fixed in commit #4dca605.
- Implemented #105: Support Java style array notations in doc comments 
  Implemented in commit #2ec5166.

pdepend-2.0.1 (2014/09/09)
==========================

Bug fix release which closes a issue within PDepend's C.R.A.P. index 
calculation.

- Handle code surrounded by @codeCoverageIgnore annotations correct. 
  Fixed in commit #3e67aa2.

pdepend-2.0.0 (2014/05/21)
==========================

New mayor release of PDepend.

- Fixed #126: Allow closure as array element Fixed in commit #b9775ac.
- Fixed #153: Support for new finally keyword implemented. Fixed in 
  commit #e536e7a.
- Fixed #144: pdepend --version gives me a wrong message.. Fixed in 
  commit #f6acea9.
- Implemented #113: Specify license, BSD license was missing in 
  composer.json file. Implemented in commit #3ba9c9e.
- Implemented #117: Adds composer autoload info Implemented in commit 
  #e624f8e.

pdepend-1.1.1 (2013/07/25)
==========================

Closes several PHP 5.4 issues.

- Fixed #116: Adding a fix for PHP 5.4 style arrays. Fixed in commit 
  #cbfddaa.
- Fixed #95: PHP 5.4 array syntax is not supported in property 
  initialization. Fixed in commit #f6ee217.
- Fixed #97: protected property PHP_Depend_Code_Method::$parent Fixed 
  in commit #87a1b5e.
- Fixed #104: Syntax errors reported when PHP 5.4 short array syntax 
  is used in method signatures or class variable definitions. Fixed in 
  commit #d731fa6.
- Fixed #103: Fix syntax error in composer.json example Fixed in 
  commit #e897a66.
- Implemented #101: Package name for chart svg Implemented in commit 
  #479aaa5.

pdepend-1.1.0 (2012/09/12)
==========================

This release closes a critical issue in the context of traits 
handling.

- Changed type of Node/Trait Fixed in commit #806eaab.
- Changed to PSR1 coding standard. Implemented in commit #.

pdepend-1.0.7 (2012/04/29)
==========================

This release closes a minor bug within the parsing code for doc 
comments..

- Fixed: DocComment is sometimes incorrectly set for functions Fixed 
  in commit #ac71753.

pdepend-1.0.6 (2012/04/22)
==========================

This release closes a bug with traits that were introduced with PHP 
5.4. This bug results in an E_FATAL when PHP_Depend performs coupling 
analysis on a trait.

- Fixed: E_FATAL when the coupling analyzer processes a trait. Fixed 
  in commit #ac71753.
- Added: Composer support Implemented in commit #3d98f02.

pdepend-1.0.5 (2012/04/05)
==========================

This release closes a bug introduced with the last release, which 
causes PHP_Depend not to flush it's metric cache when a file has 
changed.

- Fixed #27588643: PHP_Depend doesn't invalidate the cache. Fixed in 
  commit #99d5c13.

pdepend-1.0.4 (2012/02/25)
==========================

This release closes an issue introduced with the last release. It 
closes one more regression related to PHP's memory_limit and the 
Suhosin patch.

- Fixed fatal error due to bug in memory_limit modification code. 
  Fixed in commit #b869eff.

pdepend-1.0.3 (2012/02/25)
==========================

This release closes a minor issue in PHP_Depend's memory handling when 
it is run in a PHP environment that uses the Suhosin patch and the 
suhosin.memory_limit setting.

- Fixed #25450915: Alert disable memory_limit Fixed in commit 
  #0628e7d.

pdepend-1.0.2 (2012/02/15)
==========================

This release contains a huge improvement in PHP_Depend's memory usage. 
Due to some changes in the caching behavior we got a memory reduction 
of ~ 90%, measured against medium sized code bases like Symfony2 or 
FLOW3.

- Fixed #24732243: pdepend fails on 'const' Fixed in commit #4d6a687.
- Fixed #24975343: PHP_Depend doesn't handle nested list expressions. 
  Fixed in commit #d124ef0.
- Implemented #24702477: Huge memory footprint Implemented in commit 
  #75c9755.

pdepend-1.0.1 (2012/02/08)
==========================

This release fixes two bugs in PHP_Depend's parser, which resulted in 
uncatchable errors.

- Fixed #24635313: _parseOptionalExpression() returning null causes 
  exception Fixed in commit #97189b0.
- Fixed #24638569: pdepend crashes on vanilia drupal site Fixed in 
  commit #f20f40a.

pdepend-1.0.0 (2012/02/04)
==========================

Now that we have completed support for all the new language features 
introduced with PHP 5.4, we are ready to release version 1.0.0 of 
PHP_Depend. PHP_Depend can now handle traits, static closures, binary 
numbers, the callable type hint and the new short array syntax. Beside 
that, we have spent much effort in improving PHP_Depend's overall 
performance and we got an average speed gain of ~ 15% for processing 
major frameworks like Symfony2 or FLOW3, when PHP_Depend's file cache 
(default setup) is used. Additionally this release closes several 
minor issues in PHP_Depend.

- Fixed #18976391: PHP_Depend's file cache implementation does not 
  work with PHP 5.4. Fixed in commit #06ce51a.
- Fixed #18459091: PDepend task never ends, if there is an incorrect 
  inheritance Fixed in commit #13b5d12.
- Fixed #19875155: Implement static closures Fixed in commit #1e24a34.
- Implemented #8927307: Add support for traits Implemented in commit 
  #84f612e.
- Implemented #19874825: Implement the short array syntax introduced 
  with PHP 5.4 Implemented in commit #338bca2.
- Implemented #9069837: Implement expression lists. Implemented in 
  commit #bbb06c7.
- Implemented #21435399: Implement PHP 5.4 variable method names 
  Implemented in commit #911b6ec.
- Implemented #21408469: Implement PHP 5.4 binary number format 
  Implemented in commit #e3bccf1.
- Implemented #21339411: Implement PHP 5.4 callable type hint 
  Implemented in commit #ee5caa6.
- Implemented #21271399: Deprecate the --phpunit-xml log option 
  Implemented in commit #658c25c.
- Implemented #19817309: Implement PHP 5.4 array dereferencing 
  Implemented in commit #6dba831.

pdepend-0.10.9 (2012/01/25)
===========================

This release fixes a small issue in PHP_Depend's parser, which results 
in an exception when heredoc was used as property or constant 
initializer.

- Fixed #23951621: PHP_Depend fails on Heredocs and Nowdocs in 
  property declaration. Fixed in commit #373c478.

pdepend-0.10.8 (2012/01/24)
===========================

This release closes an issue in PHP_Depend's parser that produces 
invalid package names when the source file contains a statement before 
the class or interface doc comment.

- Fixed #23905939: Package gets lost when prefixed with control 
  structure Fixed in commit #b62bed7.

pdepend-0.10.7 (2011/12/06)
===========================

This release closes a critical bug in PHP_Depend's parser which 
results in an E_FATAL. This can happen when a control structure does 
not contain a body or termination token.

- E_FATAL when a control structure like if, for or foreach does not 
  contain a body or a termination symbol. Fixed in commit #b367a41.

pdepend-0.10.6 (2011/08/21)
===========================

This release closes a critical bug in PHP_Depend's parser that 
produced false positiv error messages for classes named like 'True', 
'False' or 'Null'

- Fixed #17264279: Unexpected token: True, line: 348, col: 49, 
  file:... Fixed in commit #5ac3e55.

pdepend-0.10.5 (2011/05/20)
===========================

This release closes two minor bugs in PHP_Depend. One incompatibility 
with PHP 5.2.x versions and one bug related to PHP_Depend's log 
behavior when PHP_Depend analyzes unstructured source code. This 
release was published on May the 20th 2011.

- Fixed #13255437: PHP 5.2 Compatibility Issues. Fixed in commit 
  #8d4a095.
- Fixed #13405179: PHP Depend report is not generated if all files do 
  not contain a class nor a function. Fixed in commit #554ade1.

pdepend-0.10.4 (2011/04/09)
===========================

This release contains an improvement in PHP_Depend's memory 
consumption. We have optimized the internal data structures in such a 
way that the memory footprint was reduced by ~30%. These values were 
measured for currently popular frameworks with a medium to large sized 
code base. The tests were run under ubuntu with PHP 5.2.17 and PHP 
5.3.6.

pdepend-0.10.3 (2011/03/02)
===========================

This release closes a critial bug in PHP_Depend's analyzer locator 
code that prevents PHP_Depend from running on windows. This release 
was published on March the 02th 2011.

- Fixed #10659085: Analyzer locator code does not work on windows. 
  Fixed in commit #0101798.

pdepend-0.10.2 (2011/02/28)
===========================

This release of PHP_Depend closes two bugs. One related to the start 
and end line properties of object property nodes in the syntax tree. 
The second fix closes a bug in PHP_Depend's implementation of the WMCi 
metric. Beside these two fixes this release implements three minor 
features, one design issue in the syntax tree api and the other two 
other features are related to the new metrics CE, CA, CBO and NPM. 
Additionally we have restructured PHP_Depend's directory structure 
from a custom, freestyle format to a directory layout that is similar 
to maven's convention. With this change we have fixed several issues 
and workarounds in PHP_Depend's build process.

- Fixed #9936901: WMCi calculation is incorrect for overwritten 
  methods. Fixed in commit #69d079a.
- Fixed #8927377: Invalid Start/End Line/Column for object property 
  access. Fixed in commit #fc57264.
- Implemented #9069393: Replace optional NULL argument of setPackage() 
  with separate method. Implemented in commit #1282cdb.
- Implemented #9069871: Implement efferent- and afferent-coupling for 
  classes. Implemented in commit #07537c2.
- Implemented #9997915: Implement Number of Public Methods metric. 
  Implemented in commit #2dd3ebf.

pdepend-0.10.1 (2011/02/06)
===========================

- Fixed #9634613: Notice: Undefined property $___temp___. Fixed in 
  commit #5fb6900.

pdepend-0.10.0 (2011/02/05)
===========================

This version only contains a small bugfix compared to the last release 
canditate. Version 0.10.0 of PHP_Depend was released on February the 
05th 2011. The key feature for this release is the overall performance 
of PHP_Depend. Therefore we have implemented a new caching layer that 
reuses already calculated analyzes-results much more efficient than 
older versions of PHP_Depend. With these modifications we have 
achieved a performance gain of 100% and more for consecutive 
analysis-runs. This final release only fixes a small bug in 
PHP_Depend's analyzer class locator that has caused some issues when 
PHP_Depend was executed as an external dependency that uses a \*.phar 
archive as distribution format.

- Fixed #9623949: Also find analyzers in phar archives in the current 
  include_path. Fixed in commit #f53dca9.
- Fixed #113: PHP fatal error when an unserialized object graph none 
  NodeI instances. Fixed in commit #c0f4384.
- Fixed #164: Faulty implementation of the --ignore path filter fixed. 
  Now this filter only works on the local part of a file or directory 
  name and not on the complete path. Fixed in commit #f75275e.
- Fixed #176: Calculation of CIS metric is incorrect. Fixed in commit 
  #1193f4a.
- Fixed #182: Clone is a valid function, method and type name in older 
  php versions. Fixed with git commit Fixed in commit #b18bf37.
- Fixed #189: Invalid Start/End Line/Column for object method 
  invocation. Fixed in commit #c6cc9dd.
- Fixed #191: New implementation of --ignore only accepts relative 
  paths. Fixed in commit #38e6b52.
- Fixed #163: Alternative syntax end tokens can terminate with closing 
  PHP-tag.
- Fixed #181: No log generated when parsing Typo3 extension 
  "t3extplorer" (Unexpected token ASCII 39). Indirectly fixed in this 
  release.
- Implemented #130: Simplify PHP_Depend's ASTCompoundVariable and skip 
  nested ASTCompoundExpression node instance.
- Implemented #131: Add new method isThis() to PHP_Depend's 
  ASTVariable class.
- Implemented #132: Housekeeping: Cleanup the PHP_Depend_Input package 
  test code.
- Implemented #139: Implement Post-/Pre- Increment/Decrement.
- Implemented #143: Support PHP's alternative control structure 
  syntax.
- Implemented #146: Implement PHP's declare-statement.
- Implemented #148: Implement cast expressions.
- Implemented #170: Rename FunctionNameParserImpl into 
  FunctionNameParserAllVersions. Task scope changed and complete 
  refactoring done. Parser moved into a version specific parser class.
- Implemented #178: Provide configuration option for the cache 
  directory. Implemented in commit #00ed8ec.
