<?php
namespace LinguaLeo\DataQuery;

class CriteriaCompiler
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

    public static function create($location, array $condition, array $meta = [])
    {
        $variables = self::detectVariables($condition);
        $invokedCondition = '';
        foreach ($condition as $keyword => $attributes) {
            $invokedCondition .=
                self::applyKeyword('$criteria', $keyword, $attributes) . PHP_EOL;
        }
        $functionName = 'generatedFunction' . uniqid();
        $code = sprintf(
            '$%s = function () %s {' . PHP_EOL .
            '$criteria = new LinguaLeo\DataQuery\Criteria(%s, %s);' . PHP_EOL .
            '%s' .
            'return $criteria;' . PHP_EOL .
            '};' . PHP_EOL .
            'return $%s();' . PHP_EOL,
            $functionName,
            $variables ? sprintf('use (%s)', implode(', ', $variables)) : '',
            var_export($location, true),
            var_export($meta, true),
            $invokedCondition,
            $functionName
        );
        return $code;
    }

    public static function applyKeyword($criteriaVar, $keyword, $attributes)
    {
        if (self::$keywordMap[$keyword]) {
            $map = self::$keywordMap;
            return self::$map[$keyword]($criteriaVar, $attributes);
        } else {
            // throw smth here?
        }
    }

    private static function applyWhere($criteriaVar, $attributes)
    {
        $code = '';
        foreach ($attributes as $column => $bounds) {
            if (is_array($bounds)) {
                foreach ($bounds as $comparison => $value) {
                    $code .= sprintf(
                        '%s->where(%s, %s, %s);' . PHP_EOL,
                        $criteriaVar,
                        self::compileScalar($column),
                        self::compileScalar($value),
                        self::compileScalar($comparison)
                    );
                }
            } else {
                $code .= sprintf(
                    '%s->where(%s, %s);' . PHP_EOL,
                    $criteriaVar,
                    self::compileScalar($column),
                    self::compileScalar($bounds)
                );
            }
        }
        return $code;
    }

    private static function applyLimit($criteriaVar, $attributes)
    {
        $code = '';
        if (is_array($attributes) && isset($attributes[1])) {
            $code .= sprintf(
                '%s->limit(%s, %s);',
                $criteriaVar,
                self::compileScalar($attributes[0]),
                self::compileScalar($attributes[1])
            ); 
        } else {
            $code .= sprintf(
                '%s->limit(%s);',
                $criteriaVar,
                self::compileScalar($attributes)
            ); 
        }
        return $code;
    }

    private static function applyRead($criteriaVar, $attributes)
    {
        return sprintf(
            '%s->read(%s);' . PHP_EOL,
            $criteriaVar,
            self::compileScalar($attributes)
        );
    }

    private static function applyAggregate($criteriaVar, $attributes)
    {
        $code = '';
        foreach ($attributes as $aggregation) {
            $code .= sprintf(
                '%s->aggregate(%s);' . PHP_EOL,
                $criteriaVar,
                self::compileArrayOfScalars($aggregation)
            );
        }
        return $code;
    }

    private static function applyWrite($criteriaVar, $attributes)
    {
        return sprintf(
            '%s->write(%s);' . PHP_EOL,
            $criteriaVar,
            self::compileScalar($attributes)
        );
    }

    private static function applyWritePipe($criteriaVar, $attributes)
    {
        $code = '';
        foreach ($attributes as $attribute) {
            $code .= sprintf(
                '%s->writePipe(%s);' . PHP_EOL,
                $criteriaVar,
                self::compileScalar($attribute)
            );
        }
        return $code;
    }

    private static function applyUpsert($criteriaVar, $attributes)
    {
        return sprintf(
            '%s->upsert(%s);' . PHP_EOL,
            $criteriaVar,
            self::compileScalar($attributes)
        );
    }

    private static function applyOrderBy($criteriaVar, $attributes)
    {
        $code = '';
        foreach ($attributes as $attribute) {
            $code .= sprintf(
                '%s->orderBy(%s);' . PHP_EOL,
                $criteriaVar,
                self::compileArrayOfScalars((array)$attribute)
            );
        }
        return $code;
    }

    private static function compileScalar($scalar)
    {
        if (self::isVariable($scalar)) {
            return $scalar;
        } else {
            return var_export($scalar, true);
        }
    }

    private static function compileArrayOfScalars($scalars)
    {
        return implode(', ', array_map(['self', 'compileScalar'], $scalars));
    }

    /**
     * TODO detect variables in expressions, so
     * 'limit' => [ '$size', '$page * $size' ] 
     * have ['$size', '$page'], not ['$size', '$page * $size'] variables
     */
    private static function detectVariables($code)
    {
        $variables = [];
        foreach ($code as $key => $value) {
            if (self::isVariable($key)) {
                $variables[] = $key;
            }
            if (is_array($value)) {
                $variables = array_merge($variables, self::detectVariables($value));
            } elseif (self::isVariable($value)) {
                $variables[] = $value;
            }
        }
        return array_unique($variables);
    }

    private static function isVariable($str)
    {
        return is_string($str) && (strpos($str, '$') === 0);
    }

}
