<?php

$input = file($argv[1]);

//part 1
$values = [
    ')' => 3,
    ']' => 57,
    '}' => 1197,
    '>' => 25137
];

$valuesPart2 =[
    ')' => 1,
    ']' => 2,
    '}' => 3,
    '>' => 4
];

$openClose = [
    '<' =>'>',
    '('=>')',
    '{'=>'}',
    '['=>']'
];

$badChars = [];
$scores = [];
foreach($input as $line){
    $pile = [];
    $line = substr($line, 0, -1);
    foreach(str_split($line) as $char){
        if(array_key_exists($char, $openClose)){
            $pile[] = $char;
        }
        else{
            $shouldClose = array_pop($pile);
            if($char !== $openClose[$shouldClose]){
                $badChars[] = $char;
//                echo $shouldClose, ' - ', $char, PHP_EOL;
                continue 2;
            }
        }
    }
    //part 2 : ok mais incomplets
    $complete = [];
    while($char = array_pop($pile)){
        $complete[] = $openClose[$char];
    }
    $score = 0;
    foreach($complete as $char){
        $score = ($score * 5) + $valuesPart2[$char];
    }
    $scores[] = $score;
}

$points = 0;
foreach($badChars as $char){
    $points += ($values[$char] ?? 0);
}
echo 'Part 1 :' , $points, PHP_EOL;

sort($scores);
echo 'Part 2 : ', $scores[floor(count($scores)/2)], PHP_EOL;