=================
Version 0.10.0RC3
=================

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This document describes the features and bugfixes of the
               0.10.0RC3 release of PHP_Depend. This release candidate 
               contains two bugfixes.
:Keywords:     Release, Version, Features, Bugfixes, Performance

Version 0.10.0RC3 of PHP_Depend was released on Januar the 08th 2011. The
key feature for this release was the overall performance of PHP_Depend.
Therefore we have implemented a new caching layer that reuses already
calculated analyzes-results much more efficient than older versions of
PHP_Depend. With these modifications we have achieved a performance gain of
100% and more for consecutive analysis-runs.

This third release candidate fixes one behavior change that was introduced
with version `0.10.0RC1`__ and another bug that already exist for a longer
time in PHP_Depend's parser.

Bugfixes
--------

- Fixed `#189`__: Invalid Start/End Line/Column for object method
  invocation. Fixed in commit `#c6cc9dd`__.
- Fixed `#191`__: New implementation of ``--ignore`` only accepts
  relative paths. Fixed in commit `#38e6b52`__.

Download
--------

Download the latest version of PHP_Depend as a PHAR archive `here`__.

__ /download/release/0.10.0rc1/changelog.html
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/189
__ https://github.com/pdepend/pdepend/commit/c6cc9dd
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/191
__ https://github.com/pdepend/pdepend/commit/38e6b52
__ /download/release/0.10.0rc3/pdepend.phar

