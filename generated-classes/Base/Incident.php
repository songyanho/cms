<?php

namespace Base;

use \Category as ChildCategory;
use \CategoryQuery as ChildCategoryQuery;
use \Incident as ChildIncident;
use \IncidentCategory as ChildIncidentCategory;
use \IncidentCategoryQuery as ChildIncidentCategoryQuery;
use \IncidentQuery as ChildIncidentQuery;
use \IncidentReporter as ChildIncidentReporter;
use \IncidentReporterQuery as ChildIncidentReporterQuery;
use \IncidentResource as ChildIncidentResource;
use \IncidentResourceQuery as ChildIncidentResourceQuery;
use \IncidentResourceRecord as ChildIncidentResourceRecord;
use \IncidentResourceRecordQuery as ChildIncidentResourceRecordQuery;
use \Reporter as ChildReporter;
use \ReporterQuery as ChildReporterQuery;
use \Resource as ChildResource;
use \ResourceQuery as ChildResourceQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\IncidentCategoryTableMap;
use Map\IncidentReporterTableMap;
use Map\IncidentResourceRecordTableMap;
use Map\IncidentResourceTableMap;
use Map\IncidentTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'incident' table.
 *
 *
 *
* @package    propel.generator..Base
*/
abstract class Incident implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\IncidentTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the title field.
     *
     * @var        string
     */
    protected $title;

    /**
     * The value for the location field.
     *
     * @var        string
     */
    protected $location;

    /**
     * The value for the latitude field.
     *
     * @var        double
     */
    protected $latitude;

    /**
     * The value for the longitude field.
     *
     * @var        double
     */
    protected $longitude;

    /**
     * The value for the active field.
     *
     * @var        boolean
     */
    protected $active;

    /**
     * The value for the created_at field.
     *
     * @var        \DateTime
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     *
     * @var        \DateTime
     */
    protected $updated_at;

    /**
     * @var        ObjectCollection|ChildIncidentCategory[] Collection to store aggregation of ChildIncidentCategory objects.
     */
    protected $collIncidentCategories;
    protected $collIncidentCategoriesPartial;

    /**
     * @var        ObjectCollection|ChildIncidentReporter[] Collection to store aggregation of ChildIncidentReporter objects.
     */
    protected $collIncidentReporters;
    protected $collIncidentReportersPartial;

    /**
     * @var        ObjectCollection|ChildIncidentResource[] Collection to store aggregation of ChildIncidentResource objects.
     */
    protected $collIncidentResources;
    protected $collIncidentResourcesPartial;

    /**
     * @var        ObjectCollection|ChildIncidentResourceRecord[] Collection to store aggregation of ChildIncidentResourceRecord objects.
     */
    protected $collIncidentResourceRecords;
    protected $collIncidentResourceRecordsPartial;

    /**
     * @var        ObjectCollection|ChildCategory[] Cross Collection to store aggregation of ChildCategory objects.
     */
    protected $collCategories;

    /**
     * @var bool
     */
    protected $collCategoriesPartial;

    /**
     * @var        ObjectCollection|ChildReporter[] Cross Collection to store aggregation of ChildReporter objects.
     */
    protected $collReporters;

    /**
     * @var bool
     */
    protected $collReportersPartial;

    /**
     * @var        ObjectCollection|ChildResource[] Cross Collection to store aggregation of ChildResource objects.
     */
    protected $collResources;

    /**
     * @var bool
     */
    protected $collResourcesPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildCategory[]
     */
    protected $categoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildReporter[]
     */
    protected $reportersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildResource[]
     */
    protected $resourcesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildIncidentCategory[]
     */
    protected $incidentCategoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildIncidentReporter[]
     */
    protected $incidentReportersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildIncidentResource[]
     */
    protected $incidentResourcesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildIncidentResourceRecord[]
     */
    protected $incidentResourceRecordsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Incident object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Incident</code> instance.  If
     * <code>obj</code> is an instance of <code>Incident</code>, delegates to
     * <code>equals(Incident)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Incident The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the [location] column value.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get the [latitude] column value.
     *
     * @return double
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Get the [longitude] column value.
     *
     * @return double
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Get the [active] column value.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Get the [active] column value.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getActive();
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[IncidentTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param string $v new value
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[IncidentTableMap::COL_TITLE] = true;
        }

        return $this;
    } // setTitle()

    /**
     * Set the value of [location] column.
     *
     * @param string $v new value
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function setLocation($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->location !== $v) {
            $this->location = $v;
            $this->modifiedColumns[IncidentTableMap::COL_LOCATION] = true;
        }

        return $this;
    } // setLocation()

    /**
     * Set the value of [latitude] column.
     *
     * @param double $v new value
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function setLatitude($v)
    {
        if ($v !== null) {
            $v = (double) $v;
        }

        if ($this->latitude !== $v) {
            $this->latitude = $v;
            $this->modifiedColumns[IncidentTableMap::COL_LATITUDE] = true;
        }

        return $this;
    } // setLatitude()

    /**
     * Set the value of [longitude] column.
     *
     * @param double $v new value
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function setLongitude($v)
    {
        if ($v !== null) {
            $v = (double) $v;
        }

        if ($this->longitude !== $v) {
            $this->longitude = $v;
            $this->modifiedColumns[IncidentTableMap::COL_LONGITUDE] = true;
        }

        return $this;
    } // setLongitude()

    /**
     * Sets the value of the [active] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param  boolean|integer|string $v The new value
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function setActive($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->active !== $v) {
            $this->active = $v;
            $this->modifiedColumns[IncidentTableMap::COL_ACTIVE] = true;
        }

        return $this;
    } // setActive()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($this->created_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->created_at->format("Y-m-d H:i:s")) {
                $this->created_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[IncidentTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($this->updated_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->updated_at->format("Y-m-d H:i:s")) {
                $this->updated_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[IncidentTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : IncidentTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : IncidentTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : IncidentTableMap::translateFieldName('Location', TableMap::TYPE_PHPNAME, $indexType)];
            $this->location = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : IncidentTableMap::translateFieldName('Latitude', TableMap::TYPE_PHPNAME, $indexType)];
            $this->latitude = (null !== $col) ? (double) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : IncidentTableMap::translateFieldName('Longitude', TableMap::TYPE_PHPNAME, $indexType)];
            $this->longitude = (null !== $col) ? (double) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : IncidentTableMap::translateFieldName('Active', TableMap::TYPE_PHPNAME, $indexType)];
            $this->active = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : IncidentTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : IncidentTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 8; // 8 = IncidentTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Incident'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(IncidentTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildIncidentQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collIncidentCategories = null;

            $this->collIncidentReporters = null;

            $this->collIncidentResources = null;

            $this->collIncidentResourceRecords = null;

            $this->collCategories = null;
            $this->collReporters = null;
            $this->collResources = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Incident::setDeleted()
     * @see Incident::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(IncidentTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildIncidentQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(IncidentTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior

                if (!$this->isColumnModified(IncidentTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(IncidentTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(IncidentTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                IncidentTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->categoriesScheduledForDeletion !== null) {
                if (!$this->categoriesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->categoriesScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[0] = $this->getId();
                        $entryPk[1] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \IncidentCategoryQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->categoriesScheduledForDeletion = null;
                }

            }

            if ($this->collCategories) {
                foreach ($this->collCategories as $category) {
                    if (!$category->isDeleted() && ($category->isNew() || $category->isModified())) {
                        $category->save($con);
                    }
                }
            }


            if ($this->reportersScheduledForDeletion !== null) {
                if (!$this->reportersScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->reportersScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[0] = $this->getId();
                        $entryPk[1] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \IncidentReporterQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->reportersScheduledForDeletion = null;
                }

            }

            if ($this->collReporters) {
                foreach ($this->collReporters as $reporter) {
                    if (!$reporter->isDeleted() && ($reporter->isNew() || $reporter->isModified())) {
                        $reporter->save($con);
                    }
                }
            }


            if ($this->resourcesScheduledForDeletion !== null) {
                if (!$this->resourcesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->resourcesScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[0] = $this->getId();
                        $entryPk[1] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \IncidentResourceQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->resourcesScheduledForDeletion = null;
                }

            }

            if ($this->collResources) {
                foreach ($this->collResources as $resource) {
                    if (!$resource->isDeleted() && ($resource->isNew() || $resource->isModified())) {
                        $resource->save($con);
                    }
                }
            }


            if ($this->incidentCategoriesScheduledForDeletion !== null) {
                if (!$this->incidentCategoriesScheduledForDeletion->isEmpty()) {
                    \IncidentCategoryQuery::create()
                        ->filterByPrimaryKeys($this->incidentCategoriesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->incidentCategoriesScheduledForDeletion = null;
                }
            }

            if ($this->collIncidentCategories !== null) {
                foreach ($this->collIncidentCategories as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->incidentReportersScheduledForDeletion !== null) {
                if (!$this->incidentReportersScheduledForDeletion->isEmpty()) {
                    \IncidentReporterQuery::create()
                        ->filterByPrimaryKeys($this->incidentReportersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->incidentReportersScheduledForDeletion = null;
                }
            }

            if ($this->collIncidentReporters !== null) {
                foreach ($this->collIncidentReporters as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->incidentResourcesScheduledForDeletion !== null) {
                if (!$this->incidentResourcesScheduledForDeletion->isEmpty()) {
                    \IncidentResourceQuery::create()
                        ->filterByPrimaryKeys($this->incidentResourcesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->incidentResourcesScheduledForDeletion = null;
                }
            }

            if ($this->collIncidentResources !== null) {
                foreach ($this->collIncidentResources as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->incidentResourceRecordsScheduledForDeletion !== null) {
                if (!$this->incidentResourceRecordsScheduledForDeletion->isEmpty()) {
                    \IncidentResourceRecordQuery::create()
                        ->filterByPrimaryKeys($this->incidentResourceRecordsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->incidentResourceRecordsScheduledForDeletion = null;
                }
            }

            if ($this->collIncidentResourceRecords !== null) {
                foreach ($this->collIncidentResourceRecords as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[IncidentTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . IncidentTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(IncidentTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(IncidentTableMap::COL_TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'title';
        }
        if ($this->isColumnModified(IncidentTableMap::COL_LOCATION)) {
            $modifiedColumns[':p' . $index++]  = 'location';
        }
        if ($this->isColumnModified(IncidentTableMap::COL_LATITUDE)) {
            $modifiedColumns[':p' . $index++]  = 'latitude';
        }
        if ($this->isColumnModified(IncidentTableMap::COL_LONGITUDE)) {
            $modifiedColumns[':p' . $index++]  = 'longitude';
        }
        if ($this->isColumnModified(IncidentTableMap::COL_ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = 'active';
        }
        if ($this->isColumnModified(IncidentTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'created_at';
        }
        if ($this->isColumnModified(IncidentTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'updated_at';
        }

        $sql = sprintf(
            'INSERT INTO incident (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'title':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'location':
                        $stmt->bindValue($identifier, $this->location, PDO::PARAM_STR);
                        break;
                    case 'latitude':
                        $stmt->bindValue($identifier, $this->latitude, PDO::PARAM_STR);
                        break;
                    case 'longitude':
                        $stmt->bindValue($identifier, $this->longitude, PDO::PARAM_STR);
                        break;
                    case 'active':
                        $stmt->bindValue($identifier, (int) $this->active, PDO::PARAM_INT);
                        break;
                    case 'created_at':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'updated_at':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = IncidentTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getTitle();
                break;
            case 2:
                return $this->getLocation();
                break;
            case 3:
                return $this->getLatitude();
                break;
            case 4:
                return $this->getLongitude();
                break;
            case 5:
                return $this->getActive();
                break;
            case 6:
                return $this->getCreatedAt();
                break;
            case 7:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['Incident'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Incident'][$this->hashCode()] = true;
        $keys = IncidentTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getLocation(),
            $keys[3] => $this->getLatitude(),
            $keys[4] => $this->getLongitude(),
            $keys[5] => $this->getActive(),
            $keys[6] => $this->getCreatedAt(),
            $keys[7] => $this->getUpdatedAt(),
        );
        if ($result[$keys[6]] instanceof \DateTime) {
            $result[$keys[6]] = $result[$keys[6]]->format('c');
        }

        if ($result[$keys[7]] instanceof \DateTime) {
            $result[$keys[7]] = $result[$keys[7]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collIncidentCategories) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'incidentCategories';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'incident_categories';
                        break;
                    default:
                        $key = 'IncidentCategories';
                }

                $result[$key] = $this->collIncidentCategories->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collIncidentReporters) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'incidentReporters';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'incident_reporters';
                        break;
                    default:
                        $key = 'IncidentReporters';
                }

                $result[$key] = $this->collIncidentReporters->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collIncidentResources) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'incidentResources';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'incident_resources';
                        break;
                    default:
                        $key = 'IncidentResources';
                }

                $result[$key] = $this->collIncidentResources->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collIncidentResourceRecords) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'incidentResourceRecords';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'incident_resource_records';
                        break;
                    default:
                        $key = 'IncidentResourceRecords';
                }

                $result[$key] = $this->collIncidentResourceRecords->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Incident
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = IncidentTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Incident
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setTitle($value);
                break;
            case 2:
                $this->setLocation($value);
                break;
            case 3:
                $this->setLatitude($value);
                break;
            case 4:
                $this->setLongitude($value);
                break;
            case 5:
                $this->setActive($value);
                break;
            case 6:
                $this->setCreatedAt($value);
                break;
            case 7:
                $this->setUpdatedAt($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = IncidentTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setTitle($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setLocation($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setLatitude($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setLongitude($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setActive($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setCreatedAt($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setUpdatedAt($arr[$keys[7]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\Incident The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(IncidentTableMap::DATABASE_NAME);

        if ($this->isColumnModified(IncidentTableMap::COL_ID)) {
            $criteria->add(IncidentTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(IncidentTableMap::COL_TITLE)) {
            $criteria->add(IncidentTableMap::COL_TITLE, $this->title);
        }
        if ($this->isColumnModified(IncidentTableMap::COL_LOCATION)) {
            $criteria->add(IncidentTableMap::COL_LOCATION, $this->location);
        }
        if ($this->isColumnModified(IncidentTableMap::COL_LATITUDE)) {
            $criteria->add(IncidentTableMap::COL_LATITUDE, $this->latitude);
        }
        if ($this->isColumnModified(IncidentTableMap::COL_LONGITUDE)) {
            $criteria->add(IncidentTableMap::COL_LONGITUDE, $this->longitude);
        }
        if ($this->isColumnModified(IncidentTableMap::COL_ACTIVE)) {
            $criteria->add(IncidentTableMap::COL_ACTIVE, $this->active);
        }
        if ($this->isColumnModified(IncidentTableMap::COL_CREATED_AT)) {
            $criteria->add(IncidentTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(IncidentTableMap::COL_UPDATED_AT)) {
            $criteria->add(IncidentTableMap::COL_UPDATED_AT, $this->updated_at);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildIncidentQuery::create();
        $criteria->add(IncidentTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Incident (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setLocation($this->getLocation());
        $copyObj->setLatitude($this->getLatitude());
        $copyObj->setLongitude($this->getLongitude());
        $copyObj->setActive($this->getActive());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getIncidentCategories() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addIncidentCategory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getIncidentReporters() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addIncidentReporter($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getIncidentResources() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addIncidentResource($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getIncidentResourceRecords() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addIncidentResourceRecord($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Incident Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('IncidentCategory' == $relationName) {
            return $this->initIncidentCategories();
        }
        if ('IncidentReporter' == $relationName) {
            return $this->initIncidentReporters();
        }
        if ('IncidentResource' == $relationName) {
            return $this->initIncidentResources();
        }
        if ('IncidentResourceRecord' == $relationName) {
            return $this->initIncidentResourceRecords();
        }
    }

    /**
     * Clears out the collIncidentCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addIncidentCategories()
     */
    public function clearIncidentCategories()
    {
        $this->collIncidentCategories = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collIncidentCategories collection loaded partially.
     */
    public function resetPartialIncidentCategories($v = true)
    {
        $this->collIncidentCategoriesPartial = $v;
    }

    /**
     * Initializes the collIncidentCategories collection.
     *
     * By default this just sets the collIncidentCategories collection to an empty array (like clearcollIncidentCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initIncidentCategories($overrideExisting = true)
    {
        if (null !== $this->collIncidentCategories && !$overrideExisting) {
            return;
        }

        $collectionClassName = IncidentCategoryTableMap::getTableMap()->getCollectionClassName();

        $this->collIncidentCategories = new $collectionClassName;
        $this->collIncidentCategories->setModel('\IncidentCategory');
    }

    /**
     * Gets an array of ChildIncidentCategory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildIncident is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildIncidentCategory[] List of ChildIncidentCategory objects
     * @throws PropelException
     */
    public function getIncidentCategories(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentCategoriesPartial && !$this->isNew();
        if (null === $this->collIncidentCategories || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collIncidentCategories) {
                // return empty collection
                $this->initIncidentCategories();
            } else {
                $collIncidentCategories = ChildIncidentCategoryQuery::create(null, $criteria)
                    ->filterByIncident($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collIncidentCategoriesPartial && count($collIncidentCategories)) {
                        $this->initIncidentCategories(false);

                        foreach ($collIncidentCategories as $obj) {
                            if (false == $this->collIncidentCategories->contains($obj)) {
                                $this->collIncidentCategories->append($obj);
                            }
                        }

                        $this->collIncidentCategoriesPartial = true;
                    }

                    return $collIncidentCategories;
                }

                if ($partial && $this->collIncidentCategories) {
                    foreach ($this->collIncidentCategories as $obj) {
                        if ($obj->isNew()) {
                            $collIncidentCategories[] = $obj;
                        }
                    }
                }

                $this->collIncidentCategories = $collIncidentCategories;
                $this->collIncidentCategoriesPartial = false;
            }
        }

        return $this->collIncidentCategories;
    }

    /**
     * Sets a collection of ChildIncidentCategory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $incidentCategories A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function setIncidentCategories(Collection $incidentCategories, ConnectionInterface $con = null)
    {
        /** @var ChildIncidentCategory[] $incidentCategoriesToDelete */
        $incidentCategoriesToDelete = $this->getIncidentCategories(new Criteria(), $con)->diff($incidentCategories);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->incidentCategoriesScheduledForDeletion = clone $incidentCategoriesToDelete;

        foreach ($incidentCategoriesToDelete as $incidentCategoryRemoved) {
            $incidentCategoryRemoved->setIncident(null);
        }

        $this->collIncidentCategories = null;
        foreach ($incidentCategories as $incidentCategory) {
            $this->addIncidentCategory($incidentCategory);
        }

        $this->collIncidentCategories = $incidentCategories;
        $this->collIncidentCategoriesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related IncidentCategory objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related IncidentCategory objects.
     * @throws PropelException
     */
    public function countIncidentCategories(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentCategoriesPartial && !$this->isNew();
        if (null === $this->collIncidentCategories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collIncidentCategories) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getIncidentCategories());
            }

            $query = ChildIncidentCategoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByIncident($this)
                ->count($con);
        }

        return count($this->collIncidentCategories);
    }

    /**
     * Method called to associate a ChildIncidentCategory object to this object
     * through the ChildIncidentCategory foreign key attribute.
     *
     * @param  ChildIncidentCategory $l ChildIncidentCategory
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function addIncidentCategory(ChildIncidentCategory $l)
    {
        if ($this->collIncidentCategories === null) {
            $this->initIncidentCategories();
            $this->collIncidentCategoriesPartial = true;
        }

        if (!$this->collIncidentCategories->contains($l)) {
            $this->doAddIncidentCategory($l);

            if ($this->incidentCategoriesScheduledForDeletion and $this->incidentCategoriesScheduledForDeletion->contains($l)) {
                $this->incidentCategoriesScheduledForDeletion->remove($this->incidentCategoriesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildIncidentCategory $incidentCategory The ChildIncidentCategory object to add.
     */
    protected function doAddIncidentCategory(ChildIncidentCategory $incidentCategory)
    {
        $this->collIncidentCategories[]= $incidentCategory;
        $incidentCategory->setIncident($this);
    }

    /**
     * @param  ChildIncidentCategory $incidentCategory The ChildIncidentCategory object to remove.
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function removeIncidentCategory(ChildIncidentCategory $incidentCategory)
    {
        if ($this->getIncidentCategories()->contains($incidentCategory)) {
            $pos = $this->collIncidentCategories->search($incidentCategory);
            $this->collIncidentCategories->remove($pos);
            if (null === $this->incidentCategoriesScheduledForDeletion) {
                $this->incidentCategoriesScheduledForDeletion = clone $this->collIncidentCategories;
                $this->incidentCategoriesScheduledForDeletion->clear();
            }
            $this->incidentCategoriesScheduledForDeletion[]= clone $incidentCategory;
            $incidentCategory->setIncident(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Incident is new, it will return
     * an empty collection; or if this Incident has previously
     * been saved, it will retrieve related IncidentCategories from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Incident.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildIncidentCategory[] List of ChildIncidentCategory objects
     */
    public function getIncidentCategoriesJoinCategory(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildIncidentCategoryQuery::create(null, $criteria);
        $query->joinWith('Category', $joinBehavior);

        return $this->getIncidentCategories($query, $con);
    }

    /**
     * Clears out the collIncidentReporters collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addIncidentReporters()
     */
    public function clearIncidentReporters()
    {
        $this->collIncidentReporters = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collIncidentReporters collection loaded partially.
     */
    public function resetPartialIncidentReporters($v = true)
    {
        $this->collIncidentReportersPartial = $v;
    }

    /**
     * Initializes the collIncidentReporters collection.
     *
     * By default this just sets the collIncidentReporters collection to an empty array (like clearcollIncidentReporters());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initIncidentReporters($overrideExisting = true)
    {
        if (null !== $this->collIncidentReporters && !$overrideExisting) {
            return;
        }

        $collectionClassName = IncidentReporterTableMap::getTableMap()->getCollectionClassName();

        $this->collIncidentReporters = new $collectionClassName;
        $this->collIncidentReporters->setModel('\IncidentReporter');
    }

    /**
     * Gets an array of ChildIncidentReporter objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildIncident is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildIncidentReporter[] List of ChildIncidentReporter objects
     * @throws PropelException
     */
    public function getIncidentReporters(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentReportersPartial && !$this->isNew();
        if (null === $this->collIncidentReporters || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collIncidentReporters) {
                // return empty collection
                $this->initIncidentReporters();
            } else {
                $collIncidentReporters = ChildIncidentReporterQuery::create(null, $criteria)
                    ->filterByIncident($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collIncidentReportersPartial && count($collIncidentReporters)) {
                        $this->initIncidentReporters(false);

                        foreach ($collIncidentReporters as $obj) {
                            if (false == $this->collIncidentReporters->contains($obj)) {
                                $this->collIncidentReporters->append($obj);
                            }
                        }

                        $this->collIncidentReportersPartial = true;
                    }

                    return $collIncidentReporters;
                }

                if ($partial && $this->collIncidentReporters) {
                    foreach ($this->collIncidentReporters as $obj) {
                        if ($obj->isNew()) {
                            $collIncidentReporters[] = $obj;
                        }
                    }
                }

                $this->collIncidentReporters = $collIncidentReporters;
                $this->collIncidentReportersPartial = false;
            }
        }

        return $this->collIncidentReporters;
    }

    /**
     * Sets a collection of ChildIncidentReporter objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $incidentReporters A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function setIncidentReporters(Collection $incidentReporters, ConnectionInterface $con = null)
    {
        /** @var ChildIncidentReporter[] $incidentReportersToDelete */
        $incidentReportersToDelete = $this->getIncidentReporters(new Criteria(), $con)->diff($incidentReporters);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->incidentReportersScheduledForDeletion = clone $incidentReportersToDelete;

        foreach ($incidentReportersToDelete as $incidentReporterRemoved) {
            $incidentReporterRemoved->setIncident(null);
        }

        $this->collIncidentReporters = null;
        foreach ($incidentReporters as $incidentReporter) {
            $this->addIncidentReporter($incidentReporter);
        }

        $this->collIncidentReporters = $incidentReporters;
        $this->collIncidentReportersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related IncidentReporter objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related IncidentReporter objects.
     * @throws PropelException
     */
    public function countIncidentReporters(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentReportersPartial && !$this->isNew();
        if (null === $this->collIncidentReporters || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collIncidentReporters) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getIncidentReporters());
            }

            $query = ChildIncidentReporterQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByIncident($this)
                ->count($con);
        }

        return count($this->collIncidentReporters);
    }

    /**
     * Method called to associate a ChildIncidentReporter object to this object
     * through the ChildIncidentReporter foreign key attribute.
     *
     * @param  ChildIncidentReporter $l ChildIncidentReporter
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function addIncidentReporter(ChildIncidentReporter $l)
    {
        if ($this->collIncidentReporters === null) {
            $this->initIncidentReporters();
            $this->collIncidentReportersPartial = true;
        }

        if (!$this->collIncidentReporters->contains($l)) {
            $this->doAddIncidentReporter($l);

            if ($this->incidentReportersScheduledForDeletion and $this->incidentReportersScheduledForDeletion->contains($l)) {
                $this->incidentReportersScheduledForDeletion->remove($this->incidentReportersScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildIncidentReporter $incidentReporter The ChildIncidentReporter object to add.
     */
    protected function doAddIncidentReporter(ChildIncidentReporter $incidentReporter)
    {
        $this->collIncidentReporters[]= $incidentReporter;
        $incidentReporter->setIncident($this);
    }

    /**
     * @param  ChildIncidentReporter $incidentReporter The ChildIncidentReporter object to remove.
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function removeIncidentReporter(ChildIncidentReporter $incidentReporter)
    {
        if ($this->getIncidentReporters()->contains($incidentReporter)) {
            $pos = $this->collIncidentReporters->search($incidentReporter);
            $this->collIncidentReporters->remove($pos);
            if (null === $this->incidentReportersScheduledForDeletion) {
                $this->incidentReportersScheduledForDeletion = clone $this->collIncidentReporters;
                $this->incidentReportersScheduledForDeletion->clear();
            }
            $this->incidentReportersScheduledForDeletion[]= clone $incidentReporter;
            $incidentReporter->setIncident(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Incident is new, it will return
     * an empty collection; or if this Incident has previously
     * been saved, it will retrieve related IncidentReporters from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Incident.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildIncidentReporter[] List of ChildIncidentReporter objects
     */
    public function getIncidentReportersJoinReporter(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildIncidentReporterQuery::create(null, $criteria);
        $query->joinWith('Reporter', $joinBehavior);

        return $this->getIncidentReporters($query, $con);
    }

    /**
     * Clears out the collIncidentResources collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addIncidentResources()
     */
    public function clearIncidentResources()
    {
        $this->collIncidentResources = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collIncidentResources collection loaded partially.
     */
    public function resetPartialIncidentResources($v = true)
    {
        $this->collIncidentResourcesPartial = $v;
    }

    /**
     * Initializes the collIncidentResources collection.
     *
     * By default this just sets the collIncidentResources collection to an empty array (like clearcollIncidentResources());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initIncidentResources($overrideExisting = true)
    {
        if (null !== $this->collIncidentResources && !$overrideExisting) {
            return;
        }

        $collectionClassName = IncidentResourceTableMap::getTableMap()->getCollectionClassName();

        $this->collIncidentResources = new $collectionClassName;
        $this->collIncidentResources->setModel('\IncidentResource');
    }

    /**
     * Gets an array of ChildIncidentResource objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildIncident is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildIncidentResource[] List of ChildIncidentResource objects
     * @throws PropelException
     */
    public function getIncidentResources(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentResourcesPartial && !$this->isNew();
        if (null === $this->collIncidentResources || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collIncidentResources) {
                // return empty collection
                $this->initIncidentResources();
            } else {
                $collIncidentResources = ChildIncidentResourceQuery::create(null, $criteria)
                    ->filterByIncident($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collIncidentResourcesPartial && count($collIncidentResources)) {
                        $this->initIncidentResources(false);

                        foreach ($collIncidentResources as $obj) {
                            if (false == $this->collIncidentResources->contains($obj)) {
                                $this->collIncidentResources->append($obj);
                            }
                        }

                        $this->collIncidentResourcesPartial = true;
                    }

                    return $collIncidentResources;
                }

                if ($partial && $this->collIncidentResources) {
                    foreach ($this->collIncidentResources as $obj) {
                        if ($obj->isNew()) {
                            $collIncidentResources[] = $obj;
                        }
                    }
                }

                $this->collIncidentResources = $collIncidentResources;
                $this->collIncidentResourcesPartial = false;
            }
        }

        return $this->collIncidentResources;
    }

    /**
     * Sets a collection of ChildIncidentResource objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $incidentResources A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function setIncidentResources(Collection $incidentResources, ConnectionInterface $con = null)
    {
        /** @var ChildIncidentResource[] $incidentResourcesToDelete */
        $incidentResourcesToDelete = $this->getIncidentResources(new Criteria(), $con)->diff($incidentResources);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->incidentResourcesScheduledForDeletion = clone $incidentResourcesToDelete;

        foreach ($incidentResourcesToDelete as $incidentResourceRemoved) {
            $incidentResourceRemoved->setIncident(null);
        }

        $this->collIncidentResources = null;
        foreach ($incidentResources as $incidentResource) {
            $this->addIncidentResource($incidentResource);
        }

        $this->collIncidentResources = $incidentResources;
        $this->collIncidentResourcesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related IncidentResource objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related IncidentResource objects.
     * @throws PropelException
     */
    public function countIncidentResources(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentResourcesPartial && !$this->isNew();
        if (null === $this->collIncidentResources || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collIncidentResources) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getIncidentResources());
            }

            $query = ChildIncidentResourceQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByIncident($this)
                ->count($con);
        }

        return count($this->collIncidentResources);
    }

    /**
     * Method called to associate a ChildIncidentResource object to this object
     * through the ChildIncidentResource foreign key attribute.
     *
     * @param  ChildIncidentResource $l ChildIncidentResource
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function addIncidentResource(ChildIncidentResource $l)
    {
        if ($this->collIncidentResources === null) {
            $this->initIncidentResources();
            $this->collIncidentResourcesPartial = true;
        }

        if (!$this->collIncidentResources->contains($l)) {
            $this->doAddIncidentResource($l);

            if ($this->incidentResourcesScheduledForDeletion and $this->incidentResourcesScheduledForDeletion->contains($l)) {
                $this->incidentResourcesScheduledForDeletion->remove($this->incidentResourcesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildIncidentResource $incidentResource The ChildIncidentResource object to add.
     */
    protected function doAddIncidentResource(ChildIncidentResource $incidentResource)
    {
        $this->collIncidentResources[]= $incidentResource;
        $incidentResource->setIncident($this);
    }

    /**
     * @param  ChildIncidentResource $incidentResource The ChildIncidentResource object to remove.
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function removeIncidentResource(ChildIncidentResource $incidentResource)
    {
        if ($this->getIncidentResources()->contains($incidentResource)) {
            $pos = $this->collIncidentResources->search($incidentResource);
            $this->collIncidentResources->remove($pos);
            if (null === $this->incidentResourcesScheduledForDeletion) {
                $this->incidentResourcesScheduledForDeletion = clone $this->collIncidentResources;
                $this->incidentResourcesScheduledForDeletion->clear();
            }
            $this->incidentResourcesScheduledForDeletion[]= clone $incidentResource;
            $incidentResource->setIncident(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Incident is new, it will return
     * an empty collection; or if this Incident has previously
     * been saved, it will retrieve related IncidentResources from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Incident.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildIncidentResource[] List of ChildIncidentResource objects
     */
    public function getIncidentResourcesJoinResource(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildIncidentResourceQuery::create(null, $criteria);
        $query->joinWith('Resource', $joinBehavior);

        return $this->getIncidentResources($query, $con);
    }

    /**
     * Clears out the collIncidentResourceRecords collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addIncidentResourceRecords()
     */
    public function clearIncidentResourceRecords()
    {
        $this->collIncidentResourceRecords = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collIncidentResourceRecords collection loaded partially.
     */
    public function resetPartialIncidentResourceRecords($v = true)
    {
        $this->collIncidentResourceRecordsPartial = $v;
    }

    /**
     * Initializes the collIncidentResourceRecords collection.
     *
     * By default this just sets the collIncidentResourceRecords collection to an empty array (like clearcollIncidentResourceRecords());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initIncidentResourceRecords($overrideExisting = true)
    {
        if (null !== $this->collIncidentResourceRecords && !$overrideExisting) {
            return;
        }

        $collectionClassName = IncidentResourceRecordTableMap::getTableMap()->getCollectionClassName();

        $this->collIncidentResourceRecords = new $collectionClassName;
        $this->collIncidentResourceRecords->setModel('\IncidentResourceRecord');
    }

    /**
     * Gets an array of ChildIncidentResourceRecord objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildIncident is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildIncidentResourceRecord[] List of ChildIncidentResourceRecord objects
     * @throws PropelException
     */
    public function getIncidentResourceRecords(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentResourceRecordsPartial && !$this->isNew();
        if (null === $this->collIncidentResourceRecords || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collIncidentResourceRecords) {
                // return empty collection
                $this->initIncidentResourceRecords();
            } else {
                $collIncidentResourceRecords = ChildIncidentResourceRecordQuery::create(null, $criteria)
                    ->filterByIncident($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collIncidentResourceRecordsPartial && count($collIncidentResourceRecords)) {
                        $this->initIncidentResourceRecords(false);

                        foreach ($collIncidentResourceRecords as $obj) {
                            if (false == $this->collIncidentResourceRecords->contains($obj)) {
                                $this->collIncidentResourceRecords->append($obj);
                            }
                        }

                        $this->collIncidentResourceRecordsPartial = true;
                    }

                    return $collIncidentResourceRecords;
                }

                if ($partial && $this->collIncidentResourceRecords) {
                    foreach ($this->collIncidentResourceRecords as $obj) {
                        if ($obj->isNew()) {
                            $collIncidentResourceRecords[] = $obj;
                        }
                    }
                }

                $this->collIncidentResourceRecords = $collIncidentResourceRecords;
                $this->collIncidentResourceRecordsPartial = false;
            }
        }

        return $this->collIncidentResourceRecords;
    }

    /**
     * Sets a collection of ChildIncidentResourceRecord objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $incidentResourceRecords A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function setIncidentResourceRecords(Collection $incidentResourceRecords, ConnectionInterface $con = null)
    {
        /** @var ChildIncidentResourceRecord[] $incidentResourceRecordsToDelete */
        $incidentResourceRecordsToDelete = $this->getIncidentResourceRecords(new Criteria(), $con)->diff($incidentResourceRecords);


        $this->incidentResourceRecordsScheduledForDeletion = $incidentResourceRecordsToDelete;

        foreach ($incidentResourceRecordsToDelete as $incidentResourceRecordRemoved) {
            $incidentResourceRecordRemoved->setIncident(null);
        }

        $this->collIncidentResourceRecords = null;
        foreach ($incidentResourceRecords as $incidentResourceRecord) {
            $this->addIncidentResourceRecord($incidentResourceRecord);
        }

        $this->collIncidentResourceRecords = $incidentResourceRecords;
        $this->collIncidentResourceRecordsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related IncidentResourceRecord objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related IncidentResourceRecord objects.
     * @throws PropelException
     */
    public function countIncidentResourceRecords(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentResourceRecordsPartial && !$this->isNew();
        if (null === $this->collIncidentResourceRecords || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collIncidentResourceRecords) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getIncidentResourceRecords());
            }

            $query = ChildIncidentResourceRecordQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByIncident($this)
                ->count($con);
        }

        return count($this->collIncidentResourceRecords);
    }

    /**
     * Method called to associate a ChildIncidentResourceRecord object to this object
     * through the ChildIncidentResourceRecord foreign key attribute.
     *
     * @param  ChildIncidentResourceRecord $l ChildIncidentResourceRecord
     * @return $this|\Incident The current object (for fluent API support)
     */
    public function addIncidentResourceRecord(ChildIncidentResourceRecord $l)
    {
        if ($this->collIncidentResourceRecords === null) {
            $this->initIncidentResourceRecords();
            $this->collIncidentResourceRecordsPartial = true;
        }

        if (!$this->collIncidentResourceRecords->contains($l)) {
            $this->doAddIncidentResourceRecord($l);

            if ($this->incidentResourceRecordsScheduledForDeletion and $this->incidentResourceRecordsScheduledForDeletion->contains($l)) {
                $this->incidentResourceRecordsScheduledForDeletion->remove($this->incidentResourceRecordsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildIncidentResourceRecord $incidentResourceRecord The ChildIncidentResourceRecord object to add.
     */
    protected function doAddIncidentResourceRecord(ChildIncidentResourceRecord $incidentResourceRecord)
    {
        $this->collIncidentResourceRecords[]= $incidentResourceRecord;
        $incidentResourceRecord->setIncident($this);
    }

    /**
     * @param  ChildIncidentResourceRecord $incidentResourceRecord The ChildIncidentResourceRecord object to remove.
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function removeIncidentResourceRecord(ChildIncidentResourceRecord $incidentResourceRecord)
    {
        if ($this->getIncidentResourceRecords()->contains($incidentResourceRecord)) {
            $pos = $this->collIncidentResourceRecords->search($incidentResourceRecord);
            $this->collIncidentResourceRecords->remove($pos);
            if (null === $this->incidentResourceRecordsScheduledForDeletion) {
                $this->incidentResourceRecordsScheduledForDeletion = clone $this->collIncidentResourceRecords;
                $this->incidentResourceRecordsScheduledForDeletion->clear();
            }
            $this->incidentResourceRecordsScheduledForDeletion[]= $incidentResourceRecord;
            $incidentResourceRecord->setIncident(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Incident is new, it will return
     * an empty collection; or if this Incident has previously
     * been saved, it will retrieve related IncidentResourceRecords from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Incident.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildIncidentResourceRecord[] List of ChildIncidentResourceRecord objects
     */
    public function getIncidentResourceRecordsJoinResource(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildIncidentResourceRecordQuery::create(null, $criteria);
        $query->joinWith('Resource', $joinBehavior);

        return $this->getIncidentResourceRecords($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Incident is new, it will return
     * an empty collection; or if this Incident has previously
     * been saved, it will retrieve related IncidentResourceRecords from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Incident.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildIncidentResourceRecord[] List of ChildIncidentResourceRecord objects
     */
    public function getIncidentResourceRecordsJoinReporter(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildIncidentResourceRecordQuery::create(null, $criteria);
        $query->joinWith('Reporter', $joinBehavior);

        return $this->getIncidentResourceRecords($query, $con);
    }

    /**
     * Clears out the collCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCategories()
     */
    public function clearCategories()
    {
        $this->collCategories = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collCategories crossRef collection.
     *
     * By default this just sets the collCategories collection to an empty collection (like clearCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initCategories()
    {
        $collectionClassName = IncidentCategoryTableMap::getTableMap()->getCollectionClassName();

        $this->collCategories = new $collectionClassName;
        $this->collCategoriesPartial = true;
        $this->collCategories->setModel('\Category');
    }

    /**
     * Checks if the collCategories collection is loaded.
     *
     * @return bool
     */
    public function isCategoriesLoaded()
    {
        return null !== $this->collCategories;
    }

    /**
     * Gets a collection of ChildCategory objects related by a many-to-many relationship
     * to the current object by way of the incident_category cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildIncident is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildCategory[] List of ChildCategory objects
     */
    public function getCategories(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoriesPartial && !$this->isNew();
        if (null === $this->collCategories || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collCategories) {
                    $this->initCategories();
                }
            } else {

                $query = ChildCategoryQuery::create(null, $criteria)
                    ->filterByIncident($this);
                $collCategories = $query->find($con);
                if (null !== $criteria) {
                    return $collCategories;
                }

                if ($partial && $this->collCategories) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collCategories as $obj) {
                        if (!$collCategories->contains($obj)) {
                            $collCategories[] = $obj;
                        }
                    }
                }

                $this->collCategories = $collCategories;
                $this->collCategoriesPartial = false;
            }
        }

        return $this->collCategories;
    }

    /**
     * Sets a collection of Category objects related by a many-to-many relationship
     * to the current object by way of the incident_category cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $categories A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function setCategories(Collection $categories, ConnectionInterface $con = null)
    {
        $this->clearCategories();
        $currentCategories = $this->getCategories();

        $categoriesScheduledForDeletion = $currentCategories->diff($categories);

        foreach ($categoriesScheduledForDeletion as $toDelete) {
            $this->removeCategory($toDelete);
        }

        foreach ($categories as $category) {
            if (!$currentCategories->contains($category)) {
                $this->doAddCategory($category);
            }
        }

        $this->collCategoriesPartial = false;
        $this->collCategories = $categories;

        return $this;
    }

    /**
     * Gets the number of Category objects related by a many-to-many relationship
     * to the current object by way of the incident_category cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Category objects
     */
    public function countCategories(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoriesPartial && !$this->isNew();
        if (null === $this->collCategories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategories) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getCategories());
                }

                $query = ChildCategoryQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByIncident($this)
                    ->count($con);
            }
        } else {
            return count($this->collCategories);
        }
    }

    /**
     * Associate a ChildCategory to this object
     * through the incident_category cross reference table.
     *
     * @param ChildCategory $category
     * @return ChildIncident The current object (for fluent API support)
     */
    public function addCategory(ChildCategory $category)
    {
        if ($this->collCategories === null) {
            $this->initCategories();
        }

        if (!$this->getCategories()->contains($category)) {
            // only add it if the **same** object is not already associated
            $this->collCategories->push($category);
            $this->doAddCategory($category);
        }

        return $this;
    }

    /**
     *
     * @param ChildCategory $category
     */
    protected function doAddCategory(ChildCategory $category)
    {
        $incidentCategory = new ChildIncidentCategory();

        $incidentCategory->setCategory($category);

        $incidentCategory->setIncident($this);

        $this->addIncidentCategory($incidentCategory);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$category->isIncidentsLoaded()) {
            $category->initIncidents();
            $category->getIncidents()->push($this);
        } elseif (!$category->getIncidents()->contains($this)) {
            $category->getIncidents()->push($this);
        }

    }

    /**
     * Remove category of this object
     * through the incident_category cross reference table.
     *
     * @param ChildCategory $category
     * @return ChildIncident The current object (for fluent API support)
     */
    public function removeCategory(ChildCategory $category)
    {
        if ($this->getCategories()->contains($category)) { $incidentCategory = new ChildIncidentCategory();

            $incidentCategory->setCategory($category);
            if ($category->isIncidentsLoaded()) {
                //remove the back reference if available
                $category->getIncidents()->removeObject($this);
            }

            $incidentCategory->setIncident($this);
            $this->removeIncidentCategory(clone $incidentCategory);
            $incidentCategory->clear();

            $this->collCategories->remove($this->collCategories->search($category));

            if (null === $this->categoriesScheduledForDeletion) {
                $this->categoriesScheduledForDeletion = clone $this->collCategories;
                $this->categoriesScheduledForDeletion->clear();
            }

            $this->categoriesScheduledForDeletion->push($category);
        }


        return $this;
    }

    /**
     * Clears out the collReporters collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addReporters()
     */
    public function clearReporters()
    {
        $this->collReporters = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collReporters crossRef collection.
     *
     * By default this just sets the collReporters collection to an empty collection (like clearReporters());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initReporters()
    {
        $collectionClassName = IncidentReporterTableMap::getTableMap()->getCollectionClassName();

        $this->collReporters = new $collectionClassName;
        $this->collReportersPartial = true;
        $this->collReporters->setModel('\Reporter');
    }

    /**
     * Checks if the collReporters collection is loaded.
     *
     * @return bool
     */
    public function isReportersLoaded()
    {
        return null !== $this->collReporters;
    }

    /**
     * Gets a collection of ChildReporter objects related by a many-to-many relationship
     * to the current object by way of the incident_reporter cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildIncident is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildReporter[] List of ChildReporter objects
     */
    public function getReporters(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collReportersPartial && !$this->isNew();
        if (null === $this->collReporters || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collReporters) {
                    $this->initReporters();
                }
            } else {

                $query = ChildReporterQuery::create(null, $criteria)
                    ->filterByIncident($this);
                $collReporters = $query->find($con);
                if (null !== $criteria) {
                    return $collReporters;
                }

                if ($partial && $this->collReporters) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collReporters as $obj) {
                        if (!$collReporters->contains($obj)) {
                            $collReporters[] = $obj;
                        }
                    }
                }

                $this->collReporters = $collReporters;
                $this->collReportersPartial = false;
            }
        }

        return $this->collReporters;
    }

    /**
     * Sets a collection of Reporter objects related by a many-to-many relationship
     * to the current object by way of the incident_reporter cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $reporters A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function setReporters(Collection $reporters, ConnectionInterface $con = null)
    {
        $this->clearReporters();
        $currentReporters = $this->getReporters();

        $reportersScheduledForDeletion = $currentReporters->diff($reporters);

        foreach ($reportersScheduledForDeletion as $toDelete) {
            $this->removeReporter($toDelete);
        }

        foreach ($reporters as $reporter) {
            if (!$currentReporters->contains($reporter)) {
                $this->doAddReporter($reporter);
            }
        }

        $this->collReportersPartial = false;
        $this->collReporters = $reporters;

        return $this;
    }

    /**
     * Gets the number of Reporter objects related by a many-to-many relationship
     * to the current object by way of the incident_reporter cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Reporter objects
     */
    public function countReporters(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collReportersPartial && !$this->isNew();
        if (null === $this->collReporters || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collReporters) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getReporters());
                }

                $query = ChildReporterQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByIncident($this)
                    ->count($con);
            }
        } else {
            return count($this->collReporters);
        }
    }

    /**
     * Associate a ChildReporter to this object
     * through the incident_reporter cross reference table.
     *
     * @param ChildReporter $reporter
     * @return ChildIncident The current object (for fluent API support)
     */
    public function addReporter(ChildReporter $reporter)
    {
        if ($this->collReporters === null) {
            $this->initReporters();
        }

        if (!$this->getReporters()->contains($reporter)) {
            // only add it if the **same** object is not already associated
            $this->collReporters->push($reporter);
            $this->doAddReporter($reporter);
        }

        return $this;
    }

    /**
     *
     * @param ChildReporter $reporter
     */
    protected function doAddReporter(ChildReporter $reporter)
    {
        $incidentReporter = new ChildIncidentReporter();

        $incidentReporter->setReporter($reporter);

        $incidentReporter->setIncident($this);

        $this->addIncidentReporter($incidentReporter);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$reporter->isIncidentsLoaded()) {
            $reporter->initIncidents();
            $reporter->getIncidents()->push($this);
        } elseif (!$reporter->getIncidents()->contains($this)) {
            $reporter->getIncidents()->push($this);
        }

    }

    /**
     * Remove reporter of this object
     * through the incident_reporter cross reference table.
     *
     * @param ChildReporter $reporter
     * @return ChildIncident The current object (for fluent API support)
     */
    public function removeReporter(ChildReporter $reporter)
    {
        if ($this->getReporters()->contains($reporter)) { $incidentReporter = new ChildIncidentReporter();

            $incidentReporter->setReporter($reporter);
            if ($reporter->isIncidentsLoaded()) {
                //remove the back reference if available
                $reporter->getIncidents()->removeObject($this);
            }

            $incidentReporter->setIncident($this);
            $this->removeIncidentReporter(clone $incidentReporter);
            $incidentReporter->clear();

            $this->collReporters->remove($this->collReporters->search($reporter));

            if (null === $this->reportersScheduledForDeletion) {
                $this->reportersScheduledForDeletion = clone $this->collReporters;
                $this->reportersScheduledForDeletion->clear();
            }

            $this->reportersScheduledForDeletion->push($reporter);
        }


        return $this;
    }

    /**
     * Clears out the collResources collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addResources()
     */
    public function clearResources()
    {
        $this->collResources = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collResources crossRef collection.
     *
     * By default this just sets the collResources collection to an empty collection (like clearResources());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initResources()
    {
        $collectionClassName = IncidentResourceTableMap::getTableMap()->getCollectionClassName();

        $this->collResources = new $collectionClassName;
        $this->collResourcesPartial = true;
        $this->collResources->setModel('\Resource');
    }

    /**
     * Checks if the collResources collection is loaded.
     *
     * @return bool
     */
    public function isResourcesLoaded()
    {
        return null !== $this->collResources;
    }

    /**
     * Gets a collection of ChildResource objects related by a many-to-many relationship
     * to the current object by way of the incident_resource cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildIncident is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildResource[] List of ChildResource objects
     */
    public function getResources(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collResourcesPartial && !$this->isNew();
        if (null === $this->collResources || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collResources) {
                    $this->initResources();
                }
            } else {

                $query = ChildResourceQuery::create(null, $criteria)
                    ->filterByIncident($this);
                $collResources = $query->find($con);
                if (null !== $criteria) {
                    return $collResources;
                }

                if ($partial && $this->collResources) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collResources as $obj) {
                        if (!$collResources->contains($obj)) {
                            $collResources[] = $obj;
                        }
                    }
                }

                $this->collResources = $collResources;
                $this->collResourcesPartial = false;
            }
        }

        return $this->collResources;
    }

    /**
     * Sets a collection of Resource objects related by a many-to-many relationship
     * to the current object by way of the incident_resource cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $resources A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildIncident The current object (for fluent API support)
     */
    public function setResources(Collection $resources, ConnectionInterface $con = null)
    {
        $this->clearResources();
        $currentResources = $this->getResources();

        $resourcesScheduledForDeletion = $currentResources->diff($resources);

        foreach ($resourcesScheduledForDeletion as $toDelete) {
            $this->removeResource($toDelete);
        }

        foreach ($resources as $resource) {
            if (!$currentResources->contains($resource)) {
                $this->doAddResource($resource);
            }
        }

        $this->collResourcesPartial = false;
        $this->collResources = $resources;

        return $this;
    }

    /**
     * Gets the number of Resource objects related by a many-to-many relationship
     * to the current object by way of the incident_resource cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Resource objects
     */
    public function countResources(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collResourcesPartial && !$this->isNew();
        if (null === $this->collResources || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collResources) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getResources());
                }

                $query = ChildResourceQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByIncident($this)
                    ->count($con);
            }
        } else {
            return count($this->collResources);
        }
    }

    /**
     * Associate a ChildResource to this object
     * through the incident_resource cross reference table.
     *
     * @param ChildResource $resource
     * @return ChildIncident The current object (for fluent API support)
     */
    public function addResource(ChildResource $resource)
    {
        if ($this->collResources === null) {
            $this->initResources();
        }

        if (!$this->getResources()->contains($resource)) {
            // only add it if the **same** object is not already associated
            $this->collResources->push($resource);
            $this->doAddResource($resource);
        }

        return $this;
    }

    /**
     *
     * @param ChildResource $resource
     */
    protected function doAddResource(ChildResource $resource)
    {
        $incidentResource = new ChildIncidentResource();

        $incidentResource->setResource($resource);

        $incidentResource->setIncident($this);

        $this->addIncidentResource($incidentResource);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$resource->isIncidentsLoaded()) {
            $resource->initIncidents();
            $resource->getIncidents()->push($this);
        } elseif (!$resource->getIncidents()->contains($this)) {
            $resource->getIncidents()->push($this);
        }

    }

    /**
     * Remove resource of this object
     * through the incident_resource cross reference table.
     *
     * @param ChildResource $resource
     * @return ChildIncident The current object (for fluent API support)
     */
    public function removeResource(ChildResource $resource)
    {
        if ($this->getResources()->contains($resource)) { $incidentResource = new ChildIncidentResource();

            $incidentResource->setResource($resource);
            if ($resource->isIncidentsLoaded()) {
                //remove the back reference if available
                $resource->getIncidents()->removeObject($this);
            }

            $incidentResource->setIncident($this);
            $this->removeIncidentResource(clone $incidentResource);
            $incidentResource->clear();

            $this->collResources->remove($this->collResources->search($resource));

            if (null === $this->resourcesScheduledForDeletion) {
                $this->resourcesScheduledForDeletion = clone $this->collResources;
                $this->resourcesScheduledForDeletion->clear();
            }

            $this->resourcesScheduledForDeletion->push($resource);
        }


        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        $this->id = null;
        $this->title = null;
        $this->location = null;
        $this->latitude = null;
        $this->longitude = null;
        $this->active = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collIncidentCategories) {
                foreach ($this->collIncidentCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collIncidentReporters) {
                foreach ($this->collIncidentReporters as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collIncidentResources) {
                foreach ($this->collIncidentResources as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collIncidentResourceRecords) {
                foreach ($this->collIncidentResourceRecords as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategories) {
                foreach ($this->collCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collReporters) {
                foreach ($this->collReporters as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collResources) {
                foreach ($this->collResources as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collIncidentCategories = null;
        $this->collIncidentReporters = null;
        $this->collIncidentResources = null;
        $this->collIncidentResourceRecords = null;
        $this->collCategories = null;
        $this->collReporters = null;
        $this->collResources = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(IncidentTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildIncident The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[IncidentTableMap::COL_UPDATED_AT] = true;

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
