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
                    foreach($this->query($query->toString()) as $currentRow) {
                        $currentRow['parentNode'] = $row;
                        $tmp[] = $this->query($query);
                    }
                }
                $result = $tmp;
            } else {
                $result = $this->query($query);
            }
        }
        return $result;
    }
}
