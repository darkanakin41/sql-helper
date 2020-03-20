<?php


namespace Darkanakin41\SQLHelper\Query\Expression;


class Literal extends Base
{

    public static function _quote($literal)
    {
        if (is_numeric($literal) && !is_string($literal)) {
            return (string) $literal;
        } else if (is_bool($literal)) {
            return $literal ? "true" : "false";
        } else {
            return "'" . str_replace("'", "''", $literal) . "'";
        }
    }
    /**
     * @var string
     */
    protected $preSeparator  = '';

    /**
     * @var string
     */
    protected $postSeparator = '';

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }
}
