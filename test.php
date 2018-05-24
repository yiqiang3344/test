<?php
$a = 1;
$b = $a;
$c = &$a;
$d = $c;
$e = &$c;
$e = 2;
echo $a, "\n", $b, "\n", $c, "\n", $d, "\n", $e, "\n";


