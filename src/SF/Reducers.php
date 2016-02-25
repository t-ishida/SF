<?php
namespace SF;
/**
 * Class Reducers
 * @package SF
 */
class Reducers implements Reducer
{
    /**
     * @var Reducer[]
     */
    private $list = null;

    /**
     * Reducers constructor.
     * @param Reducer[] $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    public function reduce(array $list)
    {
        foreach ($this->list as $reducer) {
            $list = $reducer->reduce($list);
        }
        return $list;
    }
}