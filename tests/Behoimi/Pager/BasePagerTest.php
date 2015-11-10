<?php
namespace Behoimi\Pager;


use Hoimi\ArrayContainer;

class BasePagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BasePager
     */
    private $target = null;
    public function setUp ()
    {
        $this->target = new DummyPager(new ArrayContainer(array(
            'order' => 'col1Key',
            'direction' => 'DESC',
            'limit' => 10,
            'page' => 2,
        )));
    }

    public function testBuildSQL ()
    {
        $this->assertSame('ORDER BY col1Name DESC LIMIT 11 OFFSET 10', $this->target->buildSQL());
    }

    public function testBuildSQLDefault ()
    {
        $this->assertSame('ORDER BY col5Name ASC LIMIT 51 OFFSET 0', (new DummyPager(new ArrayContainer(array())))->buildSQL());
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testInvalidOrderKey ()
    {
        new DummyPager(new ArrayContainer(array('order' => 'col6Key')));
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testInvalidDirection ()
    {
        new DummyPager(new ArrayContainer(array('direction' => 'hoge')));
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testInvalidPage ()
    {
        new DummyPager(new ArrayContainer(array('page' => 0)));
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testInvalidPageString ()
    {
        new DummyPager(new ArrayContainer(array('page' => 'hoge')));
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testInvalidLimit ()
    {
        new DummyPager(new ArrayContainer(array('limit' => -1)));
    }

    /**
     * @expectedException \Hoimi\Exception\ValidationException
     */
    public function testInvalidLimitString ()
    {
        new DummyPager(new ArrayContainer(array('limit' => 'hoge')));
    }
}

class DummyPager extends BasePager
{

    public function getColumns()
    {
        return array (
            'col1Key' => 'col1Name',
            'col2Key' => 'col2Name',
            'col3Key' => 'col3Name',
            'col4Key' => 'col4Name',
            'col5Key' => 'col5Name',
        );
    }

    public function getDefaultColumnKey()
    {
        return 'col5Key';
    }

    public function getDefaultDirection()
    {
        return 'ASC';
    }
}
