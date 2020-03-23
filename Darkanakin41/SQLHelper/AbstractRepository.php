<?php


namespace Darkanakin41\SQLHelper;


use ADOConnection;
use Darkanakin41\SQLHelper\Query\AbstractCriteria;
use Darkanakin41\SQLHelper\Query\QueryBuilder;
use Exception;

abstract class AbstractRepository
{
    /**
     * @var ADOConnection the connection to the database
     */
    protected $db;

    public function __construct(ADOConnection $ADOConnection)
    {
        $this->db = $ADOConnection;
    }

    /**
     * Retrieve the default query builder for the current repository
     * @return QueryBuilder
     */
    abstract public function getQueryBuilder();

    /**
     * Count entries in the database based on given criterias
     *
     * @param array $criteria
     *
     * @return int
     * @throws Exception
     */
    public function count(array $criteria)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('COUNT(*)', 'count');

        $this->addCriterias($qb, $criteria);

        $result = $this->getOneOrNullResult($qb);
        if ($result === null) {
            return 0;
        }
        return $result['count'];
    }

    /**
     * Find entries in the database based on given criterias
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int        $limit
     * @param int        $offset
     *
     * @return array
     * @throws Exception
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->getQueryBuilder();

        $this->addCriterias($qb, $criteria);

        if ($limit !== null) {
            $qb->maxResults($limit);
        }

        if ($offset !== null) {
            $qb->offset($offset);
        }


        foreach ($orderBy as $field => $order) {
            $qb->addOrderBy($field, $order ? 'ASC' : 'DESC');
        }

        return $this->getResults($qb);
    }

    /**
     * Find entry in the database based on given criterias
     *
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return array | null
     * @throws Exception
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        $qb = $this->getQueryBuilder();

        $this->addCriterias($qb, $criteria);

        $qb->maxResults(1);
        $qb->offset(0);

        foreach ($orderBy as $field => $order) {
            $qb->addOrderBy($field, $order ? 'ASC' : 'DESC');
        }

        return $this->getOneOrNullResult($qb);
    }

    /**
     * Retrieve one or more results
     *
     * @param QueryBuilder $qb
     *
     * @return array|null
     * @throws Exception
     */
    protected function getOneOrNullResult($qb)
    {

        $data = $this->getResults($qb);

        if (count($data) === 0) {
            return null;
        }

        if (count($data) > 1) {
            throw new Exception('Expect to have zero or one result but got multiple');
        }

        return $data[0];
    }

    /**
     * Retrieve Results
     *
     * @param QueryBuilder $qb
     *
     * @return array
     * @throws Exception
     */
    protected function getResults($qb)
    {
        $recordSet = $this->db->Execute($qb->getSQL());

        $data = array();

        if ($recordSet->RowCount() === 0) {
            return $data;
        }

        $iterator = $recordSet->getIterator();
        while ($iterator->valid()) {
            array_push($data, $iterator->current());
            $iterator->next();
        }

        return $data;
    }

    protected function addCriterias(QueryBuilder $qb, $criteria)
    {
        foreach ($criteria as $field => $value) {
            if (method_exists($this, $field)) {
                call_user_func(array($this, $field), $qb, $value);
                continue;
            }
            if($value instanceof AbstractCriteria){
                $value->updateQueryBuilder($qb);
                continue;
            }
            $sign = '=';
            if(stripos($value, '!') === 0){
                $sign = '<>';
                $value = substr($value, 1);
            }
            if (stripos($field, '.')) {
                $qb->addWhere(sprintf('%s %s :%s', $field, $sign, $field));
            } else {
                $qb->addWhere(sprintf('%s.%s %s :%s', $qb->getAlias(), $field, $sign, $field));
            }
            $qb->setParameter($field, $value);
        }
    }
}
