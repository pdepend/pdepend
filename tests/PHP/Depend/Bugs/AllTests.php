<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/InvalidResultWhenFunctionReturnsByReferenceBug004Test.php';
require_once dirname(__FILE__) . '/InstanceOfExpressionReferenceHandlingBug062Test.php';
require_once dirname(__FILE__) . '/ClassDeclarationWithoutBodyBug065Test.php';
require_once dirname(__FILE__) . '/IncorrectPropertyEndlineBug068Test.php';
require_once dirname(__FILE__) . '/ClosureResultsInExceptionBug070Test.php';
require_once dirname(__FILE__) . '/SignedDefaultValueResultsInExceptionBug071Test.php';
require_once dirname(__FILE__) . '/InconsistentObjectGraphBug073Test.php';
require_once dirname(__FILE__) . '/ParserKeywordAsConstantNameBug76Test.php';
require_once dirname(__FILE__) . '/TokenizerKeywordSubstitutionBug76Test.php';
require_once dirname(__FILE__) . '/SupportCommaSeparatedPropertyDeclarationsBug081Test.php';
require_once dirname(__FILE__) . '/SupportCommaSeparatedConstantDefinitionsBug082Test.php';
require_once dirname(__FILE__) . '/ParentKeywordAsParameterTypeHintBug087Test.php';
require_once dirname(__FILE__) . '/WrongCouplingAnalyzerForCommentsBug089Test.php';
require_once dirname(__FILE__) . '/NamespaceChainsNotHandledCorrectByCouplingAnalyzerBug090Test.php';
require_once dirname(__FILE__) . '/ClassConstantAsArrayDefaultValueResultsInExceptionBug091Test.php';
require_once dirname(__FILE__) . '/ClosureReturnsByReferenceBug094Test.php';
require_once dirname(__FILE__) . '/NPathComplexityIsBrokenInVersion096Bug095Test.php';
require_once dirname(__FILE__) . '/DefaultPackageContainsBrokenAritfactsBug098Test.php';
require_once dirname(__FILE__) . '/ParserSetsIncorrectStartLineBug101Test.php';
require_once dirname(__FILE__) . '/NamespaceKeywordInParameterTypeHintBug102Test.php';
require_once dirname(__FILE__) . '/ParameterStringDefaultValueBug103Test.php';
require_once dirname(__FILE__) . '/DefaultNamespaceBug106Test.php';
require_once dirname(__FILE__) . '/ComplexStringParsingBug114Test.php';
require_once dirname(__FILE__) . '/SummaryReportContainsClassesWithoutSourceFileBug115Test.php';
require_once dirname(__FILE__) . '/KeywordFunctionNameResultsInExceptionBug116Test.php';
require_once dirname(__FILE__) . '/MethodsDeclaredAbstractAreCountedAsOverwrittenBug118Test.php';
require_once dirname(__FILE__) . '/VariableVariablesInForeachStatementBug128Test.php';
require_once dirname(__FILE__) . '/ReconfigureXdebugMaxNestingLevelBug133Test.php';
require_once dirname(__FILE__) . '/EmptyExceptionMessageInPHP52HelperBug149Test.php';
require_once dirname(__FILE__) . '/InvalidNowdocSubstitutionBug150Test.php';
require_once dirname(__FILE__) . '/EndLessLoopBetweenForParentClassBug152Test.php';
require_once dirname(__FILE__) . '/InvalidTokenObjectOperatorInForeachLoopBug161Test.php';
require_once dirname(__FILE__) . '/StringWithDollarStringLiteralBug162Test.php';
require_once dirname(__FILE__) . '/AlternativeSyntaxClosingTagBug163Test.php';
require_once dirname(__FILE__) . '/InputIteratorShouldOnlyFilterOnLocalPathBug164Test.php';
require_once dirname(__FILE__) . '/ClassAndInterfaceNamesBug169Test.php';
require_once dirname(__FILE__) . '/ClassInterfaceSizeShouldNotSumComplexityBug176Test.php';
require_once dirname(__FILE__) . '/UnexpectedTokenAsciiChar39Bug181Test.php';
require_once dirname(__FILE__) . '/CloneIsValidNameInOlderPhpVersionsBug182Test.php';
require_once dirname(__FILE__) . '/ExcludePathFilterShouldFilterByAbsolutePathBug191Test.php';

/**
 * Test suite for bugs meta package.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Bugs_AllTests
{
    /**
     * Creates the phpunit test suite for this package.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHP_Depend_Bugs - AllTests');

        $suite->addTestSuite('PHP_Depend_Bugs_InvalidResultWhenFunctionReturnsByReferenceBug004Test');
        $suite->addTestSuite('PHP_Depend_Bugs_InstanceOfExpressionReferenceHandlingBug062Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ClassDeclarationWithoutBodyBug065Test');
        $suite->addTestSuite('PHP_Depend_Bugs_IncorrectPropertyEndlineBug068Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ClosureResultsInExceptionBug070Test');
        $suite->addTestSuite('PHP_Depend_Bugs_SignedDefaultValueResultsInExceptionBug071Test');
        $suite->addTestSuite('PHP_Depend_Bugs_InconsistentObjectGraphBug073Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ParserKeywordAsConstantNameBug76Test');
        $suite->addTestSuite('PHP_Depend_Bugs_TokenizerKeywordSubstitutionBug76Test');
        $suite->addTestSuite('PHP_Depend_Bugs_SupportCommaSeparatedPropertyDeclarationsBug081Test');
        $suite->addTestSuite('PHP_Depend_Bugs_SupportCommaSeparatedConstantDefinitionsBug082Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ParentKeywordAsParameterTypeHintBug087Test');
        $suite->addTestSuite('PHP_Depend_Bugs_WrongCouplingAnalyzerForCommentsBug089Test');
        $suite->addTestSuite('PHP_Depend_Bugs_NamespaceChainsNotHandledCorrectByCouplingAnalyzerBug090Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ClassConstantAsArrayDefaultValueResultsInExceptionBug091Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ClosureReturnsByReferenceBug094Test');
        $suite->addTestSuite('PHP_Depend_Bugs_NPathComplexityIsBrokenInVersion096Bug095Test');
        $suite->addTestSuite('PHP_Depend_Bugs_DefaultPackageContainsBrokenAritfactsBug098Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ParserSetsIncorrectStartLineBug101Test');
        $suite->addTestSuite('PHP_Depend_Bugs_NamespaceKeywordInParameterTypeHintBug102Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ParameterStringDefaultValueBug103Test');
        $suite->addTestSuite('PHP_Depend_Bugs_DefaultNamespaceBug106Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ComplexStringParsingBug114Test');
        $suite->addTestSuite('PHP_Depend_Bugs_SummaryReportContainsClassesWithoutSourceFileBug115Test');
        $suite->addTestSuite('PHP_Depend_Bugs_KeywordFunctionNameResultsInExceptionBug116Test');
        $suite->addTestSuite('PHP_Depend_Bugs_MethodsDeclaredAbstractAreCountedAsOverwrittenBug118Test');
        $suite->addTestSuite('PHP_Depend_Bugs_VariableVariablesInForeachStatementBug128Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ReconfigureXdebugMaxNestingLevelBug133Test');
        $suite->addTestSuite('PHP_Depend_Bugs_EmptyExceptionMessageInPHP52HelperBug149Test');
        $suite->addTestSuite('PHP_Depend_Bugs_InvalidNowdocSubstitutionBug150Test');
        $suite->addTestSuite('PHP_Depend_Bugs_EndLessLoopBetweenForParentClassBug152Test');
        $suite->addTestSuite('PHP_Depend_Bugs_InvalidTokenObjectOperatorInForeachLoopBug161Test');
        $suite->addTestSuite('PHP_Depend_Bugs_StringWithDollarStringLiteralBug162Test');
        $suite->addTestSuite('PHP_Depend_Bug_AlternativeSyntaxClosingTagBug163Test');
        $suite->addTestSuite('PHP_Depend_Bugs_InputIteratorShouldOnlyFilterOnLocalPathBug164Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ClassAndInterfaceNamesBug169Test');
        $suite->addTestSuite('PHP_Depend_Bugs_ClassInterfaceSizeShouldNotSumComplexityBug176Test');
        $suite->addTestSuite('PHP_Depend_Bugs_UnexpectedTokenAsciiChar39Bug181Test');
        $suite->addTestSuite('PHP_Depend_Bugs_CloneIsValidNameInOlderPhpVersionsBug182Test');
        $suite->addTestSuite('PHP_Depend_Input_ExcludePathFilterShouldFilterByAbsolutePathBug191Test');

        return $suite;
    }
}
