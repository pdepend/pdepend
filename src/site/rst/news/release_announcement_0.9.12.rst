==========================
PHP_Depend-0.9.12 released
==========================

:Abstract:
  I am proud to announce the 0.9.12 release of PHP_Depend. PHP_Depend is a
  low level static code analysis tool. It takes the given source and 
  calculates several software metrics for the code. This data can be used by
  software-developers, architects and designers to control the quality of a
  software-product without time consuming code audits.

I am proud to announce the 0.9.12 release of PHP_Depend. PHP_Depend is a 
low level static code analysis tool. It takes the given source and 
calculates several software metrics for the code. This data can be used by  
software-developers, architects and designers to control the quality of a 
software-product without time consuming code audits.

This new release of PHP_Depend has done a great step into the direction of a 
token free PHP_Depend version. This means future versions of PHP_Depend will
work without the need to traverse linear token streams up and down, to measure
metrics. Instead PHP_Depend will rely on it's internal abstract syntax tree,
that represents the logical structure of the analyzed source code. With this 
solution it will be possible to implement several new features that will make
PHP_Depend more useful, for example static callgraph analysis.

* Implemented `#97`__: Replace current token approach in CCN- and NPath-Analyzer
  with AST-Nodes. Implemented in svn revision `#1248`__.
* Implemented `#125`__: PHP_Depend silently parses list statements. Fixed in
  svn revision `#1223`__. Thanks to Joey Mazzarelli for providing this patch.
* Implemented `#126`__: Generate reproducable node identifiers instead of
  random numbers. Implemented in svn revision `#1244`__.
* Fixed `#128`__: Variable variables in foreach statement cause an exception.
  Fixed in svn revision `#1237`__.
* Fixed `#133`__: Fatal error: Maximum function nesting level of '100' reached,
  aborting! in /usr/share/pear/PHP/Depend/Util/Log.php on line 109. Fixed
  in svn revision `#1257`__.
* Fixed `#134`__: ASTReturnStatement is not derived from ASTStatement. Fixed
  in svn revision `#1250`__.
* Fixed `#135`__: Several Statement classes do not inherit ASTStatement. Fixed
  in svn revision `#1255`__.

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/97
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1248
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/125
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1223
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/126
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1244
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/128
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1237
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/133
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1257
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/134
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1250
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/135
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1255

As always, you can get the latest PHP_Depend version from its PEAR channel: 
`pear.pdepend.org`__: ::

  mapi@arwen ~ $ pear channel-discover pear.pdepend.org
  mapi@arwen ~ $ pear install pdepend/PHP_Depend-beta

__ http://pear.pdepend.org

Or you can fetch the sources from the subversion reposition: ::

  mapi@arwen ~ $ svn co http://svn.pdepend.org/trunk pdepend

And additionally you can find a repository mirror on github: ::

  mapi@arwen ~ $ git clone git://github.com/manuelpichler/pdepend.git


