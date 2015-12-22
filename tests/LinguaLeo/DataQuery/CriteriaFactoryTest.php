<?php
namespace LinguaLeo\DataQuery;

class CriteriaFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testLocation()
    {
        $criteria = CriteriaFactory::create('user', []);
        $this->assertSame('user', $criteria->location);
    }

    public function testMeta()
    {
        $criteria = CriteriaFactory::create('user', [], ['locale' => 'ru']);
        $this->assertSame('ru', $criteria->getMeta('locale'));
    }

    public function testWhereEquals()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['where' => ['user_id' => 3]]
        );
        $this->assertSame([['user_id', 3, Criteria::EQUAL]], $criteria->conditions);
    }

    public function testWhereGreater()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['where' => ['user_id' => [Criteria::GREATER => 2]]]
        );
        $this->assertSame([['user_id', 2, Criteria::GREATER]], $criteria->conditions);
    }

    public function testWhereGreaterAndLess()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['where' => [
                'user_id' => [
                    Criteria::GREATER => 2,
                    Criteria::LESS => 20,
                ]
            ]]
        );
        $this->assertSame([['user_id', 2, Criteria::GREATER], ['user_id', 20, Criteria::LESS]], $criteria->conditions);
    }

    public function testWhereGreaterAndLessAndActive()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['where' => [
                'user_id' => [
                    Criteria::GREATER => 2,
                    Criteria::LESS => 20,
                ],
                'user_is_active' => 1
            ]]
        );
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
        $criteria = CriteriaFactory::create(
            'user',
            ['limit' => 10]
        );
        $this->assertSame(10, $criteria->limit);
    }

    public function testLimitOffset()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['limit' => [10, 30]]
        );
        $this->assertSame(10, $criteria->limit);
        $this->assertSame(30, $criteria->offset);
    }

    public function testRead()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['read' => ['a', 'b']]
        );
        $this->assertSame(['a', 'b'], $criteria->fields);
    }

    public function testAggregate()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['aggregate' => [
                ['count'],
                ['sum', 'a']
            ]]
        );
        $this->assertSame([['count', null], ['sum', 'a']], $criteria->aggregations);
    }

    public function testWrite()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['write' => ['a' => 1, 'b' =>2]]
        );
        $this->assertSame(['a', 'b'], $criteria->fields);
        $this->assertSame([1, 2], $criteria->values);
    }

    public function testWritePipe()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['writePipe' => [
                ['a' => 1, 'b' => 2],
                ['a' => 3, 'b' => 4],
                ['a' => 5, 'b' => 6]
            ]]
        );
        $this->assertSame(['a', 'b'], $criteria->fields);
        $this->assertSame([[1, 3, 5], [2, 4, 6]], $criteria->values);
    }

    public function testUpsert()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['upsert' => ['a']]
        );
        $this->assertSame(['a'], $criteria->upsert);
    }

    public function testOrderBy()
    {
        $criteria = CriteriaFactory::create(
            'user',
            ['orderBy' => ['a', ['b', SORT_DESC]]]
        );
        $this->assertSame(['a' => SORT_ASC, 'b' => SORT_DESC], $criteria->orderBy);
    }

}
