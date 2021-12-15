<?php

/**
 * @param string $filename
 * @return Generator
 */
function read(string $filename) : Generator
{
    $input = file_get_contents("input.txt");
    $start = 0;
    while (($end = strpos($input, PHP_EOL, $start)) !== false)   {
        $line = trim(substr($input, $start, $end - $start));
        yield (int) $line;
        $start = $end+1;
    }
}

/**
 * @param Generator $list
 * @return Generator
 */
function getWindows(Generator $list)
{
    $windows = [];
    $max = 0;
    foreach ($list as $id => $value){
        $windows[$id] = ($windows[$id] ?? 0) + $value;
        $windows[$id + 1] = ($windows[$id + 1] ?? 0) + $value;
        $windows[$id + 2] = ($windows[$id + 2] ?? 0) + $value;
        if($id >= 2)
            $max = $id;
    }

    for($i = 2; $i <= $max; $i++){
        yield $windows[$i];
    }
}

/**
 * @param array $values
 * @return int
 */
function countIncreases(Iterable $values): int
{
    $previous = null;
    $increases = 0;
    foreach ($values as $value) {
        if (null !== $previous && $value > $previous) {
            $increases++;
        }
        $previous = $value;
    }
    return $increases;
}

//part 1
echo 'part 1 : ', countIncreases(read('input.txt')), PHP_EOL;

//part 2
echo 'part 2 : ', countIncreases(getWindows(read('input.txt'))), PHP_EOL;