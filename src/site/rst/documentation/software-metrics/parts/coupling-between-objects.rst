:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This article describes the CBO - Coupling Between Objects metric
               that gives information how strong a class is coupled with its
               surrounding software system. This metric was originally 
               described by Chidamber & Kemerer in their IEEE paper - A Metrics
               Suite for Object Oriented Design.
:Keywords:     CBO, Coupling Between Objects, Chidamber, Kemerer, Class Coupling, Maintainability, Testing

The *Coupling Between Objects* or *CBO* metric was originally defined by
Chidamber & Kemerer in their IEEE paper "A Metrics  Suite for Object Oriented
Design" [#ckoom]_. This software metric represents the number of other
types a class or interface is coupled to. The *CBO* metric is calculated for
classes and interfaces. It counts the unique number of reference types that
occur through method calls, method parameters, return types, thrown exceptions
and accessed fields. But there is an exception for types that are either a
subtype or supertype of the given class, because these types are not included
in the calculated *CBO* value. 

Excessive coupled classes prevent reuse of existing components and they are
damaging for a modular, encapsulated software design. To improve the modularity
of a software the inter coupling between different classes should be kept to a
minimum. Beside reusability a high coupling has a second drawback, a class that
is coupled to other classes is sensitive to changes in that classes and as a 
result it becomes more difficult to maintain and gets more error-prone.
Additionally it is harder to test a heavly coupled class in isolation and it is
harder to understand such a class. Therefore you should keep the number of
dependencies at a minimum.

Thresholds
----------

Based on their research Sahraoui, Godin and Miceli [#sgmqa]_ suggest a maximum
*CBO* value of *14*, because higher values have negative impacts on several
quality aspects of a class, which includes the maintainability, stability and
understandability.

See also
--------

- `Efferent Coupling`__: *Efferent Coupling* or *CE* is a different name for
  the same metric, that is frequently used in the literatur.

- `Afferent Coupling`__: The *Afferent Coupling* or *CA* is a metric that
  calculates the reverse coupling of a class.

Bibliography
------------

.. [#ckoom] http://www.iiitd.ac.in/PhD2010/papers/SW_Paper2.pdf

  IEEE Transactions on Software Engineering; A Metrics Suite for Object
  Oriented Design; Chidamber & Kemerer, 1994

.. [#sgmqa] http://www.iro.umontreal.ca/~sahraouh/papers/ICSM00.pdf

  Software Maintenance, 2000. Proceedings. International Conference; Can 
  metrics help to bridge the gap between the improvement of OO design quality
  and its automation?; Sahraoui, Godin, Miceli; 2000

__ /documentation/software-metrics/efferent-coupling.html
__ /documentation/software-metrics/afferent-coupling.html

