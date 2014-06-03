<?php
namespace LinguaLeo\DataQuery;

class CriteriaCompilerTest extends \PHPUnit_Framework_TestCase
{

    public function testWhereEquals()
    {
        $userIdList = [1, 2, 3];
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['where' => ['user_id' => 3]]
        ));
        $this->assertSame([['user_id', 3, Criteria::EQUAL]], $criteria->conditions);
    }

    public function testWhereGreater()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['where' => ['user_id' => [Criteria::GREATER => 2]]]
        ));
        $this->assertSame([['user_id', 2, Criteria::GREATER]], $criteria->conditions);
    }

    public function testWhereGreaterAndLess()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['where' => [
                'user_id' => [
                    Criteria::GREATER => 2,
                    Criteria::LESS => 20,
                ]
            ]]
        ));
        $this->assertSame([['user_id', 2, Criteria::GREATER], ['user_id', 20, Criteria::LESS]], $criteria->conditions);
    }

    public function testWhereGreaterAndLessAndActive()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['where' => [
                'user_id' => [
                    Criteria::GREATER => 2,
                    Criteria::LESS => 20,
                ],
                'user_is_active' => 1
            ]]
        ));
        $this->assertSame(
            [
                ['user_id', 2, Criteria::GREATER],
                ['user_id', 20, Criteria::LESS],
                ['user_is_active', 1, Criteria::EQUAL]
            ],
            $criteria->conditions
        );
    }

    public function testLimit()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['limit' => 10]
        ));
        $this->assertSame(10, $criteria->limit);
    }

    public function testLimitOffset()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['limit' => [10, 30]]
        ));
        $this->assertSame(10, $criteria->limit);
        $this->assertSame(30, $criteria->offset);
    }

    public function testRead()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['read' => ['a', 'b']]
        ));
        $this->assertSame(['a', 'b'], $criteria->fields);
    }

    public function testAggregate()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['aggregate' => [
                ['count'],
                ['sum', 'a']
            ]]
        ));
        $this->assertSame([['count', null], ['sum', 'a']], $criteria->aggregations);
    }

    public function testWrite()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['write' => ['a' => 1, 'b' =>2]]
        ));
        $this->assertSame(['a', 'b'], $criteria->fields);
        $this->assertSame([1, 2], $criteria->values);
    }

    public function testWritePipe()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['writePipe' => [
                ['a' => 1, 'b' => 2],
                ['a' => 3, 'b' => 4],
                ['a' => 5, 'b' => 6]
            ]]
        ));
        $this->assertSame(['a', 'b'], $criteria->fields);
        $this->assertSame([[1, 3, 5], [2, 4, 6]], $criteria->values);
    }

    public function testUpsert()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['upsert' => ['a']]
        ));
        $this->assertSame(['a'], $criteria->upsert);
    }

    public function testOrderBy()
    {
        $criteria = eval(CriteriaCompiler::compile(
            'user',
            ['orderBy' => ['a', ['b', SORT_DESC]]]
        ));
        $this->assertSame(['a' => SORT_ASC, 'b' => SORT_DESC], $criteria->orderBy);
    }

    public function testReadWhereOrderByLimit()
    {
        $outerMin = 2;
        $outerMax = 20;
        $outerFields = ['a', 'b'];
        $outerLimit = 10;
        $outerOffset = 30;
        $fn = function ($min, $max, $fields, $limit, $offset) {
            return eval(CriteriaCompiler::compile(
                'user',
                [
                    'read' => '$fields',
                    'where' => [
                        'user_id' => [
                            Criteria::GREATER => '$min',
                            Criteria::LESS => '$max',
                        ],
                        'user_is_active' => 1
                    ],
                    'orderBy' => ['c', ['d', SORT_DESC]],
                    'limit' => ['$limit', '$offset']
                ]
            ));
        };
        $criteria = $fn($outerMin, $outerMax, $outerFields, $outerLimit, $outerOffset);
        $this->assertSame(['a', 'b'], $criteria->fields);
        $this->assertSame(
            [
                ['user_id', $outerMin, Criteria::GREATER],
                ['user_id', $outerMax, Criteria::LESS],
                ['user_is_active', 1, Criteria::EQUAL]
            ],
            $criteria->conditions
        );
        $this->assertSame(['c' => SORT_ASC, 'd' => SORT_DESC], $criteria->orderBy);
        $this->assertSame(10, $criteria->limit);
        $this->assertSame(30, $criteria->offset);
    }

}
