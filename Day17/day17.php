<?php

function possibleVx($min, $max){
    $candidates = [];
    $stepX = ($min > 0? 1:-1);
    foreach(range($stepX, $max, $stepX) as $vx){
        $x = 0;
        $step = 1;
        $currentV = $vx;
        while($x <= $max && $currentV >= 0){
            $x += $currentV;
            if(abs($x) >= abs($min) && abs($x) <= abs($max)){
                $candidates[$x][$vx][] = ['step'=>$step, 'currentVx'=>$currentV];
            }
            $currentV -= $stepX;
            $step++;
        }
    }
    return $candidates;
}

/**
 * @param $vy
 * @return void
 */
function getMaxY($vy)
{
    $init = 0;
    $previous = -1;
    $current = 0;
    $step = $vy;
    while ($current > $previous) {
         $previous = $current;
         $current += $step;
         $step--;
    }
    return $current;
}

$input = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$relevantPart = str_replace('y=', '', explode('x=', $input[0])[1]);

[$xMin, $xMax] = explode('..', explode(',', $relevantPart)[0]);
[$yMin, $yMax] = explode('..', explode(',', $relevantPart)[1]);


$candidatesX = possibleVx((int)$xMin, (int)$xMax);

$yStep = ((int)$yMin > 0)? 1 : -1;

$winners = [];
foreach($candidatesX as $x=>$candidates) {
    foreach ($candidates as $vx => $stepAndCUrrentVx) {
        foreach ($stepAndCUrrentVx as $valid) {
            $step = $valid['step'];
            $currentVx = $valid['currentVx'];
            foreach(range((int)$yMin, (int)$yMax, $yStep) as $y) {
                $vy = (($step * ($step - 1)) / 2 + $y) / $step;
                if (is_int($vy)) {
                    $winners[$x][$y][] = [$vx, $vy];
                }
                //si currentVx == 0, on peut tester avec des valeurs >> de step
                if($currentVx === 0){
                    for(; $step < $valid['step'] + ($yMax * $yMax); $step++){
                        $vy = (($step * ($step - 1)) / 2 + $y) / $step;
                        if (is_int($vy)) {
                            $winners[$x][$y][] = [$vx, $vy];
                        }
                    }
                }
                $step = $valid['step'];
            }
        }
    }
}

$max = 0;
$valid = [];
foreach($winners as $x=>$yWinners){
    foreach ($yWinners as $y=>$v) {
        foreach ($v as [$vx, $vy]) {
            $maxY = getMaxY($vy);
            $valid[$vx][$vy] = 1;
            if ($maxY > $max)
                $max = $maxY;
        }
    }
}

echo "max Y :", $max, PHP_EOL;

$total = 0;
foreach($valid as $val){
    $total += count($val);
}

echo "Total valid velocities :", $total, PHP_EOL;
