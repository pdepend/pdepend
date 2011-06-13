=================
Version 0.10.0RC2
=================

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This document describes the features and bugfixes of the
               0.10.0RC1 release of PHP_Depend. One of the key features
               was performance and we are proud to announce a performance
               gain of 100% and more.
:Keywords:     Release, Version, Features, Bugfixes, Performance

Version 0.10.0RC2 of PHP_Depend was released on December the 16th 2010. The
key feature for this release was the overall performance of PHP_Depend.
Therefore we have implemented a new caching layer that reuses already
calculated analyzes-results much more efficient than older versions of
PHP_Depend. With these modifications we have achieved a performance gain of
100% and more for consecutive analysis-runs.

This second release candiate fixes a critical bug that was introduced with
version `0.10.0RC1`__.

Bugfixes
--------

- Fixed `#113`__: PHP fatal error when an unserialized object graph
  none NodeI instances. Fixed with commit `#c0f4384`__.

Download
--------

Download the latest version of PHP_Depend as a PHAR archive `here`__.

__ /download/release/0.10.0rc1/changelog.html
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/113
__ https://github.com/pdepend/pdepend/commit/c0f4384
__ /download/release/0.10.0rc2/pdepend.phar
