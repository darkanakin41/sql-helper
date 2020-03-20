<?php


namespace Darkanakin41\SQLHelper\Query\Expression;


class From
{
    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var Join[]
     */
    protected $joins = array();

    /**
     * @param string $from La table
     * @param string $alias L'alias
     */
    public function __construct($from, $alias)
    {
        $this->from = $from;
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param Join $join
     */
    public function addJoin($join)
    {
        $this->joins[] = $join;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $parts = array($this->from);
        if (!empty($this->alias)) {
            array_push($parts, 'AS ', $this->alias);
        }
        if (count($this->joins) > 0) {
            array_push($parts, implode(' ', $this->joins));
        }

        return implode(' ', $parts);
    }
}
