<?php /** @noinspection DisconnectedForeachInstructionInspection */


class Point{

    private int $nbIntersections;

    function __construct(private int $x, private int $y)
    {
        $this->nbIntersections = 0;
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
    public function getNbIntersections(): int
    {
        return $this->nbIntersections;
    }

    /**
     * @return void
     */
    public function addIntersection(Segment $segment){
        $this->nbIntersections++;
    }
}

class Segment{

    function __construct( private Point $start, private Point $end)
    { }

    /**
     * @return Generator{Point}
     */
    public function getPoints() : Generator
    {
        $x = $this->start->getX();
        $y = $this->start->getY();

        if(($this->start->getX() < $this->end->getX())) {
            $stepX = 1;
        }
        else{
            if(($this->start->getX() === $this->end->getX())){
                $stepX = 0;
            }
            else{
                $stepX = -1;
            }
        }

        if(($this->start->getY() < $this->end->getY())) {
            $stepY = 1;
        }
        else{
            if(($this->start->getY() === $this->end->getY())){
                $stepY = 0;
            }
            else{
                $stepY = -1;
            }
        }


        while($x !== $this->end->getX() || $y !== $this->end->getY()){
            yield new Point($x, $y);
            $x += $stepX;
            $y += $stepY;
        }
        yield $this->end;
    }

}

class Grid{

    /**
     * @var Point[][]
     */
    private array $points;

    function __construct(int $sizeX, int $sizeY)
    {
        for($x = 0; $x <= $sizeX; $x++){
            for($y = 0; $y <= $sizeY; $y++) {
                $this->points[$x][$y] = new Point($x, $y);
            }
        }
    }

    /**
     * @return void
     */
    public function addSegment(Segment $segment)
    {
        foreach($segment->getPoints() as $point){
            $this->points[$point->getX()][$point->getY()]->addIntersection($segment);
        }
    }

    /**
     * @param string[] $input
     * @param bool $part1
     * @return Grid
     */
    public static function build(array $input, bool $part1=true) : Grid
    {
        $maxX=0; $maxY=0;
        $segments = [];
        foreach($input as $i=>$line) {
            list($startStr, $endStr) = explode(' -> ', $line);
            list($x, $y) = explode(',', $startStr);
            $start = new Point((int)$x, (int)$y);
            list($x, $y) = explode(',', $endStr);
            $end = new Point((int)$x, (int)$y);

            if ($start->getX() > $maxX)
                $maxX = $start->getX();
            if ($start->getY() > $maxY)
                $maxY = $start->gety();
            if ($end->getX() > $maxY)
                $maxX = $end->getX();
            if ($end->getY() > $maxY)
                $maxY = $end->getY();

            //part 1 - horizontal or vertical only
            if ($part1) {
                if (!($start->getX() === $end->getX()) && !($start->getY() === $end->getY())) {
                    continue;
                }
            }

            $segments[$i] = new Segment($start, $end);

        }
        $grid = new Grid($maxX+1, $maxY+1);

        foreach($segments as $segment){
            $grid->addSegment($segment);
        }

        return $grid;
    }

    /**
     * @return Point[][]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

}

//$input = file('example.txt');
$input = file('input.txt');
$grid = Grid::build($input, !array_key_exists(1, $argv) || $argv[1] !== '2');

$nb = 0;
foreach($grid->getPoints() as $line){
    foreach($line as $point){
        if($point->getNbIntersections()>1){
            $nb++;
        }
    }
}

echo 'Result : ', $nb, PHP_EOL;

