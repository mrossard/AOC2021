<?php

//input
$input = file('input.txt');
//$input = file('example.txt');

$draws = explode(separator: ',', string: $input[0]);

class Board{
    private array $lines;
    private array $marks;

    private int $nbLines;
    private int $nbCols;

    private array $totals;
    private bool $hasWon;


    function __construct()
    {
        $this->lines = [];
        $this->marks = [];
        $this->nbCols = 0;
        $this->nbLines = 0;
        $this->hasWon = false;
        $this->totals = ['cols'=>[], 'lines'=>[], 'unmarked'=>0];
    }

    /**
     * @param array $strLine
     * @return void
     */
    public function addLine(array $strLine)
    {
        $line = [];
        foreach ($strLine as $strItem){
            $item = (int)$strItem;
            $line[] = $item;
            $this->totals['unmarked'] += $item;
        }
        $this->lines[] = $line;
        $this->nbLines++;
        if(0 === $this->nbCols){
            $this->nbCols = count($line);
        }
    }

    public function draw(int $number)
    {
        foreach ($this->lines as $ln=>$line){
            foreach($line as $cn=>$value){
                if($number === $value){
                    $this->marks[$cn][$ln] = 1;
                    $this->totals['cols'][$cn] = ($this->totals['cols'][$cn] ?? 0) +1;
                    if($this->totals['cols'][$cn] === $this->nbCols){
                        $this->hasWon = true;
                    }
                    $this->totals['lines'][$ln] = ($this->totals['lines'][$ln] ?? 0) +1;
                    if($this->totals['lines'][$ln] === $this->nbLines){
                        $this->hasWon = true;
                    }
                    $this->totals['unmarked'] -= $number;
                }
            }
        }
    }

    function hasWon() : bool
    {
        return $this->hasWon;
    }

    function unMarkedSum(){
        return $this->totals['unmarked'];
    }

}

/**
 * @var Board[] $boards
 */
$boards = [];
$current = 0;
for($ln = 2, $lnMax = count($input); $ln < $lnMax; $ln++){
    $line = $input[$ln];
    if($line === "\r\n"){
        $current++;
        continue;
    }
    if(!array_key_exists($current, $boards)) {
        $boards[$current] = new Board();
    }
    $boards[$current]->addLine(str_split(string: str_replace(search: "\r\n", replace: '', subject: $line), length: 3));
}

//part 1
foreach($draws as $strDraw){
    $draw = (int) $strDraw;
    //echo 'Drawn : ', $draw, PHP_EOL;
    foreach($boards as $boardId => $board){
        $board->draw($draw);
        if($board->hasWon()){
            echo 'Board ',$boardId, ' has won. Score : ', $board->unMarkedSum() * $draw, PHP_EOL;
            unset($boards[$boardId]);
        }
    }
}