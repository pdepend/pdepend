==========================
PHP_Depend-0.9.10 released
==========================

:Abstract:
  I have just released the bug fix version 0.9.10 of PHP_Depend. This
  release contains several bug fixes and improvements for PHP_Depend.

I have just released the bug fix version 0.9.10 of PHP_Depend. This
release contains several bug fixes and improvements for PHP_Depend.

* Implemented `#72`__: Add NOAM, NOOM and NOCC metrics. Implemented in svn 
  revision `#1084`__.
* Implemented `#74`__: Make CRAP-index available. Implemented in svn revision 
  `#1063`__.
* Implemented `#105`__: Support for deep search implement. Implemented in svn
  revision `#1078`__.
* Fixed `#106`__: Filter algorithm is broken for namespaced internal classes.
  Fixed in svn revision `#1039`__.
* Fixed `#110`__: Duplicate "coupling" directory in test code. Fixed in svn
  revision `#1032`__. 
* Fixed `#111`__: Dynamic Strings are treated as literal strings. Fixed in svn
  revision `#1037`__. 
* Fixed `#114`__: Parsing error caused by complex string expressions fixed.
  Fixed in svn revision `#1068`__.
* Fixed `#115`__: Summary and PHPUnit Report lists unknown classes. Fixed in
  svn revision `#1101`__.
* Fixed `#116`__: Returns reference results in parsing error. Fixed in svn 
  revision `#1090`__.
* Performance intensive calculation result cached.
* Test code restructured and improved.
* Concurrency issue for parallel running pdepend instances fixed.

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/72
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1082
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/74
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1063
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/105
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1078
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/106
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1039
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/110
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1032
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/111
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1037
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/114
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1068
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/115
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1101
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/116
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1090

As always, you can get the latest PHP_Depend version from its PEAR channel: 
`pear.pdepend.org`__: ::

  mapi@arwen ~ $ pear channel-discover pear.pdepend.org
  mapi@arwen ~ $ pear install pdepend/PHP_Depend-beta

__ http://pear.pdepend.org

Or you can fetch the sources from the subversion reposition: ::

  mapi@arwen ~ $ svn co http://svn.pdepend.org/branches/0.9.0/

And additionally you can find a repository mirror on github: ::

  mapi@arwen ~ $ git clone git://github.com/manuelpichler/pdepend.git


