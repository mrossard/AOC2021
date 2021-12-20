<?php

class Number{
    private ?Number $left;
    private ?Number $right;

    private ?int $literalValue;

    function __construct(private ?Number $parent = null){
        $this->literalValue = null;
    }


    /**
     * @return string
     */
    public function print() : string
    {
        if($this->literalValue !== null){
            return $this->literalValue;
        }
        return '['.$this->getLeft()->print().','.$this->getRight()->print().']';
    }

    /**
     * @param string $strNumber
     * @param Number|null $parent
     * @return Number
     * @throws Exception
     */
    public static function fromString(string $strNumber, ?Number $parent=null){
        $number = new Number($parent);
        if(strlen($strNumber) === 1){
            $number->setLiteralValue((int) $strNumber);
            return $number;
        }

        $opened = 1;
        $currentChar = 1; //en 0 on a toujours le crochet ouvrant
        while($currentChar < strlen($strNumber)){
            //séparateur = virgule n'ayant à sa gauche qu'un crochet ouvert et pas fermé
            switch($strNumber[$currentChar]) {
                case ',' :
                    if ($opened === 1) {
                        $number->setLeft(self::fromString(substr($strNumber, 1, $currentChar - 1), $number));
                        $number->setRight(self::fromString(substr($strNumber, $currentChar + 1, -1), $number));
                        return $number;
                    }
                    break;
                case '[':
                    $opened++;
                    break;
                case ']':
                    $opened--;
                    break;
                default:
                    //NOOP
                    break;
            }
            $currentChar++;
        }
        throw new \Exception("raté.:o");
    }

    /**
     * @return void
     */
    public function reduce() : self
    {
//        echo "Before : ", $this->print(), PHP_EOL;
        $done  = false;
        while(!$done){
            $exploded = $this->explode();
            if($exploded) {
//                echo "After explode : ", $this->print(), PHP_EOL;
                continue;
            }
            $split = $this->split();
            if($split) {
//                echo "After split : ", $this->print(), PHP_EOL;
                continue;
            }
            $done = true;
        }
        return $this;
    }


    public function explode() : bool
    {
        if(null !== $this->getLiteralValue())
            return false;

       $depth = $this->getDepth();

        if($depth === 4 && $this->literalValue === null){
            //pair nested inside 4 pairs
            $left = $this->getLeftNeighbour();
            $right = $this->getRightNeighbour();
            if(null !== $left) {
                $left->addRight($this->getLeft()->getLiteralValue());
            }
            if(null !== $right) {
                $right->addLeft($this->getRight()->getLiteralValue());
            }
            $this->setLeft(null);
            $this->setRight(null);
            $this->setLiteralValue(0);
            return true;
        }

        if($this->getLeft()->explode()) {
            return true;
        }

        if($this->getRight()->explode()) {
            return true;
        }

        return false;
    }

    public function split() : bool
    {
        if($this->literalValue !== null){
            if($this->literalValue >= 10) {
                $left = new Number($this);
                $left->setLiteralValue(floor($this->literalValue / 2));
                $right = new Number($this);
                $right->setLiteralValue(ceil($this->literalValue / 2));
                $this->setLiteralValue(null);
                $this->setLeft($left);
                $this->setRight($right);
                return true;
            }
            return false;
        }

        if($this->getLeft()->split()) {
            return true;
        }

        if($this->getRight()->split()) {
            return true;
        }

        return false;
    }

    /**
     * @return Number|null
     */
    public function getParent()
    {
        return $this->parent;
    }


    /**
     * @param Number $left
     */
    public function setLeft($left): void
    {
        if(null !== $left && null === $left->getParent()){
            $left->setParent($this);
        }
        $this->left = $left;
    }

    /**
     * @return Number
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param Number $right
     */
    public function setRight($right): void
    {
        if(null !== $right &&null === $right->getParent()){
            $right->setParent($this);
        }
        $this->right = $right;
    }

    /**
     * @return Number
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return int
     */
    public function getLiteralValue(): ?int
    {
        return $this->literalValue;
    }

    /**
     * @param ?int $literalValue
     */
    public function setLiteralValue(?int $literalValue): void
    {
        $this->literalValue = $literalValue;
    }

    private function getDepth() : int
    {
        if(null === $this->parent){
            return 0;
        }

        return $this->parent->getDepth() + 1;

    }

    private function getLeftNeighbour() : ?Number
    {
        if(null === $this->getParent()){
            return null;
        }

        if($this->getParent()->getRight() === $this){
            return $this->getParent()->getLeft()->getRightLeaf();
        }

        if($this->getParent()->getParent() === null) {
            return null;
        }

        if(null === $this->getParent()->getLeftNeighbour()) {
            return null;
        }

        return $this->getParent()->getLeftNeighbour()->getRightLeaf();

    }

    private function getRightNeighbour()
    {
        if(null === $this->getParent()){
            return null;
        }

        if($this->getParent()->getLeft() === $this){
            return $this->getParent()->getRight()->getLeftLeaf();
        }

        if($this->getParent()->getParent() === null) {
            return null;
        }

        if(null === $this->getParent()->getRightNeighbour()) {
            return null;
        }

        return $this->getParent()->getRightNeighbour()->getLeftLeaf();
    }

    private function addRight(?int $literal)
    {
        if($this->getLiteralValue() !== null){
            $this->literalValue += $literal;
        }
        else{
            $this->getRight()->addRight($literal);
        }

    }

    private function addLeft(?int $literal)
    {
        if($this->getLiteralValue() !== null){
            $this->literalValue += $literal;
        }
        else{
            $this->getRight()->addLeft($literal);
        }
    }

    public function add(Number $num) : self
    {
        $res = new Number();
        $res->setLeft($this);
        $res->setRight($num);

        return $res;
    }

    /**
     * @param Number|null $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    private function getRightLeaf()
    {
        if($this->literalValue !== null)
            return $this;

        return $this->getRight()->getRightLeaf();
    }

    private function getLeftLeaf()
    {
        if($this->literalValue !== null)
            return $this;

        return $this->getLeft()->getLeftLeaf();
    }

    function getMagnitude() : int
    {
        if(null !== $this->getLiteralValue()) {
            return $this->getLiteralValue();
        }

        return 3 * $this->getLeft()->getMagnitude() + 2 * $this->getRight()->getMagnitude();

    }

}

$strNumbers = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$result = Number::fromString($strNumbers[0]);

for($i = 1, $iMax = count($strNumbers); $i < $iMax; $i++){
    $result = $result->add(Number::fromString($strNumbers[$i]));
    $result->reduce();
}

echo 'Résultat : ', $result->print(), PHP_EOL;
echo 'Magnitude : ', $result->getMagnitude(), PHP_EOL;

$maxMagnitude = 0;
for($i = 0, $loopsMax = count($strNumbers) - 1; $i < $loopsMax; $i++){
    for($j=$i+1; $j < $loopsMax + 1; $j++){
        $mag1 = (Number::fromString($strNumbers[$i]))->add(Number::fromString($strNumbers[$j]))->reduce()->getMagnitude();
        $mag2 = (Number::fromString($strNumbers[$j]))->add(Number::fromString($strNumbers[$i]))->reduce()->getMagnitude();
        $maxMagnitude = max($mag1, $mag2, $maxMagnitude);
    }
}
echo 'Magnitude max : ', $maxMagnitude, PHP_EOL;
