<?php
class PHP_Depend_Bug24
{
    function testBug24()
    {
        ?>
        <form action="<?=DOC_ROOT; ?>">
          <input type="text" name="name" value="<?php echo htmlentities($foo); ?>" />
        </form>
        <?php
    }
    
    function testMethod()
    {
        
    }
}