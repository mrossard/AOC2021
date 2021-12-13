<?php

$input = file($argv[1]);

$caves = [];
foreach($input as $line){
    [$cave1, $cave2] = explode('-', substr($line, 0, -1));

    $caves[$cave1][] = $cave2;
    $caves[$cave2][] = $cave1;
}

function countPaths($caves, $from, $to, $visited, $part2 = false) : int
{
    $nb = 0;
    $visited[$from] = (strtoupper($from) !== $from) ? ($visited[$from] ?? 0 ) + 1 : 1;
    foreach($caves[$from] as $cave){
        if($cave === $to){
            $nb ++;
        }
        else{
            if(array_key_exists($cave, $visited) && strtoupper($cave) !== $cave ) {
                if($part2 && in_array(2, $visited, true)) {
                    continue;
                }
                if(!$part2) {
                    continue;
                }
            }
            if($cave === 'start'){
                continue;
            }
            $nb += countPaths($caves, $cave, $to, $visited, $part2);
        }
    }
    return $nb;
}

$nbPaths = countPaths($caves, 'start', 'end', [], (($argv[2]??1)==='2'));

echo "Result :", $nbPaths, PHP_EOL;