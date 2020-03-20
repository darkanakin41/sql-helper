<?php


namespace Darkanakin41\SQLHelper;


use ADOConnection;
use Darkanakin41\SQLHelper\Query\QueryBuilder;
use Exception;

abstract class AbstractRepository
{
    protected static $baseAlias = 'ic';

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

        foreach ($criteria as $field => $value) {
            $qb->addWhere(sprintf('%s.%s = :%s', self::$baseAlias, $field, $field));
            $qb->setParameter($field, $value);
        }

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

        foreach ($criteria as $field => $value) {
            $qb->addWhere(sprintf('%s.%s = :%s', self::$baseAlias, $field, $field));
            $qb->setParameter($field, $value);
        }

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
        do {
            array_push($data, $iterator->current());
        } while ($iterator->next() !== null);

        return $data;
    }
}
