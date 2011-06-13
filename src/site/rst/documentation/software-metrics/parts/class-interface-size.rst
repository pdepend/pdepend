:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This article describes the CIS - Class Interface Size metric in
               general. This metric was orginally defined in the Quality Model of
               Object-Oriented Design by Bansiya & Davis. This metric is a good
               indicator for the choosen software design and how reusable components
               are implemented.
:Keywords:     CIS, Class Interface Size, QMOOD, Quality Metrics of Object-Oriented Design, Bansiya & Davis, reusability, functionality

The *Class Interface Size* or *CIS* metric is measure of the public services 
that a class provides. This metric was orginally defined in the *QMOOD* model
[#moodcis]_ by Bansiya & Davis.

The orginal version of the *CIS* metric was defined as the number of public
methods that a class provides. Each of these methods can be seen as a service
where surrounding application can send messages to or receive messages from
a class.

  **CIS = public(NOM)**

A newer variant of the *CIS* metric also includes the public attributes of a
class, because theses properties can also be used to transport messages or 
information between a class and the surrounding application.

  **CIS = public(NOM) + public(VARS)**

PHP_Depend uses the second variant and counts all public methods and attributes
declared in a class to calculate its *Class Interface Size* metric.

This metric is a good indicator for the choosen software design. Several 
classes with a high *CIS* value are a sure sign that the design of the
analyzed software prefers composition over inheritance to share common
functionality between different components. So in most cases a high value is
a good a sign, because composition increases the reusability and flexibility.
But there are also situations where wrongly used composition of functionality
leads to a design that is harder to understand and maintain.

Thresholds
----------

It is not easy to define good thresholds for this metric, because those values
heavy depend on the choosen design, e.g. inheritance or composition. But in
generall we can say that is best practice to limit the public interface that
can be used to alter the internal state of an object. Therefore we suggest 20
as a reference point for the upper limit.

Bibliography
------------

.. [#moodcis] http://www.ptidej.net/teaching/inf6306/fall09/notes/course4/Bansiya02-QualityModel.pdf

  IEEE Transactions on Software Enginerring; *Hierarchical Model for Object-Oriented
  Design Quality Assessment*; Bansiya & Davis; 2002
