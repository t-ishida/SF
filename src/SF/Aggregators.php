<?php
namespace SF;

/**
 * Class Aggregators
 * @package SF
 */
class Aggregators implements Aggregator
{
    private $list = array();

    /**
     * Aggregators constructor.
     * @param Aggregator[] $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * @return array
     */
    public function aggregate()
    {
        $result = array();
        foreach ($this->list as $aggregator) {
            $result = array_merge($result, $aggregator->aggregate());
        }
        return $result;
    }
}