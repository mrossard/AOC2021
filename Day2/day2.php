<?php

$input = file('input.txt');

$x=0; $y=0; $aim=0;
foreach($input as $line){
    list($instruction, $units) = explode(' ', $line);
    if($instruction == 'forward'){
        $y += $aim * (int) $units;
        $x += (int) $units;
    }
    else{
        if($instruction == 'down'){
            $aim += (int) $units;
        }
        else{
            $aim -= (int) $units;
        }
    }
}

echo $x * $y;