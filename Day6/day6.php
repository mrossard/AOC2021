<?php

$fishes = explode(',', file($argv[1])[0]);
$nbDays = $argv[2];

$sums = [];

foreach($fishes as $fish){
    $sums[$fish]  = ($sums[$fish]??0) +1;
}

echo 'Day 0 : ', PHP_EOL;
for($i = 0; $i <= 8; $i++)
    echo $i, " : ", $sums[$i]??0, PHP_EOL;

for($day=1; $day <= $nbDays; $day++){

    echo PHP_EOL, 'Day ', $day, ': ', PHP_EOL;

    $newSums[0] = $sums[1] ?? 0;
    $newSums[1] = $sums[2] ?? 0;
    $newSums[2] = $sums[3] ?? 0;
    $newSums[3] = $sums[4] ?? 0;
    $newSums[4] = $sums[5] ?? 0;
    $newSums[5] = $sums[6] ?? 0;
    $newSums[6] = ($sums[7] ?? 0) + ($sums[0] ?? 0);
    $newSums[7] = $sums[8] ?? 0;
    $newSums[8] = $sums[0] ?? 0;

    $sums = $newSums;

    for($i = 0; $i <= 8; $i++)
        echo $i, " : ", $sums[$i], PHP_EOL;
}

$total = 0;
foreach($sums as $sum){
    $total += $sum;
}

echo "NB Fishes : ", $total, PHP_EOL;