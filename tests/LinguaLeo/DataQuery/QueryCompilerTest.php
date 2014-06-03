<?php
namespace LinguaLeo\DataQuery;

class QueryCompilerTest extends \PHPUnit_Framework_TestCase
{

    protected $query;

    public function setUp()
    {
        parent::setUp();
        $this->query = $this->getMock(
            'LinguaLeo\Query',
            ['select']
        );
    }

    public function testSelectFrom()
    {
        $query = $this->query;
        $query
            ->expects($this->once())
            ->method('select')
            ->with($this->isInstanceOf('LinguaLeo\DataQuery\Criteria'))
            ->will($this->returnValue(new ResultInterfaceMock()));

        $id = 3;
        eval(QueryCompiler::compile('$query', [
            'select' => 'many',
            'from' => [
                'table' => 'user',
                'meta' => [ 'locale' => 'ru' ]
            ],
            'read' => [ 'a', 'b', 'c' ],
            'where' => [ 'id' => '$id' ]
        ]));
    }

}
