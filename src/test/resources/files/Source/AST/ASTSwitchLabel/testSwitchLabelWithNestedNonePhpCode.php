<?php 
function testSwitchLabelWithNestedNonePhpCode()
{
?>
<?php switch($var): ?>
<?php case 'one': ?>
    var a = "one";
    <?php break; ?>
<?php case 'two': ?>
    var a = "two";
    <?php break; ?>
<?php case 'three': ?>
<?php default: ?>
    var a = "three";
    <?php break; ?>
<?php endswitch; ?>
<?php
}
?>
