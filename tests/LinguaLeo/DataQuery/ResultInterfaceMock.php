<?php
namespace LinguaLeo\DataQuery;

class ResultInterfaceMock implements ResultInterface
{
    public function count()
    {
        return 1;
    }

    /**
     * Returns a hash
     *
     * @return array
     */
    public function keyValue()
    {
        return [];
    }

    /**
     * Returns a row
     *
     * @return array
     */
    public function one()
    {
        return [];
    }

    /**
     * Returns a value
     *
     * @return mixed
     */
    public function value($name)
    {
        return 0;
    }

    /**
     * Returns an array of rows
     *
     * @return array
     */
    public function many()
    {
        return [];
    }

    /**
     * Returns an array of columns
     *
     * @return array
     */
    public function table()
    {
        return [];
    }

    /**
     * Returns a column
     *
     * @return array
     */
    public function column($number)
    {
        return [];
    }

    /**
     * Free the result
     *
     * @return bool
     */
    public function free()
    {
        return true;
    }
}
