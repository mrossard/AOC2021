<?php

class Grid{

    private array $data;

    /**
     * @param int $sizeX
     * @param int $sizeY
     */
    function __construct(private int $sizeX, private int $sizeY)
    {
        $this->data = [];
    }

    /**
     * @return int
     */
    public function getSizeY(): int
    {
        return $this->sizeY;
    }

    /**
     * @return int
     */
    public function getSizeX(): int
    {
        return $this->sizeX;
    }

    public function setValue(int $x, int $y, int $value) : void
    {
        $this->data[$y * $this->getSizeX() + $x] = $value;
    }

    public function getValue(int $x, int $y, int $default = PHP_INT_MAX) : int
    {
        if($x < 0 || $x >= $this->getSizeX() || $y < 0 || $y >= $this->getSizeY())
            return $default;

        return $this->data[$y * $this->getSizeX() + $x] ?? $default;
    }

    /**
     * @return array<array<int, int>>
     */
    public function getLowPoints() : array
    {
        $lowPoints = [];
        for($x = 0; $x < $this->getSizeX(); $x++){
            for($y=0; $y < $this->getSizeY(); $y++){
                $val = $this->getValue($x, $y);
                 if($val < $this->getValue($x-1, $y) &&
                    $val  < $this->getValue($x+1, $y) &&
                    $val  < $this->getValue($x, $y-1) &&
                    $val  < $this->getValue($x, $y+1)) {
                        $lowPoints[] = [$x, $y];
                 }
            }
        }
        return $lowPoints;
    }

    public function getBasin(int $x, int $y) : array
    {
        $current = $this->getValue($x, $y);
        if($current === 9 || $current === PHP_INT_MIN)
            return [];

        $basin[$y * $this->getSizeX() + $x] = [$x, $y];

        foreach(range(-1, 1, 2) as $stepX){
            foreach(range(-1, 1, 2) as $stepY){
                //décalage sur Y
                $newX = $x;
                $newY = $y + $stepY;
                $new = $this->getValue($newX, $newY, PHP_INT_MIN);
                if($new > $current && $new !== 9){
                    $basin[$newY * $this->getSizeX() + $newX] = [$newX, $newY];
                    foreach($this->getBasin($newX, $newY) as $point){
                        $index = $point[1] * $this->getSizeX() + $point[0];
                        $basin[$index] = $point;
                    }
                }
                //décalage sur X
                $newX = $x + $stepX;
                $newY = $y;
                $new = $this->getValue($newX, $newY, PHP_INT_MIN);
                if($new > $current && $new !== 9){
                    $basin[$newY * $this->getSizeX() + $newX] = [$newX, $newY];
                    foreach($this->getBasin($newX, $newY) as $point){
                        $index = $point[1] * $this->getSizeX() + $point[0];
                        $basin[$index] = $point;
                    }
                }


            }
        }
        return $basin;
    }
}

$input = file($argv[1]);
$grid = new Grid(strlen($input[0])-1, count($input));
foreach($input as $y=>$line){
    foreach(str_split(str_replace("\n", '', $line)) as $x=>$point){
        $grid->setValue($x, $y, (int)$point);
    }
}

$lowPoints = $grid->getLowPoints();

$total = 0;
foreach ($lowPoints as $point){
    $total += ($grid->getValue($point[0], $point[1]) +1);
}
echo "Partie 1 : ", $total, PHP_EOL;

//partie 2
$basins = [];
$sizes = [];
foreach($lowPoints as $lowPoint){
    $basin = $grid->getBasin($lowPoint[0], $lowPoint[1]);
    $basins[$lowPoint[0] + $lowPoint[1] * $grid->getSizeX()] = $basin;
    $sizes[] = count($basin);
}
sort($sizes);

echo "Partie 2 : ", $sizes[count($sizes) - 1] * $sizes[count($sizes) - 2] * $sizes[count($sizes) - 3], PHP_EOL;