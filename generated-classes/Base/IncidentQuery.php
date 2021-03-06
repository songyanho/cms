<?php

namespace Base;

use \Incident as ChildIncident;
use \IncidentQuery as ChildIncidentQuery;
use \Exception;
use \PDO;
use Map\IncidentTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'incident' table.
 *
 *
 *
 * @method     ChildIncidentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildIncidentQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     ChildIncidentQuery orderByLocation($order = Criteria::ASC) Order by the location column
 * @method     ChildIncidentQuery orderByLatitude($order = Criteria::ASC) Order by the latitude column
 * @method     ChildIncidentQuery orderByLongitude($order = Criteria::ASC) Order by the longitude column
 * @method     ChildIncidentQuery orderByActive($order = Criteria::ASC) Order by the active column
 * @method     ChildIncidentQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildIncidentQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildIncidentQuery groupById() Group by the id column
 * @method     ChildIncidentQuery groupByTitle() Group by the title column
 * @method     ChildIncidentQuery groupByLocation() Group by the location column
 * @method     ChildIncidentQuery groupByLatitude() Group by the latitude column
 * @method     ChildIncidentQuery groupByLongitude() Group by the longitude column
 * @method     ChildIncidentQuery groupByActive() Group by the active column
 * @method     ChildIncidentQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildIncidentQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildIncidentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildIncidentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildIncidentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildIncidentQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildIncidentQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildIncidentQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildIncidentQuery leftJoinIncidentCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the IncidentCategory relation
 * @method     ChildIncidentQuery rightJoinIncidentCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the IncidentCategory relation
 * @method     ChildIncidentQuery innerJoinIncidentCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the IncidentCategory relation
 *
 * @method     ChildIncidentQuery joinWithIncidentCategory($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the IncidentCategory relation
 *
 * @method     ChildIncidentQuery leftJoinWithIncidentCategory() Adds a LEFT JOIN clause and with to the query using the IncidentCategory relation
 * @method     ChildIncidentQuery rightJoinWithIncidentCategory() Adds a RIGHT JOIN clause and with to the query using the IncidentCategory relation
 * @method     ChildIncidentQuery innerJoinWithIncidentCategory() Adds a INNER JOIN clause and with to the query using the IncidentCategory relation
 *
 * @method     ChildIncidentQuery leftJoinIncidentReporter($relationAlias = null) Adds a LEFT JOIN clause to the query using the IncidentReporter relation
 * @method     ChildIncidentQuery rightJoinIncidentReporter($relationAlias = null) Adds a RIGHT JOIN clause to the query using the IncidentReporter relation
 * @method     ChildIncidentQuery innerJoinIncidentReporter($relationAlias = null) Adds a INNER JOIN clause to the query using the IncidentReporter relation
 *
 * @method     ChildIncidentQuery joinWithIncidentReporter($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the IncidentReporter relation
 *
 * @method     ChildIncidentQuery leftJoinWithIncidentReporter() Adds a LEFT JOIN clause and with to the query using the IncidentReporter relation
 * @method     ChildIncidentQuery rightJoinWithIncidentReporter() Adds a RIGHT JOIN clause and with to the query using the IncidentReporter relation
 * @method     ChildIncidentQuery innerJoinWithIncidentReporter() Adds a INNER JOIN clause and with to the query using the IncidentReporter relation
 *
 * @method     ChildIncidentQuery leftJoinIncidentResource($relationAlias = null) Adds a LEFT JOIN clause to the query using the IncidentResource relation
 * @method     ChildIncidentQuery rightJoinIncidentResource($relationAlias = null) Adds a RIGHT JOIN clause to the query using the IncidentResource relation
 * @method     ChildIncidentQuery innerJoinIncidentResource($relationAlias = null) Adds a INNER JOIN clause to the query using the IncidentResource relation
 *
 * @method     ChildIncidentQuery joinWithIncidentResource($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the IncidentResource relation
 *
 * @method     ChildIncidentQuery leftJoinWithIncidentResource() Adds a LEFT JOIN clause and with to the query using the IncidentResource relation
 * @method     ChildIncidentQuery rightJoinWithIncidentResource() Adds a RIGHT JOIN clause and with to the query using the IncidentResource relation
 * @method     ChildIncidentQuery innerJoinWithIncidentResource() Adds a INNER JOIN clause and with to the query using the IncidentResource relation
 *
 * @method     ChildIncidentQuery leftJoinIncidentResourceRecord($relationAlias = null) Adds a LEFT JOIN clause to the query using the IncidentResourceRecord relation
 * @method     ChildIncidentQuery rightJoinIncidentResourceRecord($relationAlias = null) Adds a RIGHT JOIN clause to the query using the IncidentResourceRecord relation
 * @method     ChildIncidentQuery innerJoinIncidentResourceRecord($relationAlias = null) Adds a INNER JOIN clause to the query using the IncidentResourceRecord relation
 *
 * @method     ChildIncidentQuery joinWithIncidentResourceRecord($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the IncidentResourceRecord relation
 *
 * @method     ChildIncidentQuery leftJoinWithIncidentResourceRecord() Adds a LEFT JOIN clause and with to the query using the IncidentResourceRecord relation
 * @method     ChildIncidentQuery rightJoinWithIncidentResourceRecord() Adds a RIGHT JOIN clause and with to the query using the IncidentResourceRecord relation
 * @method     ChildIncidentQuery innerJoinWithIncidentResourceRecord() Adds a INNER JOIN clause and with to the query using the IncidentResourceRecord relation
 *
 * @method     \IncidentCategoryQuery|\IncidentReporterQuery|\IncidentResourceQuery|\IncidentResourceRecordQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildIncident findOne(ConnectionInterface $con = null) Return the first ChildIncident matching the query
 * @method     ChildIncident findOneOrCreate(ConnectionInterface $con = null) Return the first ChildIncident matching the query, or a new ChildIncident object populated from the query conditions when no match is found
 *
 * @method     ChildIncident findOneById(int $id) Return the first ChildIncident filtered by the id column
 * @method     ChildIncident findOneByTitle(string $title) Return the first ChildIncident filtered by the title column
 * @method     ChildIncident findOneByLocation(string $location) Return the first ChildIncident filtered by the location column
 * @method     ChildIncident findOneByLatitude(double $latitude) Return the first ChildIncident filtered by the latitude column
 * @method     ChildIncident findOneByLongitude(double $longitude) Return the first ChildIncident filtered by the longitude column
 * @method     ChildIncident findOneByActive(boolean $active) Return the first ChildIncident filtered by the active column
 * @method     ChildIncident findOneByCreatedAt(string $created_at) Return the first ChildIncident filtered by the created_at column
 * @method     ChildIncident findOneByUpdatedAt(string $updated_at) Return the first ChildIncident filtered by the updated_at column *

 * @method     ChildIncident requirePk($key, ConnectionInterface $con = null) Return the ChildIncident by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIncident requireOne(ConnectionInterface $con = null) Return the first ChildIncident matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildIncident requireOneById(int $id) Return the first ChildIncident filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIncident requireOneByTitle(string $title) Return the first ChildIncident filtered by the title column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIncident requireOneByLocation(string $location) Return the first ChildIncident filtered by the location column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIncident requireOneByLatitude(double $latitude) Return the first ChildIncident filtered by the latitude column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIncident requireOneByLongitude(double $longitude) Return the first ChildIncident filtered by the longitude column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIncident requireOneByActive(boolean $active) Return the first ChildIncident filtered by the active column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIncident requireOneByCreatedAt(string $created_at) Return the first ChildIncident filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIncident requireOneByUpdatedAt(string $updated_at) Return the first ChildIncident filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildIncident[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildIncident objects based on current ModelCriteria
 * @method     ChildIncident[]|ObjectCollection findById(int $id) Return ChildIncident objects filtered by the id column
 * @method     ChildIncident[]|ObjectCollection findByTitle(string $title) Return ChildIncident objects filtered by the title column
 * @method     ChildIncident[]|ObjectCollection findByLocation(string $location) Return ChildIncident objects filtered by the location column
 * @method     ChildIncident[]|ObjectCollection findByLatitude(double $latitude) Return ChildIncident objects filtered by the latitude column
 * @method     ChildIncident[]|ObjectCollection findByLongitude(double $longitude) Return ChildIncident objects filtered by the longitude column
 * @method     ChildIncident[]|ObjectCollection findByActive(boolean $active) Return ChildIncident objects filtered by the active column
 * @method     ChildIncident[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildIncident objects filtered by the created_at column
 * @method     ChildIncident[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildIncident objects filtered by the updated_at column
 * @method     ChildIncident[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class IncidentQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\IncidentQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'cms', $modelName = '\\Incident', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildIncidentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildIncidentQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildIncidentQuery) {
            return $criteria;
        }
        $query = new ChildIncidentQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildIncident|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = IncidentTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(IncidentTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildIncident A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, title, location, latitude, longitude, active, created_at, updated_at FROM incident WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildIncident $obj */
            $obj = new ChildIncident();
            $obj->hydrate($row);
            IncidentTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildIncident|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(IncidentTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(IncidentTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(IncidentTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(IncidentTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(IncidentTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(IncidentTableMap::COL_TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the location column
     *
     * Example usage:
     * <code>
     * $query->filterByLocation('fooValue');   // WHERE location = 'fooValue'
     * $query->filterByLocation('%fooValue%'); // WHERE location LIKE '%fooValue%'
     * </code>
     *
     * @param     string $location The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByLocation($location = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($location)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $location)) {
                $location = str_replace('*', '%', $location);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(IncidentTableMap::COL_LOCATION, $location, $comparison);
    }

    /**
     * Filter the query on the latitude column
     *
     * Example usage:
     * <code>
     * $query->filterByLatitude(1234); // WHERE latitude = 1234
     * $query->filterByLatitude(array(12, 34)); // WHERE latitude IN (12, 34)
     * $query->filterByLatitude(array('min' => 12)); // WHERE latitude > 12
     * </code>
     *
     * @param     mixed $latitude The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByLatitude($latitude = null, $comparison = null)
    {
        if (is_array($latitude)) {
            $useMinMax = false;
            if (isset($latitude['min'])) {
                $this->addUsingAlias(IncidentTableMap::COL_LATITUDE, $latitude['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($latitude['max'])) {
                $this->addUsingAlias(IncidentTableMap::COL_LATITUDE, $latitude['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(IncidentTableMap::COL_LATITUDE, $latitude, $comparison);
    }

    /**
     * Filter the query on the longitude column
     *
     * Example usage:
     * <code>
     * $query->filterByLongitude(1234); // WHERE longitude = 1234
     * $query->filterByLongitude(array(12, 34)); // WHERE longitude IN (12, 34)
     * $query->filterByLongitude(array('min' => 12)); // WHERE longitude > 12
     * </code>
     *
     * @param     mixed $longitude The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByLongitude($longitude = null, $comparison = null)
    {
        if (is_array($longitude)) {
            $useMinMax = false;
            if (isset($longitude['min'])) {
                $this->addUsingAlias(IncidentTableMap::COL_LONGITUDE, $longitude['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($longitude['max'])) {
                $this->addUsingAlias(IncidentTableMap::COL_LONGITUDE, $longitude['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(IncidentTableMap::COL_LONGITUDE, $longitude, $comparison);
    }

    /**
     * Filter the query on the active column
     *
     * Example usage:
     * <code>
     * $query->filterByActive(true); // WHERE active = true
     * $query->filterByActive('yes'); // WHERE active = true
     * </code>
     *
     * @param     boolean|string $active The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByActive($active = null, $comparison = null)
    {
        if (is_string($active)) {
            $active = in_array(strtolower($active), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(IncidentTableMap::COL_ACTIVE, $active, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(IncidentTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(IncidentTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(IncidentTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(IncidentTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(IncidentTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(IncidentTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \IncidentCategory object
     *
     * @param \IncidentCategory|ObjectCollection $incidentCategory the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByIncidentCategory($incidentCategory, $comparison = null)
    {
        if ($incidentCategory instanceof \IncidentCategory) {
            return $this
                ->addUsingAlias(IncidentTableMap::COL_ID, $incidentCategory->getIncidentId(), $comparison);
        } elseif ($incidentCategory instanceof ObjectCollection) {
            return $this
                ->useIncidentCategoryQuery()
                ->filterByPrimaryKeys($incidentCategory->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByIncidentCategory() only accepts arguments of type \IncidentCategory or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the IncidentCategory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function joinIncidentCategory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('IncidentCategory');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'IncidentCategory');
        }

        return $this;
    }

    /**
     * Use the IncidentCategory relation IncidentCategory object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \IncidentCategoryQuery A secondary query class using the current class as primary query
     */
    public function useIncidentCategoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinIncidentCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'IncidentCategory', '\IncidentCategoryQuery');
    }

    /**
     * Filter the query by a related \IncidentReporter object
     *
     * @param \IncidentReporter|ObjectCollection $incidentReporter the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByIncidentReporter($incidentReporter, $comparison = null)
    {
        if ($incidentReporter instanceof \IncidentReporter) {
            return $this
                ->addUsingAlias(IncidentTableMap::COL_ID, $incidentReporter->getIncidentId(), $comparison);
        } elseif ($incidentReporter instanceof ObjectCollection) {
            return $this
                ->useIncidentReporterQuery()
                ->filterByPrimaryKeys($incidentReporter->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByIncidentReporter() only accepts arguments of type \IncidentReporter or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the IncidentReporter relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function joinIncidentReporter($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('IncidentReporter');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'IncidentReporter');
        }

        return $this;
    }

    /**
     * Use the IncidentReporter relation IncidentReporter object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \IncidentReporterQuery A secondary query class using the current class as primary query
     */
    public function useIncidentReporterQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinIncidentReporter($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'IncidentReporter', '\IncidentReporterQuery');
    }

    /**
     * Filter the query by a related \IncidentResource object
     *
     * @param \IncidentResource|ObjectCollection $incidentResource the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByIncidentResource($incidentResource, $comparison = null)
    {
        if ($incidentResource instanceof \IncidentResource) {
            return $this
                ->addUsingAlias(IncidentTableMap::COL_ID, $incidentResource->getIncidentId(), $comparison);
        } elseif ($incidentResource instanceof ObjectCollection) {
            return $this
                ->useIncidentResourceQuery()
                ->filterByPrimaryKeys($incidentResource->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByIncidentResource() only accepts arguments of type \IncidentResource or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the IncidentResource relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function joinIncidentResource($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('IncidentResource');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'IncidentResource');
        }

        return $this;
    }

    /**
     * Use the IncidentResource relation IncidentResource object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \IncidentResourceQuery A secondary query class using the current class as primary query
     */
    public function useIncidentResourceQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinIncidentResource($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'IncidentResource', '\IncidentResourceQuery');
    }

    /**
     * Filter the query by a related \IncidentResourceRecord object
     *
     * @param \IncidentResourceRecord|ObjectCollection $incidentResourceRecord the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByIncidentResourceRecord($incidentResourceRecord, $comparison = null)
    {
        if ($incidentResourceRecord instanceof \IncidentResourceRecord) {
            return $this
                ->addUsingAlias(IncidentTableMap::COL_ID, $incidentResourceRecord->getIncidentId(), $comparison);
        } elseif ($incidentResourceRecord instanceof ObjectCollection) {
            return $this
                ->useIncidentResourceRecordQuery()
                ->filterByPrimaryKeys($incidentResourceRecord->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByIncidentResourceRecord() only accepts arguments of type \IncidentResourceRecord or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the IncidentResourceRecord relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function joinIncidentResourceRecord($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('IncidentResourceRecord');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'IncidentResourceRecord');
        }

        return $this;
    }

    /**
     * Use the IncidentResourceRecord relation IncidentResourceRecord object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \IncidentResourceRecordQuery A secondary query class using the current class as primary query
     */
    public function useIncidentResourceRecordQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinIncidentResourceRecord($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'IncidentResourceRecord', '\IncidentResourceRecordQuery');
    }

    /**
     * Filter the query by a related Category object
     * using the incident_category table as cross reference
     *
     * @param Category $category the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByCategory($category, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useIncidentCategoryQuery()
            ->filterByCategory($category, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related Reporter object
     * using the incident_reporter table as cross reference
     *
     * @param Reporter $reporter the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByReporter($reporter, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useIncidentReporterQuery()
            ->filterByReporter($reporter, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related Resource object
     * using the incident_resource table as cross reference
     *
     * @param Resource $resource the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildIncidentQuery The current query, for fluid interface
     */
    public function filterByResource($resource, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useIncidentResourceQuery()
            ->filterByResource($resource, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildIncident $incident Object to remove from the list of results
     *
     * @return $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function prune($incident = null)
    {
        if ($incident) {
            $this->addUsingAlias(IncidentTableMap::COL_ID, $incident->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the incident table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(IncidentTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            IncidentTableMap::clearInstancePool();
            IncidentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(IncidentTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(IncidentTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            IncidentTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            IncidentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(IncidentTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(IncidentTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(IncidentTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(IncidentTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(IncidentTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildIncidentQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(IncidentTableMap::COL_CREATED_AT);
    }

} // IncidentQuery
