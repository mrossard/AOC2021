<?php

$input = file('input.txt');
//$input = file('example.txt');

$nbBits = strlen($input[0])  - 2;
$nbLines = count($input);

$gamma = [];
$epsilon = [];
$sum = [];

for($pos=0; $pos < $nbBits; $pos++){
    $sum[$pos] = 0;
    foreach($input as $line){
        $val = (int) substr($line, $pos, 1);
        $sum[$pos] += $val;
        if($sum[$pos] > ($nbLines/2)){
            $gamma[$pos] = 1;
            $epsilon[$pos] = 0;
            //continue 2;
        }
    }
    if(!array_key_exists($pos, $gamma)) {
        $gamma[$pos] = 0;
        $epsilon[$pos] = 1;
    }
}

$strGamma = implode($gamma);

$decGamma = bindec($strGamma);
echo $strGamma, ' => ', $decGamma, PHP_EOL;

$strEpsilon = implode($epsilon);
$decEspilon = bindec($strEpsilon);
echo $strEpsilon, ' => ', $decEspilon,PHP_EOL;

echo 'part1  : ', $decGamma * $decEspilon, PHP_EOL,PHP_EOL;

//part 2
function matching(array $candidates, array $sums, $type, $pos = 0) : array
{
    if(count($candidates) == 1)
        return $candidates;

    if($sums[$pos] >= (count($candidates)/2) )
        $valid = $type;
    else
        $valid = abs($type - 1);

    foreach ($candidates as $i=>$candidate){
        $val = (int)substr($candidate, $pos, 1);
        if($val !== $valid) {
            unset($candidates[$i]);
            for($j = $pos + 1; $j < count($sums); $j++){
                $sums[$j] -= (int)substr($candidate, $j, 1);
            }
        }
    }
    return matching($candidates, $sums, $type,$pos+1);
}

$oxygen = matching($input, $sum, 1);
$scrubber = matching($input, $sum, 0);

echo 'part2  : ', bindec(array_pop($oxygen)) * bindec(array_pop($scrubber)), PHP_EOL,PHP_EOL;