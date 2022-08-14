<?php
$foo = [['bug' => 'test']];
$key = 'bug';
foreach ($foo as [$key => $bar]) {
    var_dump($bar);
}
