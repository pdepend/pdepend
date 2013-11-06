<?php
function testCalculatesExpectedLLocForTryCatchStatement($obj)
{
    try {
        $obj->foo();
    } catch (OutOfBoundsException $e) {
        error_log($e->getMessage());
    } catch (DomainException $e) {
        log_error($e->getMessage());
    } catch (RuntimeException $e) {
        throw new ErrorException($e->getMessage());
    }
}