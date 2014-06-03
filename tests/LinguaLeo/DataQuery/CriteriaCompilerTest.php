<?php
namespace LinguaLeo\DataQuery;

class CriteriaCompilerTest extends \PHPUnit_Framework_TestCase
{

    public function testWhereEquals()
    {
        $userIdList = [1, 2, 3];
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['where' => ['user_id' => 3]]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame([['user_id', 3, Criteria::EQUAL]], $criteria->conditions);
    }

    public function testWhereGreater()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['where' => ['user_id' => [Criteria::GREATER => 2]]]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame([['user_id', 2, Criteria::GREATER]], $criteria->conditions);
    }

    public function testWhereGreaterAndLess()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['where' => [
                'user_id' => [
                    Criteria::GREATER => 2,
                    Criteria::LESS => 20,
                ]
            ]]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame([['user_id', 2, Criteria::GREATER], ['user_id', 20, Criteria::LESS]], $criteria->conditions);
    }

    public function testWhereGreaterAndLessAndActive()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['where' => [
                'user_id' => [
                    Criteria::GREATER => 2,
                    Criteria::LESS => 20,
                ],
                'user_is_active' => 1
            ]]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
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
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['limit' => 10]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame(10, $criteria->limit);
    }

    public function testLimitOffset()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['limit' => [10, 30]]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame(10, $criteria->limit);
        $this->assertSame(30, $criteria->offset);
    }

    public function testRead()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['read' => ['a', 'b']]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame(['a', 'b'], $criteria->fields);
    }

    public function testAggregate()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['aggregate' => [
                ['count'],
                ['sum', 'a']
            ]]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame([['count', null], ['sum', 'a']], $criteria->aggregations);
    }

    public function testWrite()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['write' => ['a' => 1, 'b' =>2]]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame(['a', 'b'], $criteria->fields);
        $this->assertSame([1, 2], $criteria->values);
    }

    public function testWritePipe()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['writePipe' => [
                ['a' => 1, 'b' => 2],
                ['a' => 3, 'b' => 4],
                ['a' => 5, 'b' => 6]
            ]]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame(['a', 'b'], $criteria->fields);
        $this->assertSame([[1, 3, 5], [2, 4, 6]], $criteria->values);
    }

    public function testUpsert()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['upsert' => ['a']]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
        $this->assertSame(['a'], $criteria->upsert);
    }

    public function testOrderBy()
    {
        list($fn, $code) = CriteriaCompiler::create(
            'user',
            ['orderBy' => ['a', ['b', SORT_DESC]]]
        );
        $criteria = eval($code . 'return ' . $fn . '();');
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
            list($fn, $code) = CriteriaCompiler::create(
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
            );
            return eval($code . 'return ' . $fn . '();');
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
