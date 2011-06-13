The *Cyclomatic Complexity* number [#cabeccn]_ or short *CCN*
is one of the oldest complexity metrics. The first time this
software metric was mentioned was 1976 by Thomas J. McCabe.
This metric counts the available decision paths in a software
fragment to determine its complexity. Each decision path
starts with one of the conditional statements from the
following list, so that it is fairly easy to detect them in
existing source code.

* ?
* case
* elseif
* for
* foreach
* if
* while

A look at this list of statements may result in the questions:
Is this list wrong, it doesn't list ``else`` and ``default``?
But it is correct. The assumption is that both statements
will contain the defaut execution path of a program which 
also exists when there are no special cases to capture.

Each decision path gets the value *1* and the sum of all these
values represents the Cyclomatic Complexity of the analyzed
software fragment. Note that each function and method also 
counts with a value of *1* With this knowlegde we can now 
calculate the complexity of the following example code: ::

  <?php                                 
  //                                                         | CCN
  // -------------------------------------------------------------
  class CyclomaticComplexityNumber                        // |  0
  {                                                       // |  0
      public function exampe( $x, $y )                    // |  1
      {                                                   // |  0
          if ( $x > 23 || $y < 42 )                       // |  1
          {                                               // |  0
              for ( $i = $x; $i >= $x && $i <= $y; ++$i ) // |  1
              {                                           // |  0
              }                                           // |  0
          }                                               // |  0
          else                                            // |  0
          {                                               // |  0
              switch ( $x + $y )                          // |  0
              {                                           // |  0
                  case 1:                                 // |  1
                      break;                              // |  0
                  case 2:                                 // |  1
                      break;                              // |  0
                  default:                                // |  0
                      break;                              // |  0
              }                                           // |  0
          }                                               // |  0
      }                                                   // |  0
      file_exists('/tmp/log') or touch('/tmp/log');       // |  0
  }                                                       // |  0
  // -------------------------------------------------------------
  //                                                         |  5

Based on the previous definition the Cyclomatic Complexity 
Number of the example code example is *5*. But you may have
noticed that this approach does not capture all decision paths
that exist. We haven't catched those paths that came from the
by the boolean expression ``||`` line 8 and ``&&`` line 10, and 
the logical ``or`` expression in line 27. A variation of the
Cyclomatic Complexity Number that also captures those paths
is the so called CCN2. The CCN2 is the most widely used
variation of this software metrics. Tools like PHPUnit, PMD
and Checkstyle report it as Cyclomatic Complexity of an
analyzed software fragment.

Now we get a complexity value of *8* when we apply the CCN2 
to the previous example, what is a growt of the software's 
complexity of 60%.
 
Due to the fact that Cyclomatic Complexity Number was 
originally invented for procedural programming languages, 
this definition for the Cyclomatic Complexity Number still 
misses one element to measure the complexity of an object 
oriented software system. With the concept of exceptions a 
software gets additional decision paths for each ``catch``
statement used in the source code. While ``try`` contains 
the code for the regular execution code without special 
cases, similar to ``else`` and ``default`` statements.

* ?
* &&
* ||
* or
* and
* xor
* case
* catch
* elseif
* for 
* foreach
* if
* while

Now that we know what the Cyclomatic Complexity Number is,
what can we do with the measured information? We can find 
the complexity hotspots in a system, for example the top 
ten artifacts with the highest complexity, but this is only
important during an initial analyses phase to get the big 
picture of an application. For a continuous inspection this
information is not so important. A continuous analyses
requires thresholds that help to categories calculated 
values. During the time four values have emerged as good 
thresholds for the Cyclomatic Complexity Number of a 
software system.

* A software fragment with a CCN value between *1-4* has 
  low complexity.
* A complexity value between *5-7* is moderate and still 
  easy to understand.
* Everything between *6-10* has a high complexity, while
  everything greater *10* is very complex and hard to 
  understand.

You may ask, why should I care about the complexity of a
software system, where is the value of benefit in this
metric?

Mostly the complex parts of an application contain business
critical logic. But this complexity has negative impacts on 
the readability and understandability of source code. Those
parts will normally become a maintainence and bug fixing
nightmare, because no one knows all the constraints, side
effects and what's exactly going on in that part of the 
software. This situation results in the well known saying
*"Never touch a running system"* which in turn mostly ends 
in copy&paste programming. The situation can even become
more critical when the original author leaves the 
development team or the company.

Example:
Finally a small example how to apply the new knowledge 
about the Cyclomatic Complexity Number, thresholds and the
negative impacts of complex software to an existing 
development process. The following source listing shows 
a complex method taken from PHP_Depend's source. This method
has a Cyclomatic Complexity Number of *16* and I must admit
that the original author needed some time to understand what 
was going on in this method. ::

  <?php
  // ...
  private function _countCalls(PHP_Depend_Code_AbstractCallable $callable)
  {
      $callT  = array(
          PHP_Depend_TokenizerI::T_STRING,
          PHP_Depend_TokenizerI::T_VARIABLE
      );
      $chainT = array(
          PHP_Depend_TokenizerI::T_DOUBLE_COLON,
          PHP_Depend_TokenizerI::T_OBJECT_OPERATOR,
      );

      $called = array();

      $tokens = $callable->getTokens();
      $count  = count($tokens);
      for ($i = 0; $i < $count; ++$i) {
          // break on function body open
          if ($tokens[$i]->type === PHP_Depend_TokenizerI::T_CURLY_BRACE_OPEN) {
              break;
          }
      }

      for (; $i < $count; ++$i) {
          // Skip non parenthesis tokens
          if ($tokens[$i]->type !== PHP_Depend_TokenizerI::T_PARENTHESIS_OPEN) {
              continue;
          }
          // Skip first token
          if (!isset($tokens[$i - 1]) || !in_array($tokens[$i - 1]->type, $callT)) {
              continue;
          }
          // Count if no other token exists
          if (!isset($tokens[$i - 2]) && !isset($called[$tokens[$i - 1]->image])) {
              $called[$tokens[$i - 1]->image] = true;
              ++$this->_calls;
              continue;
          } else if (in_array($tokens[$i - 2]->type, $chainT)) {
              $identifier = $tokens[$i - 2]->image . $tokens[$i - 1]->image;
              for ($j = $i - 3; $j >= 0; --$j) {
                  if (!in_array($tokens[$j]->type, $callT)
                      && !in_array($tokens[$j]->type, $chainT)
                  ) {
                      break;
                  }
                  $identifier = $tokens[$j]->image . $identifier;
              }

              if (!isset($called[$identifier])) {
                  $called[$identifier] = true;
                  ++$this->_calls;
              }
          } else if ($tokens[$i - 2]->type !== PHP_Depend_TokenizerI::T_NEW
              && !isset($called[$tokens[$i - 1]->image])
          ) {
              $called[$tokens[$i - 1]->image] = true;
              ++$this->_calls;
          }
      }
  }

The first thing to do is to make sure that the test suite 
is good enough to ensure that the required refactorings 
will not change the public behavior of the component or
class. When this is donw and we are sure our that api
breaks will be detected by the test suitewe can start to
extract logic into separate methods.

The following example shows the result of the refactoring: ::

  <?php
  // ...
  private function _countCalls(PHP_Depend_Code_AbstractCallable $callable)
  {
      $called = array();

      $tokens = $callable->getTokens();
      $count  = count($tokens);
      for ($i = $this->_findOpenCurlyBrace($tokens); $i < $count; ++$i) {

          if ($this->_isCallableOpenParenthesis($tokens, $i) === false) {
              continue;
          }

          if ($this->_isMethodInvocation($tokens, $i) === true) {
              $image = $this->_getInvocationChainImage($tokens, $i);
          } else if ($this->_isFunctionInvocation($tokens, $i) === true) {
              $image = $tokens[$i - 1]->image;
          } else {
              $image = null;
          }

          if ($image !== null) {
              $called[$image] = $image;
          }
      }

      $this->_calls += count($called);
  }

The subjective feeling of readability heavily depends on the 
complexity of control structures, as we can see by a 
comparison of the original and the refactored version of the 
method example. The new version with its Cyclomatic Complexity
Number of *5* is much easier to read and understand.

.. [#cabeccn] http://www.literateprogramming.com/mccabe.pdf

  IEEE Transactions on Software Enginerring; *A Complexity Measure*;
  Thomas J. McCabe; 1976
