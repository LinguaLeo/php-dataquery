<?php
namespace LinguaLeo\DataQuery;

class QueryCompiler
{

    public static function compileFunction($queryVariableName, array $query)
    {
        $variables = CriteriaCompiler::detectVariables($query);
        $invokedQuery = '';
        list($location, $meta) = self::getFrom($query);
        list($method, $quantity, $args) = self::getMethod($query);
        list($criteriaFunctionName, $criteriaFunctionCode) =
            CriteriaCompiler::compileFunction($location, $query, $meta);
        $functionName = '$generatedFunction' . uniqid();
        $code = sprintf(
            '%s' .
            '%s = function () use (%s, %s) {' . PHP_EOL .
            'return %s->%s(' .
            '%s())->%s(%s);' . PHP_EOL .
            '};',
            $criteriaFunctionCode,
            $functionName,
            $queryVariableName,
            $criteriaFunctionName,
            $queryVariableName,
            $method,
            $criteriaFunctionName,
            $quantity,
            var_export($args, true)
        );
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
            $resultSpecification = (array)$query['select'];
            $resultMethod = array_shift($resultSpecification);
            return [
                'select',
                $resultMethod,
                $resultSpecification ? $resultSpecification[0] : []
            ];
        } else {
            // throw smth
        }
    }

}
