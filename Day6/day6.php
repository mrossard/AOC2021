<?php

$fishes = explode(',', file($argv[1])[0]);
$nbDays = $argv[2];

$sums = [];

foreach($fishes as $fish){
    $sums[$fish]  = ($sums[$fish]??0) +1;
}

for($day=1; $day <= $nbDays; $day++){

    for($i = 0; $i < 8; $i++){
        $newSums[$i] = $sums[$i+1] ?? 0;
    }

    $newSums[6] += ($sums[0] ?? 0);
    $newSums[8] = $sums[0] ?? 0;

    $sums = $newSums;
}

$total = 0;
foreach($sums as $sum){
    $total += $sum;
}

echo "NB Fishes : ", $total, PHP_EOL;