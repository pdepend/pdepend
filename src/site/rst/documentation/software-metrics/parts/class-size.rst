:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This article describes the CSZ - Class Size metric as it is defined
               in the literature and how it is implemented in PHP_Depend. Later in
               this article we describe the impact of the class size on several
               quality-factors in a software system.
:Keywords:     CSZ, CS, Class Size, Khaled et al., Optiomal Class Size, SRP, Single Responsibility Principle, testability, responsibility, maintainability

The *Class Size* or *CSZ* metric is a another measure for the complexity and
size of a class. In many publications and books this metric is frequently
referenced with the abbreviation *CS*. The definition and detailed description
of this metric can be found in *The Optimal Class Size for Object-Oriented 
Software* paper by Khaled, Benlarbi, Nishith & Shesh [#ooocsz]_.

There are three different definitions of the *Class Size* metric, two of them
are only a different name for already existing metrics. The first one takes the
number of statements, respectively the *Logical Lines Of Code* (*LLOC*), to
measure the size of class. The second variant uses the *Lines Of Code* (*LOC*)
as an indicator for the class size.

The third variant uses two language constructs of object-oriented progamming
languages to measure the size of class and calculates the sum of both values.
These to language constructs are methods and attributes, so that the existing
metrics *Number Of Methods* (*NOM*) and *Number Of Attributes* (*VARS*) can 
be reused to calculate the *Class Size*.

  **CSZ = NOM + VARS**

PHP_Depend implements this third variant of *Class Size* metric algorithm to
measure the size of a class.

As several researches and studies on procedual software projects have shown,
the optimal size of a software component, lies somewhere between too small
and too large, to be less fault-prone. This model can be visualized with an
u-curve, where the lower arc of the U represents the optimal size of a
component. But this model does not apply to object-orientend software systems,
as Khaled et al. have shown in their paper [#ooocsz]_. Object-oriented systems
tend to be more fault-prone when they get bigger and less fault-prone when
they use small classes and follow the Single Responsibility Principle (SRP)
[#poodsrp]_.

For the *CSZ* metric this means that smaller values are always better, than
greater ones, because a smaller size is an indicator for better testability,
good maintainability and a well defined responsibility of the class.

Thresholds
----------

Based on their research of several software systems and their reflection of
other studies about the optimal size of a software component Khaled et al.
suggest an upper limit 39 for a *Class Size* metric based on the number of
a class' methods and attributes. But as always, this threshold can only be
a clue for own limits, that fit to custom requirements.

Bibliography
------------

.. [#ooocsz] http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.94.7296&rep=rep1&type=pdf

  IEEE Transactions on Softare Engineering; The Optimal Class Size for
  Object-Oriented Software; Khaled, Benlarbi, Nishith & Shesh; 2002

.. [#poodsrp] http://www.objectmentor.com/resources/articles/srp.pdf

  ButUncleBob.com; The Single Responsibility Principle; Robert C. Martin
