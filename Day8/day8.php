<?php

/**
 * @param array $words
 * @param array $segmentFrequency
 * @param array $target
 * @return array
 */
function getWordMap(array $words, array $segmentFrequency, array $target): array
{
    $segmentMap = [];
    $letters = [];

    foreach (range('a', 'g') as $letter) {
        $substr_count = substr_count(implode('', $words), $letter);
        $letters[$letter] = ['frequency' => $substr_count, 'candidates' => []];
        foreach ($segmentFrequency as $segment => $frequency) {
            if ($frequency === $substr_count) {
                $letters[$letter]['candidates'][] = $segment;
            }
        }
        if (count($letters[$letter]['candidates']) == 1) {
            $segmentMap[$letter] = $letters[$letter]['candidates'][0];
            unset($letters[$letter]);
        }
    }

    //on essaye de matcher les mots sur les cibles
    $wordMap = [];
    foreach ($words as $word) {
        foreach ($target as $number => $segments) {
            if (strlen($word) !== strlen($segments))
                continue;
            $letters = str_split($word);
            foreach ($letters as $letter) {
                if (array_key_exists($letter, $segmentMap) && !str_contains(haystack: $segments, needle: $segmentMap[$letter])) {
                    continue 2;
                }
            }
            $wordMap[$number][$word] = $word;
        }
    }

    // ici il nous manque encore 0, 5, 6.
    // 5 => enlever la solution 3
    // 0 => enlever 9
    // 6 => enlever 9
    // 0 est celui des deux qui contient tous les segments de 7
    unset($wordMap[5][$wordMap[3][array_key_first($wordMap[3])]]);
    unset($wordMap[0][$wordMap[9][array_key_first($wordMap[9])]]);
    unset($wordMap[6][$wordMap[9][array_key_first($wordMap[9])]]);

    foreach (str_split($wordMap[7][array_key_first($wordMap[7])]) as $segment) {
        foreach ($wordMap[0] as $word) {
            if (!str_contains($word, $segment)) {
                unset($wordMap[0][$word]);
                continue 2;
            }
        }
    }
    unset($wordMap[6][$wordMap[0][array_key_first($wordMap[0])]]);

    foreach($wordMap as $number=>$candidates){
        $wordMap[$number] = array_pop($candidates);
    }
    return $wordMap;
}

function sortString(string $word) : string
{
    $split = str_split($word);
    sort($split);
    return implode(separator: '', array: $split);
};

$target = [
    0=>'abcefg',
    1=>'cf',
    2=>'acdeg',
    3=>'acdfg',
    4=>'bcdf',
    5=>'abdfg',
    6=>'abdefg',
    7=>'acf',
    8=>'abcdefg',
    9=>'abcdfg'
];

$line = implode($target);

$segmentFrequency = [];
foreach(range('a', 'g') as $letter){
    $frequency = substr_count($line, $letter);
    $segmentFrequency[$letter] = $frequency;
}

$input = file($argv[1]);

//parties 1 & 2
$uniqueCount = 0;
$total = 0;
foreach($input as $id=> $line){
    list($digits, $outputValue) = explode(' | ', $line);
    $words = explode(' ', $digits);
    foreach($words as $wid=>$word){
        $words[$wid] = sortString($word);
    }
    $wordMap = getWordMap($words, $segmentFrequency, $target);
    $reversed = array_flip($wordMap);

    $outputWords = explode(' ', substr($outputValue,0,  -1));

    $value = '';
    foreach ($outputWords as $word){
        $word = sortString($word);
        if($word === $wordMap[1] || $word === $wordMap[4] || $word === $wordMap[7] || $word === $wordMap[8]){
            $uniqueCount ++;
        }
        $value .= $reversed[$word];
    }
    $total += (int) $value;
}

echo " Uniques : ", $uniqueCount, PHP_EOL;
echo " Total : ", $total, PHP_EOL;