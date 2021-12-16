<?php

class Packet{

    private int $version;
    private int $type;
    /**
     * @var int|Packet[]
     */
    private int|array $contents;

    private string $leftovers;

    private Closure $operation;
    private $startValue;

    public function __construct(private string $strPacket){
        $this->initFromString($strPacket);
    }

    /**
     * @return string
     */
    public function getStrPacket(): string
    {
        return $this->strPacket;
    }

    private function initFromString(string $strPacket)
    {
        $strVersion = substr($strPacket, 0, 3);
        $strType = substr($strPacket, 3,3);
        $this->version = bindec($strVersion);
        $this->type = bindec($strType);
        switch ($this->type){
            case 4:
                $this->readLiteral(substr($strPacket, 6));
                break;
            case 0:
                $this->readSubPackets($strPacket);
                $this->operation = Closure::fromCallable('gmp_add');
                $this->startValue = 0;
                break;
            case 1:
                $this->readSubPackets($strPacket);
                $this->operation = Closure::fromCallable('gmp_mul');
                $this->startValue = 1;
                break;
            case 2:
                $this->readSubPackets($strPacket);
                $this->operation = Closure::fromCallable('min');
                $this->startValue = PHP_INT_MAX;
                break;
            case 3:
                $this->readSubPackets($strPacket);
                $this->operation = Closure::fromCallable('max');
                $this->startValue = PHP_INT_MIN;
                break;
            case 5:
                $this->readSubPackets($strPacket);
                $this->operation = static function($p1, $p2){
                    return ($p1 > $p2) ? 1 : 0;
                };
                break;
            case 6:
                $this->readSubPackets($strPacket);
                $this->operation = static function($p1, $p2){
                    return ($p1 < $p2) ? 1 : 0;
                };
                break;
            case 7:
                $this->readSubPackets($strPacket);
                $this->operation = static function($p1, $p2){
                    return (gmp_cmp($p1, $p2) === 0) ? 1 : 0;
                };
                break;
            default:
                $this->readSubPackets($strPacket);
                break;
        }
    }

    private function readLiteral(string $str)
    {
        $done = false;
        $offset = 0;
        $binLiteral = '';
        while(!$done){
            $groupStart = $str[$offset];
            $binLiteral .= substr($str, $offset+1, 4);
            if($groupStart === '0') {
                $done = true;
            }
            $offset += 5;
        }
        $this->contents =  bindec($binLiteral);
        $this->leftovers = substr($str, $offset);
    }

    /**
     * @return string
     */
    public function getLeftovers(): string
    {
        return $this->leftovers;
    }

    public function versionSum() : int
    {
        $sum = $this->version;
        if(is_array($this->contents)){
            foreach($this->contents as $subPacket){
                $sum += $subPacket->versionSum();
            }
        }
        return $sum;
    }

    /**
     * @param string $hexStr
     * @return string
     */
    public static function hexToBinString(string $hexStr) :string
    {
        $map  = [
            '0'=>'0000',
            '1'=>'0001',
            '2'=>'0010',
            '3'=>'0011',
            '4'=>'0100',
            '5'=>'0101',
            '6'=>'0110',
            '7'=>'0111',
            '8'=>'1000',
            '9'=>'1001',
            'A'=>'1010',
            'B'=>'1011',
            'C'=>'1100',
            'D'=>'1101',
            'E'=>'1110',
            'F'=>'1111',

        ];
        $binStr = '';
        foreach(str_split($hexStr) as $hexChar){
            $binStr .= $map[$hexChar];
        }
        return $binStr;
    }

    /**
     * @param string $strPacket
     * @return void
     */
    private function readSubPackets(string $strPacket): void
    {
        $lengthTypeId = $strPacket[6];
        if ($lengthTypeId === '0') {
            $subPacketsLength = bindec(substr($strPacket, 7, 15));
            $subPackets = substr($strPacket, 22, $subPacketsLength);
            while (strlen($subPackets) > 6) {
                $next = new Packet($subPackets);
                $subPackets = $next->getLeftovers();
                $this->contents[] = $next;
            }
            $this->leftovers = substr($strPacket, 22 + $subPacketsLength);
        } else {
            $nbSubPackets = bindec(substr($strPacket, 7, 11));
            $remaining = substr($strPacket, 18);
            for ($i = 0; $i < $nbSubPackets; $i++) {
                $next = new Packet($remaining);
                $remaining = $next->getLeftovers();
                $this->contents[] = $next;
            }
            $this->leftovers = $remaining;
        }
    }

    /**
     * @return int|Packet[]
     */
    public function getContents()
    {
        return $this->contents;
    }

    public function getValue()
    {
        if($this->type === 4)
            return $this->getContents();

        if(!isset($this->startValue)){
            $contents = $this->getContents();

            return ($this->operation)($contents[0]->getValue(), $contents[1]->getValue());
        }

        $value = $this->startValue;
        foreach($this->getContents() as $packet){
            $value = ($this->operation)($value, $packet->getValue());
        }

        return $value;
    }
}


$input = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$packet = new Packet(Packet::hexToBinString($input[0]));
var_dump($packet->versionSum());
var_dump($packet->getValue());