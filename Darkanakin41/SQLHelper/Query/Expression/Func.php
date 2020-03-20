<?php


namespace Darkanakin41\SQLHelper\Query\Expression;


class Func
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * Creates a function, with the given argument.
     *
     * @param string $name
     * @param array  $arguments
     */
    public function __construct($name, $arguments)
    {
        $this->name = $name;
        $this->arguments = (array)$arguments;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name.'('.implode(', ', $this->arguments).')';
    }
}
