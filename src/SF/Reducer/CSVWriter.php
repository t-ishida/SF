<?php
namespace SF\Reducer;

use SF\Reducer;
use SF\Util\File;

/**
 * Class CSVWriter
 * @package SF\Reducer
 */
class CSVWriter extends File implements Reducer
{
    /**
     * CSVWriter constructor.
     * @param $path
     */
    public function __construct($path)
    {
        parent::__construct($path, 'w');
    }

    /**
     * @param array $list
     * @return array
     */
    public function reduce(array $list)
    {
        $escape = function($col) {return '"' . str_replace(array('\\', '"', "\n"), array('\\\\','\"', '\n'), $col) . '"';};
        $this->writeLine(implode(',', array_map($escape, array_keys((array)reset($list)))));
        foreach ($list as $row) {
            $this->writeLine(implode(',', array_map($escape, array_values((array)$row))));
        }
        return $list;
    }

}
