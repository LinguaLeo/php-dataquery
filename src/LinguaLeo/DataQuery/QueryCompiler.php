<?php
namespace LinguaLeo\DataQuery;

class QueryCompiler
{

    public static function compileFunction($queryVariableName, array $query)
    {
        $variables = CriteriaCompiler::detectVariables($query);
        $invokedQuery = '';
        list($location, $meta) = self::getFrom($query);
        list($method, $quantity) = self::getMethod($query);
        list($criteriaFunctionName, $criteriaFunctionCode) =
            CriteriaCompiler::create($location, $query, $meta);
        $functionName = '$generatedFunction' . uniqid();
        $code = $criteriaFunctionCode .
            $functionName . ' = function () use (' . $queryVariableName . ', ' . $criteriaFunctionName . ') {' . PHP_EOL .
            'return ' . $queryVariableName . '->' . $method . '(' .
            $criteriaFunctionName . '())->' . $quantity . '();' . PHP_EOL .
            '};';
        return [$functionName, $code];
    }

    public static function compile($queryVariableName, array $query)
    {
        list($fn, $code) = self::compileFunction($queryVariableName, $query);
        return ($code . 'return ' . $fn . '();');
    }

    private static function getFrom(array $query)
    {
        return [$query['from']['table'], $query['from']['meta']];
    }

    private static function getMethod(array $query)
    {
        if (isset($query['select'])) {
            return ['select', $query['select']];
        } else {
            // throw smth
        }
    }

}
