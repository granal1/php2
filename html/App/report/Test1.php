<?php
namespae app\report;

class Test1
{
    public function __construct (private string $a,private string $b)
    {

    }

    public function work($a, $b)
    {
        return $a*$b;
    }


}
