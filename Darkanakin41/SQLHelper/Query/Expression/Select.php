<?php


namespace Darkanakin41\SQLHelper\Query\Expression;


class Select extends Base
{
    /**
     * @var string
     */
    protected $preSeparator = '';

    /**
     * @var string
     */
    protected $postSeparator = '';

    /**
     * @var array
     */
    protected $allowedClasses = array(
        'Darkanakin41\SQLHelper\Query\Expression\Func'
    );

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }
}
