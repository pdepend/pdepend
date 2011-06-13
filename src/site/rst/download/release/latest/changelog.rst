==============
Version 0.10.5
==============

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This document contains the release notes for the PHP_Depend
               release 0.10.5. This version of PHP_Depend closes two minor
               bugs in the logging subsystem and an incompatibility with
               PHP 5.2.x versions.
:Keywords:     Release, Version, Bugfix, Bugs, Logging

s release closes two minor bugs in PHP_Depend. One incompatibility
with PHP 5.2.x versions and one bug related to PHP_Depend's log
behavior when PHP_Depend analyzes unstructured source code. This release
was published on May the 20th 2011.

Bugfixes
--------

- Fixed `#13255437`__: PHP 5.2 Compatibility Issues. Fixed with commit
  `#8d4a095`__.
- Fixed `#13405179`__: PHP Depend report is not generated if all files do
  not contain a class nor a function. Fixed with commit `#554ade1`__.

Download
--------

Download the latest version of PHP_Depend as a PHAR archive `here`__ or
get the source from `GitHub`__.

__ https://www.pivotaltracker.com/story/show/13255437
__ https://github.com/pdepend/pdepend/commit/8d4a095
__ https://www.pivotaltracker.com/story/show/13405179
__ https://github.com/pdepend/pdepend/commit/554ade1
__ /download/release/0.10.5/pdepend.phar
__ https://github.com/pdepend/pdepend/tree/0.10.5

