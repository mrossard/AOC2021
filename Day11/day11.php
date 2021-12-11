<?php

class Octopus{

    /**
     * @var Octopus[]
     */
    private array $neigbours;

    private array $flashes;

    function __construct(private int $energyLevel, private int $position)
    {
        $this->flashes = [];
        $this->neigbours = [];
    }

    function newStep(){
        if($this->energyLevel > 9) {
            $this->energyLevel = 0;
        }
    }

    function levelUp(int $step){
        $this->energyLevel++;
        if($this->energyLevel === 10){
            $this->flash($step);
        }
    }

    function addNeighbour(Octopus $neighbour){
        if(!array_key_exists($neighbour->getPosition(), $this->neigbours)){
            $this->neigbours[$neighbour->getPosition()] = $neighbour;
            $neighbour->addNeighbour($this);
        }
    }

    function flash(int $step){
        $this->flashes[$step] = true;
        foreach($this->neigbours as $neigbour){
            $neigbour->levelUp($step);
        }
    }

    /**
     * @return int
     */
    public function getNbFlashes(): int
    {
        return count($this->flashes);
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getEnergyLevel(): int
    {
        return $this->energyLevel;
    }

    public function hasFlashed(int $step)
    {
        return array_key_exists($step, $this->flashes);
    }
}


/**
 * @param Octopus[] $octopuses
 * @return void
 */
function part1(array $octopuses) : void
{
    for ($i = 0; $i < 100; $i++) {
        $nbFlashes = 0;
        foreach ($octopuses as $octopus) {
            $octopus->levelUp($i + 1);
        }
        foreach ($octopuses as $octopus) {
            if ($octopus->hasFlashed($i + 1)) {
                $nbFlashes++;
            }
            $octopus->newStep();
        }

    }

    $flashes = 0;
    foreach ($octopuses as $octopus) {
        $flashes += $octopus->getNbFlashes();
    }

    echo $flashes, PHP_EOL;
}


/**
 * @param Octopus[] $octopuses
 * @return void
 */
function part2(array $octopuses) : void
{
    $i = 1;
    while (true) {
        $nbFlashes = 0;
        foreach ($octopuses as $octopus) {
            $octopus->levelUp($i);
        }
        foreach ($octopuses as $octopus) {
            if ($octopus->hasFlashed($i)) {
                $nbFlashes++;
            }
            $octopus->newStep();
        }

        echo "Step ", $i, ' : ', $nbFlashes, ' flashes', PHP_EOL;

        if ($nbFlashes === count($octopuses)) {
            break;
        }
        $i++;
    }
}


$input = file($argv[1]);
$lineSize = strlen($input[0]) - 1;

/**
 * @var Octopus[] $octopuses
 */
$octopuses = [];
foreach($input as $y=>$line){
    foreach (str_split(substr($line, 0, -1)) as $x => $energy){
        $position = ($y * $lineSize) + $x;
        $octopuses[$position] = new Octopus((int)$energy, $position);
        foreach([[-1,-1], [-1, 0], [0, -1], [1, -1]] as [$stepX, $stepY]){
            $y2 = $y + $stepY;
            $x2 = $x + $stepX;
            if($x2 < 0 || $y2 <0 || $x2 >= $lineSize){
                continue;
            }
            $position2 = ($y2 * $lineSize) + $x2;
            $neighbour = $octopuses[$position2] ?? null;
            if(null !== $neighbour){
                $octopuses[$position]->addNeighbour($neighbour);
            }
        }
    }
}

if(($argv[2]??'1') === '1') {
    part1($octopuses);
}
else{
    part2($octopuses);
}