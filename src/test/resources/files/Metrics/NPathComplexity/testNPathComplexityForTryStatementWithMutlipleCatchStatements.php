<?php
class testNPathComplexityForTryStatementWithMutlipleCatchStatements
{
    function testNPathComplexityForTryStatementWithMutlipleCatchStatements()
    {
        try {
        } catch (E1 $e) {
        } catch (E2 $e) {
        } catch (E3 $e) {
        } catch (E4 $e) {
        }
    }
}