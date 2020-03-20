<?php


namespace Darkanakin41\SQLHelper\Query\Expression;


use InvalidArgumentException;

abstract class Base
{
    /**
     * @var string
     */
    protected $preSeparator = '(';

    /**
     * @var string
     */
    protected $separator = ', ';

    /**
     * @var string
     */
    protected $postSeparator = ')';

    /**
     * @var array
     */
    protected $allowedClasses = array();

    /**
     * @var array
     */
    protected $parts = array();

    /**
     * @param array $args
     */
    public function __construct($args = array())
    {
        $this->addMultiple($args);
    }

    /**
     * @param array $args
     *
     * @return Base
     */
    public function addMultiple($args = array())
    {
        foreach ((array)$args as $arg) {
            $this->add($arg);
        }

        return $this;
    }

    /**
     * @param mixed $arg
     *
     * @return Base
     *
     * @throws InvalidArgumentException
     */
    public function add($arg)
    {
        if ($arg !== null && (!$arg instanceof self || $arg->count() > 0)) {
            if (!is_string($arg)) {
                $class = get_class($arg);

                if (!in_array($class, $this->allowedClasses)) {
                    throw new InvalidArgumentException("Expression of type '$class' not allowed in this context.");
                }
            }

            $this->parts[] = $arg;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->parts);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->count() == 1) {
            return (string)$this->parts[0];
        }

        return $this->preSeparator.implode($this->separator, $this->parts).$this->postSeparator;
    }
}
