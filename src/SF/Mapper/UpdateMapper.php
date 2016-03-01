<?php
namespace SF\Mapper;

use SF\APISettings;
use SF\Client;
use SF\Mapper;

class UpdateMapper extends Client implements Mapper
{
    private $url = null;
    public function __construct($url, APISettings $settings)
    {
        $this->url = $url;
        parent::__construct($settings);
    }

    public function map($entity)
    {
        $url = $this->url . $entity->Id . '?_HttpMethod=PATCH';
        unset($entity->Id);
        $body = json_encode($entity);
        $this->post($url, $body);
        return $entity;
    }

    public function buildHeader()
    {
        $array =  parent::buildHeader();
        $array[] = 'Content-Type: application/json';
        return $array;
    }
}