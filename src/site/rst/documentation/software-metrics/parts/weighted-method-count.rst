:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This article describes the WMC - Weighted Method per Class metric
               in general, as it was defined by Chidamber & Kemerer in their IEEE
               paper that was published 1994 and the concrete WMC implementation
               used by PHP_Depend.
:Keywords:     WMC, Cyclomatic Complexity, Weighted Method per Class, Chidamber & Kemerer

The *Weighted Method Count* or *Weighted Method per Class* metric was orginally
defined in A Metrics Suite for Object Oriented Design by Chidamber & Kemerer
[#ckoom]_.

The WMC metric is defined as the sum of complexities of all methods declared in
a class. This metric is a good indicator how much effort will be necessary to
maintain and develop a particular class. There are three slightly different 
definitions of the WMC, where each definition uses another metric as a measure
of the methods' complexity. Possible complexity values are:

* McCabe's Cyclomatic Complexity [#mcccn]_
* Lines of Code
* 1 (Number Of Methods or Unweighted WMC)

PHP_Depend uses the sum of `Cyclomatic Complexity Numbers`__ of all methods
and constructors declared in a class to calculate the WMC metric. A lower WMC
usually indicates to a class with bettwer abstraction and polymorphism. While
a class with a high complexity value is a good indicator that it this class is 
very application specific and does more than one job, and therefore harder to
test, reuse and maintain.

Thresholds
----------

An appropriate threshold for the WMC lower limit is 1, because a class should
at least consist of one method. An upper limit for the WMC of a class is harder
to define and you can find several different suggestions in the literature. But
it seems that an upper limit of 50 is a good reference point for most projects
that start to use the Weighted Method Count metric.

Bibliography
------------

.. [#ckoom] http://www.iiitd.ac.in/PhD2010/papers/SW_Paper2.pdf

  IEEE Transactions on Software Engineering, A Metrics Suite for Object
  Oriented Design, Chidamber & Kemerer, 1994

.. [#mcccn] http://www.literateprogramming.com/mccabe.pdf

  IEEE Transactions on Software Enginerring, A Complexity Measure,
  Thomas McCabe, 1976

__ /documentation/software-metrics/cyclomatic-complexity.html
