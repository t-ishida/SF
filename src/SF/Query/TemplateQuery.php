<?php

namespace SF\Query;
class TemplateQuery extends StringQuery
{
    private $parameter = null;

    public function prepare(array $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * @return null
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    public function toString()
    {
        $self = $this;
        $lines = explode("\n", parent::toString());
        $params = $this->parameter ? (array) $this->parameter : array();
        $keys = array_map(function($key){return "\x0b" . str_replace("\x0b", '\\x0b', $key) . "\x0b";}, array_keys($params));
        $values = array_map(function($value) use ($self){return $self->quote($value);}, array_values($params));
        $query = '';
        foreach ($lines as $line) {
            if (strpos($line, '?') !== false)  {
                $line = str_replace($keys, $values, strtr($line, '?', "\x0b"));
                if (strpos($line, "\x0b") !== false)  {
                    $line = null;
                } 
            } 
            if ($line) {
                $query .= trim($line) . " ";
            }
        }
        return trim($query);
    }
    
    public function quote($value)
    {
        return strval($value) === strval(intval($value)) ? $value : ("'" . str_replace(
                array('\\', "\0", "\n", "\r", "'", '"', "\x1a", "\x0b"),
                array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z', '\\x0b'),
                $value
            ) . "'");
    }

}