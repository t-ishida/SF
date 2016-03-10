<?php
namespace SF\Aggregator;


use SF\Aggregator;
use SF\Query;
use SF\Util\File;
use SF\Util\StringUtil;

class CSVAggregator extends File implements Aggregator
{
    private $headers = null;

    public function __construct($filePath, $headers = null)
    {
        parent::__construct($filePath, 'r');
    }

    /**
     * @return array
     */
    public function aggregate()
    {
        if ($this->headers === null) {
            $this->headers = StringUtil::parseCSV($this->gets());
        }
        $result = array();
        while($row = $this->gets()) {
            if (!trim($row)) continue;
            $result[] = (object)array_combine($this->headers,  StringUtil::parseCSV($row));
        }
        return $result;
    }
}