<?php

function insert(array $pairs, array $insertions, array $totals) : array
{
    $newPairs = $pairs;
    foreach ($insertions as $start => $insertion) {
        foreach ($insertion as $end => $insert) {
            if(array_key_exists($start.$end, $pairs)){
                $totals[$insert] = ($totals[$insert]??0) + $pairs[$start.$end];
                $newPairs[$start.$insert] = ($newPairs[$start.$insert] ?? 0) +  $pairs[$start.$end];
                $newPairs[$insert.$end] = ($newPairs[$insert.$end] ??  0) +  $pairs[$start.$end];
                $newPairs[$start.$end] -= $pairs[$start.$end];
            }
        }
    }
    return [$newPairs, $totals];
}

$input = file($argv[1]);

$template = substr($input[0], 0, -1);

$insertions = [];
for($line = 2; $line < count($input); $line++){
    [$pair, $insert] = explode(' -> ', substr($input[$line], 0, -1));
    $insertions[$pair[0]][$pair[1]] = $insert;
}

//paires
$pairs = [];
$split = str_split($template);
for ($a = 0, $b = 1; $b < count($split); $a++, $b++) {
    $pairs[$split[$a].$split[$b]] = ($pairs[$split[$a].$split[$b]] ?? 0) + 1;
}

//totaux
$totals = [];
$stats = count_chars($template, 1);
foreach ($stats as $char=>$number){
    $totals[chr($char)] = $number;
}

//RUN
for($i=1; $i <= $argv[2]; $i++){
    [$pairs, $totals] = insert($pairs, $insertions, $totals);
}

sort($totals);

echo 'Result : ', $totals[array_key_last($totals)] - $totals[array_key_first($totals)], PHP_EOL;