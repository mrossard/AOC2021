<?php

function printImg(array $image) : void
{
    foreach($image as $y=>$line){
        foreach ($line as $x=>$value){
            echo ($value===1?'#':'.');
        }
        echo PHP_EOL;
    }
}

function value(int $x, int $y, array $image, int $infinite) : int
{
    $binVal = '';
    for($j = $y-1; $j <= $y+1; $j++){
        for($i = $x-1; $i<= $x+1; $i++){
            $binVal .= $image[$j][$i] ?? $infinite;
        }
    }

    return base_convert($binVal, 2, 10);
}

function enhance(array $image, array $algorithm, int &$infinite) : array
{
    $newImage = [];

    $yKeys = array_keys($image);
    $xKeys = array_keys($image[0]);

    for($y = min($yKeys) - 1; $y <= max($yKeys) + 1; $y++){
        for($x = min($xKeys) - 1; $x <= max($xKeys) + 1; $x++) {
            $newImage[$y][$x] = $algorithm[value($x, $y, $image, $infinite)];
        }
    }

    $newInfinite = ($infinite === 0) ? 0:511;

    $infinite = $algorithm[$newInfinite];

    return $newImage;
}

function countLitPixels(array $image){
    $sum = 0;

    foreach ($image as $y=>$line){
        foreach ($line as $x=>$value) {
            $sum += $value;
        }
    }
    return $sum;
}

$input = file($argv[1], FILE_IGNORE_NEW_LINES);

$algorithm = [];
foreach(str_split($input[0]) as $y=> $val){
    $algorithm[$y] = ($val==='#'?1:0);
}

$image = [];
for($y = 0, $yMax = count($input) - 2; $y < $yMax; $y++){
    foreach(str_split($input[$y + 2]) as $x=> $char){
        $image[$y][$x] = ($char==='#'?1:0);
    }
}


$infinite = 0;

for($i = 0; $i < $argv[2]; $i++) {
    $image = enhance($image, $algorithm, $infinite);
}

echo countLitPixels($image), PHP_EOL;