:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This article describes the CE - Efferent Coupling metric that
               describes the dependencies a software artifact has onto other
               artifacts in the same software system. The content of this
               article is based on UncleBob's whitepaper "OO Design Quality
               Metrics - An Analysis of Dependencies".
:Keywords:     CE, Efferent Coupling, UncleBob, Robert C. Martin, Class Coupling, Package Coupling, Maintainability, Testing, Stability

The *Efferent Coupling* or *CE* counts the number of software artifacts a
software entity depends on. Therefore it takes all artifacts the entity 
depends on and builds a unique set for these dependencies. This means that
if there are N dependencies from ``A`` to ``B`` and M dependencies from ``A`` 
to ``C``, the *Efferent Coupling* of ``A`` is ``2``. Because we like and 
force reuse of existing code a high value for this metric isn't that bad at 
a first glance. But what does a high *Efferent Coupling* value mean beside
code reuse? It means that a component depends on several other implementation
details and this makes the component itself instable, because an incompatible
change between two versions or a switch to a different library will/may break
the component. Therefore it is good pratice to keep the *Efferent Coupling* 
for all artifacts at a minimum.

A good example for such an everything breaking change is normally the switch
from one database abstraction to another. In most applications there are so
many dependencies between the concrete application code and different packages
in the library code, that a switch from product *A* to product *B* will break
several parts in the application. Therefore it is best practice to monitor the
*Efferent Coupling* and to refactor all those artifacts where the value is
extremly high. A good solution to high *Efferent Coupling* is the introduction
of additional abstraction. For the given example this would mean, that you add
an additional package which abstracts the concrete product vendor and then you
derive concrete implementations from this abstract package.

A detailed description of the *CE* metric, it's impact on the stability of a
software system and the introduction of more abstraction can be found in 
UncleBob's whitepaper "OO Design Quality Metrics - An Analysis of
Dependencies" [#ubdqm]_.

Thresholds
----------

It is really hard to give a threshold for the *Efferent Coupling* metric and
the only answer can be **It depends**. Normally you should try to keep the 
*Efferent Coupling* at a minimum, and the more abstract and lowlevel a
component is, the less the value should be.

See also
--------

- `Coupling Between Objects`__: *Coupling Between Objects* or *CBO* is a 
  different name for the same metric, that is frequently used in the
  literatur.

- `Afferent Coupling`__: The *Afferent Coupling* or *CA* is a metric that
  calculates the reverse coupling of a class.

Bibliography
------------

.. [#ubdqm] http://www.objectmentor.com/publications/oodmetrc.pdf

  Object Mentor; OO Design Quality Metrics - An Analysis of Dependencies;
  Robert C. Martin; 1994

__ /documentation/software-metrics/coupling-between-objects.html
__ /documentation/software-metrics/afferent-coupling.html

