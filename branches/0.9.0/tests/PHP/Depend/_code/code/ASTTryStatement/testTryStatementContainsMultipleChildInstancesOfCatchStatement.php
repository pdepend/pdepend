<?php
function testTryStatementContainsMultipleChildInstancesOfCatchStatement()
{
    try {
        fooBar();
    } catch (OutOfBoundsException $e) {

    } catch (OutOfRangeException $e) {

    } catch (DomainException $e) {

    } catch (Exception $e) {
        
    }
}