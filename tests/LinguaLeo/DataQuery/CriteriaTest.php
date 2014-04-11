<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 LinguaLeo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace LinguaLeo\DataQuery;

class CriteriaTest extends \PHPUnit_Framework_TestCase
{
    protected $criteria;

    public function setUp()
    {
        $this->criteria = new Criteria('foo', ['locale' => 'ru']);
    }

    public function testWhere()
    {
        $this->criteria->where('baz', 1, Criteria::GREATER);
        $this->assertSame([['baz', 1, Criteria::GREATER]], $this->criteria->conditions);
    }

    public function testWhereMany()
    {
        $this->criteria
            ->where('baz', 1, Criteria::GREATER)
            ->where('quux', 2, Criteria::NOT_EQUAL);
        $this->assertSame([
            ['baz', 1, Criteria::GREATER],
            ['quux', 2, Criteria::NOT_EQUAL],
        ], $this->criteria->conditions);
    }

    public function testLimit()
    {
        $this->criteria->limit(1);
        $this->assertSame(1, $this->criteria->limit);
        $this->assertSame(0, $this->criteria->offset);
    }

    public function testLimitOffset()
    {
        $this->criteria->limit(1, 2);
        $this->assertSame(1, $this->criteria->limit);
        $this->assertSame(2, $this->criteria->offset);
    }

    public function testRead()
    {
        $this->criteria->read(['a', 'b']);
        $this->assertSame(['a', 'b'], $this->criteria->fields);
    }

    public function testReadReset()
    {
        $this->criteria->read(['a', 'b']);
        $this->criteria->read(['c', 'd']);
        $this->assertSame(['c', 'd'], $this->criteria->fields);
    }

    public function testAggregate()
    {
        $this->criteria->aggregate('count');
        $this->criteria->aggregate('sum', 'a');
        $this->assertSame([['count', null], ['sum', 'a']], $this->criteria->aggregations);
    }

    public function testWrite()
    {
        $this->criteria->write(['a' => 1, 'b' => null]);
        $this->assertSame(['a', 'b'], $this->criteria->fields);
        $this->assertSame([1, null], $this->criteria->values);
    }

    public function testWriteReset()
    {
        $this->criteria->write(['a' => 1, 'b' => 2]);
        $this->criteria->write(['c' => 3, 'd' => 4]);
        $this->assertSame(['c', 'd'], $this->criteria->fields);
        $this->assertSame([3, 4], $this->criteria->values);
    }

    public function testWritePipeOne()
    {
        $this->criteria->writePipe(['a' => 1, 'b' => 2]);
        $this->assertSame(['a', 'b'], $this->criteria->fields);
        $this->assertSame([1, 2], $this->criteria->values);
    }

    public function testWritePipeMany()
    {
        $this->criteria
            ->writePipe(['a' => 1, 'b' => 2])
            ->writePipe(['a' => 3, 'b' => 4])
            ->writePipe(['a' => 5, 'b' => 6]);

        $this->assertSame(['a', 'b'], $this->criteria->fields);
        $this->assertSame([[1,3,5], [2,4,6]], $this->criteria->values);
    }

    public function testWritePipeDefinedFields()
    {
        $this->criteria
            ->writePipe(['a' => 1, 'b' => 2])
            ->writePipe(['a' => 3, 'b' => 4, 'c' => 3]);

        $this->assertSame(['a', 'b'], $this->criteria->fields);
        $this->assertSame([[1,3], [2,4]], $this->criteria->values);
    }

    public function testWritePipeDefinedFieldsAsNullable()
    {
        $this->criteria
            ->writePipe(['a' => null, 'b' => 2])
            ->writePipe(['a' => 3, 'b' => null]);

        $this->assertSame(['a', 'b'], $this->criteria->fields);
        $this->assertSame([[null,3], [2,null]], $this->criteria->values);
    }

    /**
     * @expectedException \LinguaLeo\DataQuery\Exception\CriteriaException
     * @expectedExceptionMessage The field c not found in values
     */
    public function testWritePipeUndefinedFields()
    {
        $this->criteria
            ->writePipe(['a' => 1, 'b' => 2, 'c' => 3])
            ->writePipe(['a' => 4, 'b' => 5]);
    }

    public function testOrderBy()
    {
        $this->criteria->orderBy('a');
        $this->criteria->orderBy('b', SORT_DESC);
        $this->assertSame(['a' => SORT_ASC, 'b' => SORT_DESC], $this->criteria->orderBy);
    }

    public function testUpsert()
    {
        $this->criteria->upsert(['a']);
        $this->assertSame(['a'], $this->criteria->upsert);
    }

    public function testGetMeta()
    {
        $this->assertSame('ru', $this->criteria->getMeta('locale'));
    }

    /**
     * @expectedException \LinguaLeo\DataQuery\Exception\CriteriaException
     * @expectedExceptionMessage The trololo meta value not found
     */
    public function testUndefinedGetMeta()
    {
        $this->criteria->getMeta('trololo');
    }

    public function testSetMeta()
    {
        $this->criteria->setMeta('atata', 'ololo');
        $this->assertSame('ololo', $this->criteria->getMeta('atata'));
    }
}