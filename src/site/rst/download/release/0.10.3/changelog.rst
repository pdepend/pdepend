==============
Version 0.10.3
==============

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This document contains the release notes for the PHP_Depend
               release 0.10.3. This version closes one critical windows 
               related bug that was introduced with version 0.10.2.
:Keywords:     Release, Version, Features, Bugfixes, Windows

This release closes a critial bug in PHP_Depend's analyzer locator code
that prevents PHP_Depend from running on windows. This release was
published on March the 02th 2011.

Bugfixes
--------

- Fixed `#10659085`__: Analyzer locator code does not work on windows. Fixed
  with commit `#0101798`__.

Download
--------

Download the latest version of PHP_Depend as a PHAR archive `here`__ or
get the source from `GitHub`__.

__ https://www.pivotaltracker.com/story/show/10659085
__ https://github.com/pdepend/pdepend/commit/0101798
__ /download/release/0.10.3/pdepend.phar
__ https://github.com/pdepend/pdepend/tree/0.10.3

