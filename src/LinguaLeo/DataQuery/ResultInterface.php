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

interface ResultInterface extends \Countable
{
    /**
     * Returns a hash
     *
     * @return array
     */
    public function keyValue();

    /**
     * Returns a row
     *
     * @return array
     */
    public function one();

    /**
     * Returns a value
     *
     * @return mixed
     */
    public function value($name);

    /**
     * Returns an array of rows
     *
     * @return array
     */
    public function many();

    /**
     * Returns an array of columns
     *
     * @return array
     */
    public function table();

    /**
     * Returns a column
     *
     * @return array
     */
    public function column($number);

    /**
     * Free the result
     *
     * @return bool
     */
    public function free();
}