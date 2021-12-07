<?php

$input = explode(',', file($argv[1])[0]);
sort($input);

$count = count($input);
if($count % 2 === 0) {
    $pos = $input[$count / 2];
}
else{
    $pos = ((int) $input[floor($count / 2)] + (int) $input[floor($count / 2) +1]) / 2;
}

$cost =0;
foreach($input as $crab){
    $cost += abs($pos - (int) $crab);
}

echo 'Partie 1 : ', $pos, ' coute ', $cost, PHP_EOL;

//2
$minCost = count($input) * count($input) * count($input) * count($input);
$minPos = -1;
$cost = [];
for($pos = $input[0]; $pos <= $input[count($input)-1]; $pos++){
    $cost[$pos] = 0;
    foreach($input as $crab){
        $ecart = abs($pos - (int)$crab);
        $cost[$pos] += ($ecart *($ecart+1))/2;
    }

    if($cost[$pos] < $minCost) {
        $minCost = $cost[$pos];
        $minPos = $pos;
    }
}

echo 'Partie 2 : ', $minPos, ' coute : ', $minCost, PHP_EOL;