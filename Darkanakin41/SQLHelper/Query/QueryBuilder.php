<?php

namespace Darkanakin41\SQLHelper\Query;

use Darkanakin41\SQLHelper\Query\Expression\OrderBy;

class QueryBuilder
{
    /**
     * @var Expression\Select Liste des items à mettre dans la close select
     */
    private $select;
    /**
     * @var Expression\From[] Liste des items à mettre dans la close from
     */
    private $froms = array();
    /**
     * @var Expression\Andx Liste des items à mettre dans la close where
     */
    private $where;
    /**
     * @var OrderBy
     */
    private $orderBy;
    /**
     * @var array Liste des paramètres à remplacer dans la requête
     */
    private $parameters = array();
    /**
     * @var int le nombre de résultats maximums
     */
    private $maxResults = -1;
    /**
     * @var int le numéro de la ligne de démarrage
     */
    private $offset = -1;

    /**
     * @var string|null l'alias de la requete
     */
    private $alias;

    public function __construct($alias = null)
    {
        $this->alias = $alias;
    }

    /**
     * @return string|null
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Défini le SELECT
     *
     * @param string $selectItem
     * @param string $alias
     */
    public function select($selectItem, $alias = '')
    {
        $this->select = new Expression\Select();
        $this->addSelect($selectItem, $alias);
    }

    /**
     * Ajoute un élément dans le SELECT
     *
     * @param string $selectItem
     * @param string $alias
     */
    public function addSelect($selectItem, $alias = '')
    {
        if ($this->select === null) {
            $this->select = new Expression\Select();
        }
        $clause = $selectItem;
        if (!empty($alias)) {
            $clause .= ' AS '.$alias;
        }
        $this->select->add($clause);
    }

    /**
     * Défini le FROM
     *
     * @param string $table
     * @param string $alias
     */
    public function from($table, $alias = '')
    {
        $this->froms = array();
        if (empty($alias) && !empty($this->alias)) {
            $alias = $this->alias;
        }
        $this->addFrom($table, $alias);
    }

    /**
     * Ajoute un élément dans le FROM
     *
     * @param string $table
     * @param string $alias
     */
    public function addFrom($table, $alias = '')
    {
        array_push($this->froms, new Expression\From($table, $alias));
    }

    /**
     * Défini le WHERE
     *
     * @param string $condition
     */
    public function where($condition)
    {
        $this->where = new Expression\Andx();
        $this->addWhere($condition);
    }

    /**
     * Ajoute un élément dans le WHERE
     *
     * @param string $condition
     */
    public function addWhere($condition)
    {
        if ($this->where === null) {
            $this->where = new Expression\Andx();
        }
        $this->where->add($condition);
    }

    /**
     * Ajoute un paramètre à remplacer dans la requête
     *
     * @param string $parameter
     * @param mixed  $value
     */
    public function setParameter($parameter, $value)
    {
        $this->parameters[$parameter] = $value;
    }


    /**
     * Défini le ORDER BY
     *
     * @param string $field
     * @param string $order
     */
    public function orderBy($field, $order = 'ASC')
    {
        if ($this->orderBy === null) {
            $this->orderBy = new Expression\OrderBy();
        }
        $this->addOrderBy($field, $order);
    }

    /**
     * Ajoute un élément dans le ORDER BY
     *
     * @param string $field
     * @param string $order
     */
    public function addOrderBy($field, $order = 'ASC')
    {
        if ($this->orderBy === null) {
            $this->orderBy = new Expression\OrderBy();
        }
        $this->orderBy->add($field, $order);
    }

    /**
     * Ajoute une jointure LEFT JOIN
     *
     * @param $baseAlias
     * @param $table
     * @param $alias
     * @param $condition
     */
    public function leftJoin($baseAlias, $table, $alias, $condition)
    {
        foreach ($this->froms as $from) {
            if ($from->getAlias() === $baseAlias) {
                $from->addJoin(new Expression\Join(Expression\Join::LEFT_JOIN, $table, $alias, Expression\Join::ON, $condition));
            }
        }
    }

    /**
     * Défini le nombre de résultat maximum
     *
     * @param $maxResults
     */
    public function maxResults($maxResults)
    {
        $this->maxResults = $maxResults;
    }

    /**
     * Défini le numéro de la première ligne à récupérer
     *
     * @param $offset
     */
    public function offset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Récupère le SQL final
     *
     * @return string
     */
    public function getSQL()
    {
        $sqlParts = array();

        array_push($sqlParts, "SELECT", (string)$this->select);
        array_push($sqlParts, "FROM", implode(', ', $this->froms));

        if ($this->where !== null && count($this->where->getParts()) > 0) {
            array_push($sqlParts, 'WHERE', (string)$this->where);
        }

        if ($this->orderBy !== null && $this->orderBy->getParts() > 0) {
            array_push($sqlParts, 'ORDER BY', (string)$this->orderBy);
        }

        if ($this->maxResults > -1) {
            array_push($sqlParts, 'LIMIT', $this->maxResults);
        }

        if ($this->offset > -1) {
            array_push($sqlParts, 'OFFSET', $this->offset);
        }

        $sql = implode(' ', $sqlParts);

        return $this->applyParameters($sql);
    }

    public function __toString()
    {
        return $this->getSQL();
    }

    private function applyParameters($sql)
    {
        foreach ($this->parameters as $key => $value) {
            if (is_string($value)) {
                $sql = str_ireplace(":$key", "'$value'", $sql);
            } elseif (is_numeric($value)) {
                $sql = str_ireplace(":$key", $value, $sql);
            }
        }
        return $sql;
    }
}
