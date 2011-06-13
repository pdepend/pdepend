==========================
PHP_Depend-0.9.11 released
==========================

:Abstract:
  I have just released version 0.9.11 of PHP_Depend. It contains
  a few bug fixes and improvements for PHP_Depend.

I have just released version 0.9.11 of PHP_Depend. It contains
a few bug fixes and improvements for PHP_Depend.

* Fixed `#118`__: Calculation of the Number Of Overwritten Methods is
  wrong. Fixed in svn revision `#1112`__.
* Fixed `#119`__: Three test cases fail for all PHP versions prior to 5.3.
  Fixed in svn revision `#1114`__.
* Implemented `#121`__: Parse arguments of the ASTForeachStatement. Implemented
  in svn revision `#1115`__.
* Critical issue in PHP_Depend's temporary data cache fixed. This bug 
  only occured when running several instances of PHP_Depend in  
  parallel. In this setup the used cache key `spl_object_hash()`__ has 
  caused a corrupted cache, because different php process instances 
  have written different data to the same cache file.

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/118
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1112
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/119
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1114
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/121
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1115
__ http://php.net/spl_object_hash

As always, you can get the latest PHP_Depend version from its PEAR channel: 
`pear.pdepend.org`__: ::

  mapi@arwen ~ $ pear channel-discover pear.pdepend.org
  mapi@arwen ~ $ pear install pdepend/PHP_Depend-beta

__ http://pear.pdepend.org

Or you can fetch the sources from the subversion reposition: ::

  mapi@arwen ~ $ svn co http://svn.pdepend.org/branches/0.9.0/

And additionally you can find a repository mirror on github: ::

  mapi@arwen ~ $ git clone git://github.com/manuelpichler/pdepend.git


