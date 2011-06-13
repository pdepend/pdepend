=================
Version 0.10.0RC1
=================

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This document describes the features and bugfixes of the
               0.10.0RC1 release of PHP_Depend. One of the key features
               was performance and we are proud to announce a performance
               gain of 100% and more.
:Keywords:     Release, Version, Features, Bugfixes, Performance

Version 0.10.0RC1 of PHP_Depend was released on November the 25th 2010. The
key feature for this release was the overall performance of PHP_Depend.
Therefore we have implemented a new caching layer that reuses already
calculated analyzes-results much more efficient than older versions of
PHP_Depend. With these modifications we have achieved a performance gain of
100% and more for consecutive analysis-runs, against projects like Symfony2,
Flow3 and the Zend Framework.

Additionally this release contains the following new features and bugfixes.

Features
--------

- Implemented `#130`__: Simplify PHP_Depend's ASTCompoundVariable and skip
  nested ASTCompoundExpression node instance. Implemented in svn revision
  number #1344.
- Implemented `#131`__: Add new method isThis() to PHP_Depend's ASTVariable
  class. Implemented in svn revision #1291.
- Implemented `#132`__: Housekeeping: Cleanup the PHP_Depend_Input package test
  code. Done in svn revision #1366.
- Implemented `#139`__: Implement Post-/Pre- Increment/Decrement. Implemented
  in svn revision #1317.
- Implemented `#143`__: Support PHP's alternative control structure syntax.
  Done in svn revision #1375.
- Implemented `#146`__: Implement PHP's declare-statement. Done in subversion
  revision #1375.
- Implemented `#148`__: Implement cast expressions. Implemented in svn
  revision #1283.
- Implemented `#170`__: Rename FunctionNameParserImpl into
  FunctionNameParserAllVersions. Task scope changed and complete refactoring
  done. Parser moved into a version specific parser class. Done in subversion
  revision #.
- Implemented `#178`__: Provide configuration option for the cache directory.
  Implemented with git commit `#00ed8ec`__.

Bugfixes
--------

- Fixed `#163`__: Alternative syntax end tokens can terminate with closing
  PHP-tag. Fixed in svn revision #1527.
- Fixed `#164`__: Faulty implementation of the --ignore path filter fixed.
  Now this filter only works on the local part of a file or directory
  name and not on the complete path. Fixed with commit `#f75275e`__.
- Fixed `#176`__: Calculation of CIS metric is incorrect. Fixed with commit
  #1193f4a.
- Fixed `#181`__: No log generated when parsing Typo3 extension "t3extplorer"
  (Unexpected token ASCII 39). Indirectly fixed in this release.
- Fixed `#182`__: Clone is a valid function, method and type name in older
  php versions. Fixed with git commit `#b18bf37`__.

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/130
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/131
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/132
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/139
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/143
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/146
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/148
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/170
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/178
__ https://github.com/pdepend/pdepend/commit/00ed8ec

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/163
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/164
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/176
__ https://github.com/pdepend/pdepend/commit/f75275e
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/181
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/182
__ https://github.com/pdepend/pdepend/commit/b18bf37

