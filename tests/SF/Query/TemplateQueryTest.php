<?php
namespace SF\Query;


class TemplateQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateQuery
     */
    private $target = null;
    public function setUp()
    {
        $this->target = new TemplateQuery("SELECT 
* 
FROM 
  tbl 
WHERE 
(1 = 1) 
AND id = ?id?
AND name = 'name'
");
    }

    public function testQuote()
    {
        $this->assertThat($this->target->quote('1234'), $this->equalTo('1234'));
        $this->assertThat($this->target->quote('hoge'), $this->equalTo("'hoge'"));
        $this->assertThat($this->target->quote("ho'ge"), $this->equalTo("'ho\\'ge'"));
    }
    
    public function testToString()
    {
        $this->target->prepare(array('id' => 1234));
        $this->assertThat($this->target->toString(), $this->equalTo("SELECT * FROM tbl WHERE (1 = 1) AND id = 1234 AND name = 'name'"));
        $this->target->prepare(array());
        $this->assertThat($this->target->toString(), $this->equalTo("SELECT * FROM tbl WHERE (1 = 1) AND name = 'name'"));
    }
}
