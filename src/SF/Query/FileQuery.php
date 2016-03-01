<?php
namespace SF\Query;


use SF\Util\File;

class FileQuery extends File implements Query
{
    public function __construct($filePath)
    {
        parent::__construct($filePath, 'r');
    }
    public function toString()
    {
        return $this->readAll();
    }

    public function __toString()
    {
        return $this->toString();

    }
}