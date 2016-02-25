<?php
namespace SF;

/**
 * Class Crawler
 * @package SF
 */
class Crawler
{
    /**
     * @var Aggregator
     */
    private $aggregator = null;
    /**
     * @var Reducer
     */
    private $reducer = null;
    /**
     * @var Mapper
     */
    private $mapper = null;

    /**
     * Crawler constructor.
     * @param $aggregator
     * @param null $reducer
     * @param null $mapper
     */
    public function __construct($aggregator, $reducer = null, $mapper = null)
    {
        $this->aggregator = $aggregator;
        $this->reducer    = $reducer;
        $this->mapper     = $mapper;
    }

    /**
     * @return array
     */
    public function run () {
        $result = $this->aggregator->aggregate();
        if ($this->mapper) {
            for ($i = 0, $l = count($result); $i < $l; $i++) {
                $result[$i] = $this->mapper->map($result[$i]);
            }
        }
        if ($this->reducer) {
            $result = $this->reducer->reduce($result);
        }
        return $result;
    }

    /**
     * @return null
     */
    public function getAggregator()
    {
        return $this->aggregator;
    }

    /**
     * @return null
     */
    public function getReducer()
    {
        return $this->reducer;
    }

    /**
     * @return null
     */
    public function getMapper()
    {
        return $this->mapper;
    }
}