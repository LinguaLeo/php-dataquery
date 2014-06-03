<?php
namespace LinguaLeo\DataQuery;

class QueryCompilerTest extends \PHPUnit_Framework_TestCase
{

    protected $query;

    public function setUp()
    {
        parent::setUp();
        $this->result = $this->getMock(
            'ResultInterfaceMock',
            ['keyValue', 'one', 'value', 'many', 'table', 'column']
        );
        $this->query = $this->getMock(
            'LinguaLeo\Query',
            ['select']
        );
    }

    public function testSelectManyFrom()
    {
        $query = $this->query;
        $result = $this->result;
        $query
            ->expects($this->once())
            ->method('select')
            ->with($this->isInstanceOf('LinguaLeo\DataQuery\Criteria'))
            ->will($this->returnValue($result));
        $result
            ->expects($this->once())
            ->method('many');

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

    public function testSelectOneFrom()
    {
        $query = $this->query;
        $result = $this->result;
        $query
            ->expects($this->once())
            ->method('select')
            ->with($this->isInstanceOf('LinguaLeo\DataQuery\Criteria'))
            ->will($this->returnValue($result));
        $result
            ->expects($this->once())
            ->method('one');

        $id = 3;
        eval(QueryCompiler::compile('$query', [
            'select' => 'one',
            'from' => [
                'table' => 'user',
                'meta' => [ 'locale' => 'ru' ]
            ],
            'read' => [ 'a', 'b', 'c' ],
            'where' => [ 'id' => '$id' ]
        ]));
    }

    public function testSelectKeyValueFrom()
    {
        $query = $this->query;
        $result = $this->result;
        $query
            ->expects($this->once())
            ->method('select')
            ->with($this->isInstanceOf('LinguaLeo\DataQuery\Criteria'))
            ->will($this->returnValue($result));
        $result
            ->expects($this->once())
            ->method('keyValue');

        $id = 3;
        eval(QueryCompiler::compile('$query', [
            'select' => 'keyValue',
            'from' => [
                'table' => 'user',
                'meta' => [ 'locale' => 'ru' ]
            ],
            'read' => [ 'a', 'b', 'c' ],
            'where' => [ 'id' => '$id' ]
        ]));
    }

    public function testSelectTableFrom()
    {
        $query = $this->query;
        $result = $this->result;
        $query
            ->expects($this->once())
            ->method('select')
            ->with($this->isInstanceOf('LinguaLeo\DataQuery\Criteria'))
            ->will($this->returnValue($result));
        $result
            ->expects($this->once())
            ->method('table');

        $id = 3;
        eval(QueryCompiler::compile('$query', [
            'select' => 'table',
            'from' => [
                'table' => 'user',
                'meta' => [ 'locale' => 'ru' ]
            ],
            'read' => [ 'a', 'b', 'c' ],
            'where' => [ 'id' => '$id' ]
        ]));
    }

    public function testSelectValueFrom()
    {
        $query = $this->query;
        $result = $this->result;
        $query
            ->expects($this->once())
            ->method('select')
            ->with($this->isInstanceOf('LinguaLeo\DataQuery\Criteria'))
            ->will($this->returnValue($result));
        $result
            ->expects($this->once())
            ->method('value')
            ->with($this->equalTo('id'));

        $id = 3;
        eval(QueryCompiler::compile('$query', [
            'select' => [ 'value', 'id' ],
            'from' => [
                'table' => 'user',
                'meta' => [ 'locale' => 'ru' ]
            ],
            'read' => [ 'a', 'b', 'c' ],
            'where' => [ 'id' => '$id' ]
        ]));
    }

    public function testSelectColumnFrom()
    {
        $query = $this->query;
        $result = $this->result;
        $query
            ->expects($this->once())
            ->method('select')
            ->with($this->isInstanceOf('LinguaLeo\DataQuery\Criteria'))
            ->will($this->returnValue($result));
        $result
            ->expects($this->once())
            ->method('column')
            ->with($this->equalTo(3));

        $id = 3;
        eval(QueryCompiler::compile('$query', [
            'select' => [ 'column', 3 ],
            'from' => [
                'table' => 'user',
                'meta' => [ 'locale' => 'ru' ]
            ],
            'read' => [ 'a', 'b', 'c' ],
            'where' => [ 'id' => '$id' ]
        ]));
    }

}
