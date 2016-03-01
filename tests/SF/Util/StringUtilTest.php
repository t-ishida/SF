<?php
namespace SF\Util;


class StringUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testParseCSV()
    {
        $this->assertEquals(array("1", "2", "3\n\n4","5"), StringUtil::parseCSV('1,"2"                  , "3

4",5'));
    }

}
