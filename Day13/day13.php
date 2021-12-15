<?php

$input = file($argv[1]);

function fold(array $points, array $folds, &$maxX, &$maxY) : array
{
    $newPoints = [];
    foreach ($folds as $fold) {
        $direction = $fold[0];
        $foldOn = $fold[1];

        foreach ($points as $y => $line) {
            foreach ($line as $x => $point) {
                switch ($direction) {
                    case 'x':
                        $maxX = $foldOn -1;
                        if ($x > $foldOn) {
                            $x = $foldOn - ($x - $foldOn);
                        }
                        break;
                    case 'y':
                        $maxY = $foldOn - 1;
                        if ($y > $foldOn) {
                            $y = $foldOn - ($y - $foldOn);
                        }
                        break;
                    default:
                        die('wtf?');
                }
                $newPoints[$y][$x] = $point;
            }
        }
        $points = $newPoints;
    }

    return $newPoints;
}


function printPoints($points, $maxX, $maxY){
    for($y = 0; $y <= $maxY; $y++){
        for($x = 0; $x <= $maxX; $x++){
            echo $points[$y][$x] ?? ' ';
        }
        echo PHP_EOL;
    }
    echo PHP_EOL;
}

$points = [];
$i=0;
$line = $input[0];
$maxX = 0;
$maxY = 0;
while($line !== "\n"){
    [$x, $y] = explode(',', substr($line, 0, -1));
    $points[(int)$y][(int)$x] = '█';
    if((int)$x > $maxX){
        $maxX = $x;
    }
    if((int)$y > $maxY){
        $maxY = $y;
    }
    $i++;
    $line = $input[$i];
}

$folds = [];
for($j=$i+1, $jMax = count($input); $j < $jMax; $j++){
    $line = explode('=',$input[$j]);
    $direction = substr($line[0], -1);
    $length = (int) $line[1];
    $folds[] = [$direction, $length];
}

//partie 1
$newPoints = fold($points, [$folds[0]], $maxX, $maxY);
$nbPoints = 0;
foreach($newPoints as $line){
    foreach($line as $point){
        $nbPoints++;
    }
}

echo "part 1 :", $nbPoints, PHP_EOL;
echo PHP_EOL;

//partie 2
$newPoints = fold($points, $folds, $maxX, $maxY);
printPoints($newPoints, $maxX, $maxY);

