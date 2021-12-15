<?php

class Point
{
    function __construct(private int $x, private int $y, private int $weight)
    {
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function distanceFrom(Point $target) : int
    {
        return abs($this->getX() - $target->getX()) * abs($this->getY() - $target->getY());
    }

    /**
     * @param Point[] $points
     * @return array
     *
     * uasort est inexplicablement lent...c'est un peu mieux
     */
    public static function sortByWeight(array $points) : array
    {
        /*uasort($points, function(Point $a, Point $b){
            return $a->getWeight() <=> $b->getWeight();
        });
        return $points;*/

        $newPoints = [];
        $weights = [];
        foreach($points as $point){
            $weights[$point->getWeight()] = $point->getWeight();
        }
        sort($weights);

        foreach ($weights as $weight){
            foreach($points as $key=>$point){
                if($point->getWeight() === $weight){
                    $newPoints[$key] = $point;
                    unset($points[$key]);
                }
            }
        }
        return $newPoints;
    }
}


class Grid
{
    /**
     * @var Point[]
     */
    private array $points;

    private int $sizeX;
    private int $sizeY;

    public function __construct(array $input, int $numberOfIterations)
    {
        $this->points = [];
        $this->readInput($input, $numberOfIterations);
    }

    /**
     * @return int
     */
    public function getSizeX(): int
    {
        return $this->sizeX;
    }

    /**
     * @return int
     */
    public function getSizeY(): int
    {
        return $this->sizeY;
    }

    /**
     * @return Point[]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    function addPoint(Point $point, int $position) : void
    {
        $this->points[$position] = $point;
    }

    /**
     * @param string[] $input
     * @param int $numberOfIterations
     * @return void
     */
    public function readInput(array $input, int $numberOfIterations) : void
    {
        $tileSizeX = (strlen($input[0]));
        $tileSizeY = count($input) ;
        $this->sizeX = $tileSizeX * $numberOfIterations;
        $this->sizeY = $tileSizeY * $numberOfIterations;

        for($itY= 0; $itY < $numberOfIterations; $itY++) {
            $offsetY = $itY * ($tileSizeY);
            for ($itX = 0; $itX < $numberOfIterations; $itX++) {
                $offsetX = $itX * $tileSizeX;
                foreach ($input as $y => $line) {
                    $riskLine = str_split($line);
                    foreach ($riskLine as $x => $risk) {
                        $posX = $x + $offsetX;
                        $posY = $y + $offsetY;

                        $risk = ((int)$risk + $itX + $itY - 1) % 9 + 1;

                        $this->addPoint(new Point($posX, $posY, (int)$risk), $posY * $this->sizeX + $posX);
                    }
                }
            }
        }
    }

    /**
     * A*
     * @return array
     */
    public function traversalCost() : array
    {
        /**
         * @var Point[] $closed
         * @var Point[] $open
         */
        $closed = [];
        $open = [];
        //start
        $open[0] = clone $this->points[0];
        $open[0]->setWeight(0);
        $closed[0] = $open[0];
        $target = $this->points[array_key_last($this->points)];

        while (count($open) > 0) {
            $position = array_key_first($open);
            $current = $open[$position];
            unset($open[$position]);
            if ($current->getX() === $target->getX() && $current->getY() == $target->getY()) {
                return [($current->getY() * $this->sizeX + $current->getX()) => $current, ...$closed];
            }
            foreach ([[$current->getX() - 1, $current->getY()],
                         [$current->getX(), $current->getY() + 1],
                         [$current->getX() + 1, $current->getY()],
                         [$current->getX(), $current->getY() - 1]] as [$x2, $y2]) {

                if ($x2 >= 0 && $x2 < $this->sizeX && $y2 >= 0 && $y2 < $this->sizeY) {
                    $position2 = $y2 * $this->sizeX + $x2;
                    $next = clone $this->points[$position2];
                    $weight = $next->getWeight() + $current->getWeight();
                    $next->setWeight($weight);
                    if(array_key_exists($position2, $closed) || (array_key_exists($position2, $open) && $open[$position2]->getWeight() < $weight)){
                        continue;
                    }
                    $open[$position2] = $next;
                }
            }
            $closed[$current->getY() * $this->sizeY + $current->getX()] = $current;
            $open = Point::sortByWeight($open);
        }
        die('woops');
    }

}

$grid = new Grid(file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), $argv[2]??1);

$points = $grid->traversalCost();

echo $points[$grid->getSizeY() * $grid->getSizeX() -1]->getWeight(), PHP_EOL;