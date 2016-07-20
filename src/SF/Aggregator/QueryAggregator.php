<?php
namespace SF\Aggregator;

use SF\Aggregator;
use SF\Query\Query;
use SF\APISettings;
use SF\Query\TemplateQuery;

class QueryAggregator extends \SF\Client implements Aggregator
{
    /**
     * @var Query
     */
    private $queries = null;

    /**
     * QueryAggregator constructor.
     * @param Query $query
     * @param APISettings $settings
     */
    public function __construct($query, APISettings $settings)
    {
        parent::__construct($settings);
        if(!is_array($query) && $query instanceof Query) {
            throw new \InvalidArgumentException('$query is not query');
        }
        if ($query instanceof Query) {
            $query = array($query);
        } 
        $this->queries = $query;
    }

    /**
     * @return array
     */
    public function aggregate()
    {
        $result = null;
        foreach ($this->queries as $query) {
            if ($result) {
                $tmp = array();
                foreach ($result as $row) {
                    $query instanceof TemplateQuery && $query->prepare($row);
                    $currentResult = $this->createResult($this->get('/services/data/v26.0/query', array('q' => $query->toString())));
                    $currentResult['parentNode'] = $row;
                    $tmp[] = $currentResult;
                }
                $result = $tmp;
            } else {
                $result = $this->createResult($this->get('/services/data/v26.0/query', array('q' => $query->toString())));
            }
        }
        return $result;
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
