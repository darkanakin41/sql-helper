<?php


namespace Darkanakin41\SQLHelper\Query\Expression;


class Composite extends Base
{
    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->count() === 1) {
            return (string) $this->parts[0];
        }

        $components = array();

        foreach ($this->parts as $part) {
            $components[] = $this->processQueryPart($part);
        }

        return implode($this->separator, $components);
    }

    /**
     * @param string $part
     *
     * @return string
     */
    private function processQueryPart($part)
    {
        $queryPart = (string) $part;

        if (is_object($part) && $part instanceof self && $part->count() > 1) {
            return $this->preSeparator . $queryPart . $this->postSeparator;
        }

        if (stripos($queryPart, ' OR ') !== false || stripos($queryPart, ' AND ') !== false) {
            return $this->preSeparator . $queryPart . $this->postSeparator;
        }

        return $queryPart;
    }
}
