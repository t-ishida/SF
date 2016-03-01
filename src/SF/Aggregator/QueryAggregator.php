<?php
namespace SF\Aggregator;

use SF\Aggregator;
use SF\Query\Query;
use SF\APISettings;

class QueryAggregator extends \SF\Client implements Aggregator
{
    /**
     * @var Query
     */
    private $query = null;

    /**
     * QueryAggregator constructor.
     * @param Query $query
     * @param APISettings $settings
     */
    public function __construct(Query $query, APISettings $settings)
    {
        parent::__construct($settings);
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function aggregate()
    {
        return $this->createResult($this->get('/services/data/v26.0/query', array('q' => $this->query->toString())));
    }

    /**
     * @param $apiResult
     * @return array
     */
    public function createResult($apiResult)
    {
        $result = array_map(function($row){return $this->flat($row);}, $apiResult->records);
        isset($apiResult->nextRecordsUrl) &&
            $result = array_merge($result, $this->createResult($this->get($apiResult->nextRecordsUrl)));
        return $result;
    }

    /**
     * @return array
     */
    public function flat()
    {
        $key = null; $val = null;
        $argc = func_num_args();
        if ($argc === 1) {
            list($val) = func_get_args();
        } elseif ($argc === 2) {
            list($key, $val) = func_get_args();
        }
        is_object($val) && $val = (array)$val;
        $result = array();
        if (isset($val['attributes'])) {
            unset($val['attributes']);
        }
        foreach ($val as $key2 => $val2) {
            if (is_object($val2) || is_array($val2)) {
                list($key2, $val2) = $this->flat($key2, $val2);
                foreach ($val2 as $key3 => $val3) {
                    $result[$key ? "$key=>$key3" : $key3] = $val3;
                }
            } else {
                $result[$key ? "$key=>$key2" : $key2] = $val2;
            }
        }
        return $key === null ? $result : array($key, $result);
    }
}