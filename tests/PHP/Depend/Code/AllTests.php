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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTAllocationExpressionTest.php';
require_once dirname(__FILE__) . '/ASTArgumentsTest.php';
require_once dirname(__FILE__) . '/ASTArrayIndexExpressionTest.php';
require_once dirname(__FILE__) . '/ASTArrayTypeTest.php';
require_once dirname(__FILE__) . '/ASTAssignmentExpressionTest.php';
require_once dirname(__FILE__) . '/ASTBooleanAndExpressionTest.php';
require_once dirname(__FILE__) . '/ASTBooleanOrExpressionTest.php';
require_once dirname(__FILE__) . '/ASTBreakStatementTest.php';
require_once dirname(__FILE__) . '/ASTCastExpressionTest.php';
require_once dirname(__FILE__) . '/ASTCatchStatementTest.php';
require_once dirname(__FILE__) . '/ASTClassOrInterfaceReferenceTest.php';
require_once dirname(__FILE__) . '/ASTClassReferenceTest.php';
require_once dirname(__FILE__) . '/ASTCloneExpressionTest.php';
require_once dirname(__FILE__) . '/ASTClosureTest.php';
require_once dirname(__FILE__) . '/ASTCommentTest.php';
require_once dirname(__FILE__) . '/ASTCompoundExpressionTest.php';
require_once dirname(__FILE__) . '/ASTCompoundVariableTest.php';
require_once dirname(__FILE__) . '/ASTConditionalExpressionTest.php';
require_once dirname(__FILE__) . '/ASTConstantDeclaratorTest.php';
require_once dirname(__FILE__) . '/ASTConstantDefinitionTest.php';
require_once dirname(__FILE__) . '/ASTConstantPostfixTest.php';
require_once dirname(__FILE__) . '/ASTConstantTest.php';
require_once dirname(__FILE__) . '/ASTContinueStatementTest.php';
require_once dirname(__FILE__) . '/ASTDeclareStatementTest.php';
require_once dirname(__FILE__) . '/ASTDoWhileStatementTest.php';
require_once dirname(__FILE__) . '/ASTEchoStatementTest.php';
require_once dirname(__FILE__) . '/ASTElseIfStatementTest.php';
require_once dirname(__FILE__) . '/ASTEvalExpressionTest.php';
require_once dirname(__FILE__) . '/ASTExitExpressionTest.php';
require_once dirname(__FILE__) . '/ASTExpressionTest.php';
require_once dirname(__FILE__) . '/ASTFieldDeclarationTest.php';
require_once dirname(__FILE__) . '/ASTForInitTest.php';
require_once dirname(__FILE__) . '/ASTForUpdateTest.php';
require_once dirname(__FILE__) . '/ASTForStatementTest.php';
require_once dirname(__FILE__) . '/ASTForeachStatementTest.php';
require_once dirname(__FILE__) . '/ASTFormalParameterTest.php';
require_once dirname(__FILE__) . '/ASTFormalParametersTest.php';
require_once dirname(__FILE__) . '/ASTFunctionPostfixTest.php';
require_once dirname(__FILE__) . '/ASTGlobalStatementTest.php';
require_once dirname(__FILE__) . '/ASTGotoStatementTest.php';
require_once dirname(__FILE__) . '/ASTHeredocTest.php';
require_once dirname(__FILE__) . '/ASTIdentifierTest.php';
require_once dirname(__FILE__) . '/ASTIfStatementTest.php';
require_once dirname(__FILE__) . '/ASTIncludeExpressionTest.php';
require_once dirname(__FILE__) . '/ASTInstanceOfExpressionTest.php';
require_once dirname(__FILE__) . '/ASTIssetExpressionTest.php';
require_once dirname(__FILE__) . '/ASTLabelStatementTest.php';
require_once dirname(__FILE__) . '/ASTListExpressionTest.php';
require_once dirname(__FILE__) . '/ASTLiteralTest.php';
require_once dirname(__FILE__) . '/ASTLogicalAndExpressionTest.php';
require_once dirname(__FILE__) . '/ASTLogicalOrExpressionTest.php';
require_once dirname(__FILE__) . '/ASTLogicalXorExpressionTest.php';
require_once dirname(__FILE__) . '/ASTMemberPrimaryPrefixTest.php';
require_once dirname(__FILE__) . '/ASTMethodPostfixTest.php';
require_once dirname(__FILE__) . '/ASTParentReferenceTest.php';
require_once dirname(__FILE__) . '/ASTPostfixExpressionTest.php';
require_once dirname(__FILE__) . '/ASTPreDecrementExpressionTest.php';
require_once dirname(__FILE__) . '/ASTPreIncrementExpressionTest.php';
require_once dirname(__FILE__) . '/ASTPrimitiveTypeTest.php';
require_once dirname(__FILE__) . '/ASTPropertyPostfixTest.php';
require_once dirname(__FILE__) . '/ASTRequireExpressionTest.php';
require_once dirname(__FILE__) . '/ASTReturnStatementTest.php';
require_once dirname(__FILE__) . '/ASTScopeStatementTest.php';
require_once dirname(__FILE__) . '/ASTScopeTest.php';
require_once dirname(__FILE__) . '/ASTSelfReferenceTest.php';
require_once dirname(__FILE__) . '/ASTStatementTest.php';
require_once dirname(__FILE__) . '/ASTStaticReferenceTest.php';
require_once dirname(__FILE__) . '/ASTStaticVariableDeclarationTest.php';
require_once dirname(__FILE__) . '/ASTStringTest.php';
require_once dirname(__FILE__) . '/ASTStringIndexExpressionTest.php';
require_once dirname(__FILE__) . '/ASTSwitchStatementTest.php';
require_once dirname(__FILE__) . '/ASTSwitchLabelTest.php';
require_once dirname(__FILE__) . '/ASTThrowStatementTest.php';
require_once dirname(__FILE__) . '/ASTTryStatementTest.php';
require_once dirname(__FILE__) . '/ASTTypeNodeTest.php';
require_once dirname(__FILE__) . '/ASTUnaryExpressionTest.php';
require_once dirname(__FILE__) . '/ASTUnsetStatementTest.php';
require_once dirname(__FILE__) . '/ASTVariableTest.php';
require_once dirname(__FILE__) . '/ASTVariableDeclaratorTest.php';
require_once dirname(__FILE__) . '/ASTVariableVariableTest.php';
require_once dirname(__FILE__) . '/ASTWhileStatementTest.php';

require_once dirname(__FILE__) . '/CommonASTNodeTest.php';
require_once dirname(__FILE__) . '/CommonCallableTest.php';
require_once dirname(__FILE__) . '/CommonItemTest.php';

require_once dirname(__FILE__) . '/ClassTest.php';
require_once dirname(__FILE__) . '/FileTest.php';
require_once dirname(__FILE__) . '/FunctionTest.php';
require_once dirname(__FILE__) . '/InterfaceTest.php';
require_once dirname(__FILE__) . '/MethodTest.php';
require_once dirname(__FILE__) . '/NodeIteratorTest.php';
require_once dirname(__FILE__) . '/PackageTest.php';
require_once dirname(__FILE__) . '/ParameterTest.php';
require_once dirname(__FILE__) . '/PropertyTest.php';

require_once dirname(__FILE__) . '/ReflectionClassTest.php';
require_once dirname(__FILE__) . '/ReflectionParameterTest.php';
require_once dirname(__FILE__) . '/ReflectionPropertyTest.php';

require_once dirname(__FILE__) . '/Filter/AllTests.php';

/**
 * Main test suite for the PHP_Depend_Code package.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Code_AllTests
{
    /**
     * Creates the phpunit test suite for this package.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHP_Depend_Code - AllTests');
        
        $suite->addTest(PHP_Depend_Code_Filter_AllTests::suite());

        $suite->addTestSuite('PHP_Depend_Code_CommonASTNodeTest');
        $suite->addTestSuite('PHP_Depend_Code_CommonCallableTest');
        $suite->addTestSuite('PHP_Depend_Code_CommonItemTest');

        $suite->addTestSuite('PHP_Depend_Code_ClassTest');
        $suite->addTestSuite('PHP_Depend_Code_FileTest');
        $suite->addTestSuite('PHP_Depend_Code_FunctionTest');
        $suite->addTestSuite('PHP_Depend_Code_InterfaceTest');
        $suite->addTestSuite('PHP_Depend_Code_MethodTest');
        $suite->addTestSuite('PHP_Depend_Code_NodeIteratorTest');
        $suite->addTestSuite('PHP_Depend_Code_PackageTest');
        $suite->addTestSuite('PHP_Depend_Code_PropertyTest');
        $suite->addTestSuite('PHP_Depend_Code_ParameterTest');

        $suite->addTestSuite('PHP_Depend_Code_ASTAllocationExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTArgumentsTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTArrayIndexExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTArrayTypeTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTAssignmentExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTBooleanAndExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTBooleanOrExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTBreakStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTCastExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTCatchStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTClassOrInterfaceReferenceTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTClassReferenceTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTCloneExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTClosureTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTCommentTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTCompoundExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTCompoundVariableTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTConditionalExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTConstantDeclaratorTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTConstantDefinitionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTConstantPostfixTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTConstantTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTContinueStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTDoWhileStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTDeclareStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTEchoStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTElseIfStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTEvalExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTExitExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTFieldDeclarationTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTForInitTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTForUpdateTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTForStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTForeachStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTFormalParametersTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTFormalParameterTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTFunctionPostfixTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTGlobalStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTGotoStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTHeredocTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTIdentifierTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTIfStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTIncludeExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTInstanceOfExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTIssetExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTLiteralTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTLabelStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTListExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTLogicalAndExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTLogicalOrExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTLogicalXorExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTMemberPrimaryPrefixTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTMethodPostfixTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTParentReferenceTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTPostfixExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTPreDecrementExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTPreIncrementExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTPrimitiveTypeTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTPropertyPostfixTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTRequireExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTReturnStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTScopeStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTScopeTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTSelfReferenceTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTStaticReferenceTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTStaticVariableDeclarationTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTStringTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTStringIndexExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTSwitchStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTSwitchLabelTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTThrowStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTTryStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTTypeNodeTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTUnaryExpressionTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTUnsetStatementTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTVariableTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTVariableDeclaratorTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTVariableVariableTest');
        $suite->addTestSuite('PHP_Depend_Code_ASTWhileStatementTest');

        $suite->addTestSuite('PHP_Depend_Code_ReflectionClassTest');
        $suite->addTestSuite('PHP_Depend_Code_ReflectionParameterTest');
        $suite->addTestSuite('PHP_Depend_Code_ReflectionPropertyTest');

        return $suite;
    }
}