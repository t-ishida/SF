<?php
const SETTINGS = __DIR__ . '/.salesforce';
require_once __DIR__ . '/../vendor/autoload.php';
$settings = new SF\APISettings(SETTINGS);

$mapper = new \SF\Mapper\UpdateMapper('/services/data/v20.0/sobjects/Product2/', $settings);
$mapper->map((object)array('Id' => '01t10000002FnBP', 'Name' => 'いしいテスト => updated'));
