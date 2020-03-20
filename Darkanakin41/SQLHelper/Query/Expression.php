<?php

namespace Darkanakin41\SQLHelper\Query;

class Expression
{
    /**
     * Build an OR condition
     *
     * @param $args
     *
     * @return string
     */
    public static function orX(){
        return new Expression\Orx(func_get_args());
    }
    /**
     * Build an AND condition
     *
     * @param $args
     *
     * @return string
     */
    public static function andX(){
        return new Expression\Andx(func_get_args());
    }

    /**
     * Build an IN condition
     *
     * @param $field
     * @param $values
     *
     * @return string
     */
    public static function in($field, $values){
        if (is_array($values)) {
            foreach ($values as &$literal) {
                if ( ! ($literal instanceof Expression\Literal)) {
                    $literal = Expression\Literal::_quote($literal);
                }
            }
        }
        return new Expression\Func($field . ' IN', $values);
    }
}
