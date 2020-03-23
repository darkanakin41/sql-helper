<?php


namespace Darkanakin41\SQLHelper\Query;


abstract class AbstractCriteria
{
    public abstract function updateQueryBuilder(QueryBuilder $qb);
}
