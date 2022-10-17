<?php
namespae app\report\2020_4\12;

class Report
{

    public function __construct (private string $a,private string $b)
    {

    }


    public function work($a, $b)
    {
        return $a*$b;
    }


}
