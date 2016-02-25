<?php
namespace SF\Aggregator;



class StringQuery implements Query
{
    private $string = '';
    public function __construct($string)
    {
        $this->string = $string;
    }

    public function toString ()
    {
        return $this->string;
    }

    public function __toString()
    {
        return $this->toString();
    }
}