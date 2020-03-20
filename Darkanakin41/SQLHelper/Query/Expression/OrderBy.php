<?php


namespace Darkanakin41\SQLHelper\Query\Expression;


class OrderBy
{
    /**
     * @var string
     */
    protected $preSeparator = '';

    /**
     * @var string
     */
    protected $separator = ', ';

    /**
     * @var string
     */
    protected $postSeparator = '';

    /**
     * @var array
     */
    protected $allowedClasses = array();

    /**
     * @var array
     */
    protected $parts = array();

    /**
     * @param string|null $sort
     * @param string|null $order
     */
    public function __construct($sort = null, $order = null)
    {
        if ($sort) {
            $this->add($sort, $order);
        }
    }

    /**
     * @param string      $sort
     * @param string|null $order
     *
     * @return void
     */
    public function add($sort, $order = null)
    {
        $order = ! $order ? 'ASC' : $order;
        $this->parts[] = $sort . ' '. $order;
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->parts);
    }

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->preSeparator . implode($this->separator, $this->parts) . $this->postSeparator;
    }
}
