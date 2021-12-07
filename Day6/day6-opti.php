<?php

$fishes = explode(',', file($argv[1])[0]);
$nbDays = $argv[2];

$sums = [];

foreach($fishes as $fish){
    $sums[$fish]  = ($sums[$fish]??0) +1;
}

for($day=0; $day < $nbDays; ++$day){
    $sums[($day+7)%9] = ($sums[($day+7)%9] ?? 0) + ($sums[$day%9] ?? 0);
}

$total = 0;
foreach($sums as $sum){
    $total += $sum;
}

echo "NB Fishes : ", $total, PHP_EOL;