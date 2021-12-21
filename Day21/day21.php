<?php

$input =  file($argv[1], FILE_IGNORE_NEW_LINES);

$positions[1] = (int) explode('position: ',$input[0])[1];
$positions[2] = (int) explode('position: ',$input[1])[1];

$scores = [1=>0, 2=>0];
$totalRolls = 0;

while(true) {
    for ($i = 1; $i <=100; $i++){
        $totalRolls ++;
        $currentPlayer = ((($totalRolls+5)%6) < 3)? 1 : 2;
        $positions[$currentPlayer] += $i ;

        if($totalRolls%3 === 0){
            $positions[$currentPlayer] = (($positions[$currentPlayer] + 9) % 10) + 1;
            $scores[$currentPlayer] += $positions[$currentPlayer];
        }
        if($scores[$currentPlayer] >=1000){
            $otherPlayer = $currentPlayer === 1 ? 2 : 1;
            echo 'Part 1 : ', $scores[$otherPlayer] * $totalRolls, PHP_EOL;
            break 2;
        }
    }
}