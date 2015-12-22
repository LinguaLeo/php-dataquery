<?php
namespace LinguaLeo\DataQuery;

class CriteriaFactory
{

    private static $keywordMap = [
        'where' => 'applyWhere',
        'limit' => 'applyLimit',
        'read' => 'applyRead',
        'aggregate' => 'applyAggregate',
        'write' => 'applyWrite',
        'writePipe' => 'applyWritePipe',
        'upsert' => 'applyUpsert',
        'orderBy' => 'applyOrderBy'
    ];

    /**
     * @param string $location
     * @param array $condition
     * @param array $meta
     */
    public static function create($location, array $condition, array $meta = [])
    {
        $criteria = new Criteria($location, $meta);
        foreach ($condition as $keyword => $attributes) {
            self::applyKeyword($criteria, $keyword, $attributes);
        }
        return $criteria;
    }

    /**
     *
     * @param Criteria $criteria
     * @param string $keyword
     * @param mixed $attributes
     */
    public static function applyKeyword(Criteria &$criteria, $keyword, $attributes)
    {
        if (self::$keywordMap[$keyword]) {
            $map = self::$keywordMap;
            self::$map[$keyword]($criteria, $attributes);
        } else {
            // throw smth here?
        }
    }

    /**
     * @param Criteria $criteria
     * @param mixed $attributes
     */
    private static function applyWhere(Criteria &$criteria, $attributes)
    {
        foreach ($attributes as $column => $bounds) {
            if (is_array($bounds)) {
                foreach ($bounds as $comparison => $value) {
                    $criteria->where($column, $value, $comparison);
                }
            } else {
                $criteria->where($column, $bounds);
            }
        }
    }

    /**
     * @param Criteria $criteria
     * @param mixed $attributes
     */
    private static function applyLimit(Criteria &$criteria, $attributes)
    {
        if (is_array($attributes) && isset($attributes[1])) {
            $criteria->limit($attributes[0], $attributes[1]);
        } else {
            $criteria->limit($attributes);
        }
    }

    /**
     * @param Criteria $criteria
     * @param mixed $attributes
     */
    private static function applyRead(Criteria &$criteria, $attributes)
    {
        $criteria->read($attributes);
    }

    /**
     * @param Criteria $criteria
     * @param mixed $attributes
     */
    private static function applyAggregate(Criteria &$criteria, $attributes)
    {
        foreach ($attributes as $aggregation) {
            call_user_func_array([$criteria, 'aggregate'], $aggregation);
        }
    }

    /**
     * @param Criteria $criteria
     * @param mixed $attributes
     */
    private static function applyWrite(Criteria &$criteria, $attributes)
    {
        $criteria->write($attributes);
    }

    /**
     * @param Criteria $criteria
     * @param mixed $attributes
     */
    private static function applyWritePipe(Criteria &$criteria, $attributes)
    {
        foreach ($attributes as $attribute) {
            $criteria->writePipe($attribute);
        }
    }

    /**
     * @param Criteria $criteria
     * @param mixed $attributes
     */
    private static function applyUpsert(Criteria &$criteria, $attributes)
    {
        $criteria->upsert($attributes);
    }

    /**
     * @param Criteria $criteria
     * @param mixed $attributes
     */
    private static function applyOrderBy(Criteria &$criteria, $attributes)
    {
        foreach ($attributes as $attribute) {
            call_user_func_array(
                [$criteria, 'orderBy'],
                is_array($attribute) ? $attribute : [$attribute]
            );
        }
    }

}
