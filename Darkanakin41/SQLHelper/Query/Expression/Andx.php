<?php


namespace Darkanakin41\SQLHelper\Query\Expression;


class Andx extends Composite
{
    /**
     * @var string
     */
    protected $separator = ' AND ';

    /**
     * @var array
     */
    protected $allowedClasses = array(
        'Darkanakin41\SQLHelper\Query\Expression\Comparison',
        'Darkanakin41\SQLHelper\Query\Expression\Func',
        'Darkanakin41\SQLHelper\Query\Expression\Orx',
        'Darkanakin41\SQLHelper\Query\Expression\Andx',
    );

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }
}
