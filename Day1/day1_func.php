<?php

function inc($number) : int
{
    return $number + 1;
}

$x = [1, 2, 3, 4];
array_map('inc', $x);
var_dump($x);