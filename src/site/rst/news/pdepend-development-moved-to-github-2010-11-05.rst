======================================
PHP_Depend development moved to GitHub
======================================

:Description:
  The main part of PHP_Depend's development already happens in a Git
  repository since Februar 2009. This means that the development happens
  locally and is then pushed to a public master repository. For backward
  compatibility we have synced the previous Subversion repository with
  Git checkout, but now we have decided to stop the sometimes really painful
  Subversion support. At least now on you should switch to PHP_Depend's
  official GitHub repository, if your development environment still
  relies on the old Subversion repository.

The main part of PHP_Depend's development already happens in a `Git`__
repository since Februar 2009. This means that the development happens
locally and is then pushed to a public master repository. For backward
compatibility we have synced the previous `Subversion`__ repository with
Git checkout, but now we have decided to stop the sometimes really painful
Subversion support. At least now on you should switch to PHP_Depend's 
official `GitHub`__ repository, if your development environment still 
relies on the old Subversion repository. 

.. class:: shell

::

  ~ $ git clone git://github.com/pdepend/pdepend.git

To make this hard cut in the used infrastructure as smooth as possible for
all users of PHP_Depend we have decided to keep the old Subversion `urls`__
alive through GitHub's `svn support`__. But you should keep in mind that

.. class:: shell

::

  ~ $ svn co http://svn.pdepend.org/trunk

and

.. class:: shell

::

  ~ $ svn co http://svn.pdepend.org/tags/0.9.19

will all map to PHP_Depend's Master branch on GitHub.

__ http://git-scm.org
__ http://subversion.apache.org
__ http://github.com/pdepend/pdepend
__ http://svn.pdepend.org
__ http://github.com/blog/626-announcing-svn-support

