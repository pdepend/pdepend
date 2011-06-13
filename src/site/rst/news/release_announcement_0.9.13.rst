================================
PHP_Depend 0.9.13 bugfix release
================================

:Abstract:
  Today we have released the bugfix version 0.9.13 of PHP_Depend, the tool
  to measure several software metrics for your PHP application. This release
  fixes two critical bugs. One in the tokenizer implementation, which has
  modified valid code under test in such a way, that it has become invalid
  PHP code. The second fix is related to PHP_Depends's test code, where a
  missing ``require_once`` statement has caused a ``E_FATAL`` while running
  the test suite in process isolation.

Today we have released the bugfix version 0.9.13 of PHP_Depend, the tool
to measure several software metrics for your PHP application. This release
fixes two critical bugs. One in the tokenizer implementation, which has 
modified valid code under test in such a way, that it has become invalid
PHP code. The second fix is related to PHP_Depends's test code, where a
missing ``require_once`` statement has caused a ``E_FATAL`` while running
the test suite in process isolation.

- Fixed `#145`__: Incorrect require_once statement in ASTSwitchStatement
  source file. Fixed in svn revision `#1262`__.
- Fixed `#150`__: Invalid nowdoc substitution has produced broken code.
  Fixed in svn revision `#1266`__.

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/145
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1262
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/150
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1266

As always, you can get the latest PHP_Depend version from its PEAR channel: 
`pear.pdepend.org`__: ::

  mapi@arwen ~ $ pear channel-discover pear.pdepend.org
  mapi@arwen ~ $ pear install pdepend/PHP_Depend-beta

__ http://pear.pdepend.org

Or you can fetch the sources from the subversion reposition: ::

  mapi@arwen ~ $ svn co http://svn.pdepend.org/trunk pdepend

And additionally you can find a repository mirror on github: ::

  mapi@arwen ~ $ git clone git://github.com/manuelpichler/pdepend.git


