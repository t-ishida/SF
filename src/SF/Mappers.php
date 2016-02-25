<?php
namespace SF;

/**
 * Class Mappers
 * @package SF
 */
class Mappers implements Mapper
{
    /**
     * @var Mapper[]
     */
    private $list = null;

    /**
     * Mappers constructor.
     * @param Mapper[] $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    public function map($entity)
    {
        foreach($this->list as $mapper) {
            $entity = $mapper->map($entity);
        }
        return $entity;
    }
}