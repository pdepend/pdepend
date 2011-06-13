==============
Version 0.10.4
==============

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This document contains the release notes for the PHP_Depend
               release 0.10.4. This version is a pure optimization release
               that reduces PHP_Depend's memory footprint by ~30%.
:Keywords:     Release, Version, Optimization, Memory footprint, Memory consumption

This release contains an improvement in PHP_Depend's memory consumption.
We have optimized the internal data structures in such a way that the
memory footprint was reduced by ~30%. These values were measured for
currently popular frameworks with a medium to large sized code base. The
tests were run under ubuntu with PHP 5.2.17 and PHP 5.3.6. This release 
was published on April the 09th 2011.

Download
--------

Download the latest version of PHP_Depend as a PHAR archive `here`__ or
get the source from `GitHub`__.

__ /download/release/0.10.4/pdepend.phar
__ https://github.com/pdepend/pdepend/tree/0.10.4

