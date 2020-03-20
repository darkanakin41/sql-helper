<?php


namespace Darkanakin41\SQLHelper\Query\Expression;



class Orx extends Composite
{
    /**
     * @var string
     */
    protected $separator = ' OR ';

    /**
     * @var array
     */
    protected $allowedClasses = array(
        'Darkanakin41\SQLHelper\Query\Expression\Comparison',
        'Darkanakin41\SQLHelper\Query\Expression\Func',
        'Darkanakin41\SQLHelper\Query\Expression\Andx',
        'Darkanakin41\SQLHelper\Query\Expression\Orx',
    );

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }
}
