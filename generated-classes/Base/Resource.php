<?php

namespace Base;

use \Incident as ChildIncident;
use \IncidentQuery as ChildIncidentQuery;
use \IncidentResource as ChildIncidentResource;
use \IncidentResourceQuery as ChildIncidentResourceQuery;
use \IncidentResourceRecord as ChildIncidentResourceRecord;
use \IncidentResourceRecordQuery as ChildIncidentResourceRecordQuery;
use \Resource as ChildResource;
use \ResourceQuery as ChildResourceQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\IncidentResourceRecordTableMap;
use Map\IncidentResourceTableMap;
use Map\ResourceTableMap;
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
 * Base class that represents a row from the 'resource' table.
 *
 *
 *
* @package    propel.generator..Base
*/
abstract class Resource implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\ResourceTableMap';


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
     * The value for the name field.
     *
     * @var        string
     */
    protected $name;

    /**
     * The value for the image field.
     *
     * @var        string
     */
    protected $image;

    /**
     * The value for the tel field.
     *
     * @var        string
     */
    protected $tel;

    /**
     * The value for the sms field.
     *
     * @var        boolean
     */
    protected $sms;

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
     * @var        ObjectCollection|ChildIncident[] Cross Collection to store aggregation of ChildIncident objects.
     */
    protected $collIncidents;

    /**
     * @var bool
     */
    protected $collIncidentsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildIncident[]
     */
    protected $incidentsScheduledForDeletion = null;

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
     * Initializes internal state of Base\Resource object.
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
     * Compares this with another <code>Resource</code> instance.  If
     * <code>obj</code> is an instance of <code>Resource</code>, delegates to
     * <code>equals(Resource)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Resource The current object, for fluid interface
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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [image] column value.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get the [tel] column value.
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Get the [sms] column value.
     *
     * @return boolean
     */
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * Get the [sms] column value.
     *
     * @return boolean
     */
    public function isSms()
    {
        return $this->getSms();
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
     * @return $this|\Resource The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ResourceTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return $this|\Resource The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[ResourceTableMap::COL_NAME] = true;
        }

        return $this;
    } // setName()

    /**
     * Set the value of [image] column.
     *
     * @param string $v new value
     * @return $this|\Resource The current object (for fluent API support)
     */
    public function setImage($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->image !== $v) {
            $this->image = $v;
            $this->modifiedColumns[ResourceTableMap::COL_IMAGE] = true;
        }

        return $this;
    } // setImage()

    /**
     * Set the value of [tel] column.
     *
     * @param string $v new value
     * @return $this|\Resource The current object (for fluent API support)
     */
    public function setTel($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->tel !== $v) {
            $this->tel = $v;
            $this->modifiedColumns[ResourceTableMap::COL_TEL] = true;
        }

        return $this;
    } // setTel()

    /**
     * Sets the value of the [sms] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param  boolean|integer|string $v The new value
     * @return $this|\Resource The current object (for fluent API support)
     */
    public function setSms($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->sms !== $v) {
            $this->sms = $v;
            $this->modifiedColumns[ResourceTableMap::COL_SMS] = true;
        }

        return $this;
    } // setSms()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Resource The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($this->created_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->created_at->format("Y-m-d H:i:s")) {
                $this->created_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[ResourceTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Resource The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($this->updated_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->updated_at->format("Y-m-d H:i:s")) {
                $this->updated_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[ResourceTableMap::COL_UPDATED_AT] = true;
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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ResourceTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ResourceTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ResourceTableMap::translateFieldName('Image', TableMap::TYPE_PHPNAME, $indexType)];
            $this->image = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ResourceTableMap::translateFieldName('Tel', TableMap::TYPE_PHPNAME, $indexType)];
            $this->tel = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ResourceTableMap::translateFieldName('Sms', TableMap::TYPE_PHPNAME, $indexType)];
            $this->sms = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : ResourceTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : ResourceTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = ResourceTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Resource'), 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(ResourceTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildResourceQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collIncidentResources = null;

            $this->collIncidentResourceRecords = null;

            $this->collIncidents = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Resource::setDeleted()
     * @see Resource::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ResourceTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildResourceQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(ResourceTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior

                if (!$this->isColumnModified(ResourceTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(ResourceTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ResourceTableMap::COL_UPDATED_AT)) {
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
                ResourceTableMap::addInstanceToPool($this);
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

            if ($this->incidentsScheduledForDeletion !== null) {
                if (!$this->incidentsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->incidentsScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[1] = $this->getId();
                        $entryPk[0] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \IncidentResourceQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->incidentsScheduledForDeletion = null;
                }

            }

            if ($this->collIncidents) {
                foreach ($this->collIncidents as $incident) {
                    if (!$incident->isDeleted() && ($incident->isNew() || $incident->isModified())) {
                        $incident->save($con);
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

        $this->modifiedColumns[ResourceTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ResourceTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ResourceTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(ResourceTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(ResourceTableMap::COL_IMAGE)) {
            $modifiedColumns[':p' . $index++]  = 'image';
        }
        if ($this->isColumnModified(ResourceTableMap::COL_TEL)) {
            $modifiedColumns[':p' . $index++]  = 'tel';
        }
        if ($this->isColumnModified(ResourceTableMap::COL_SMS)) {
            $modifiedColumns[':p' . $index++]  = 'Sms';
        }
        if ($this->isColumnModified(ResourceTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'created_at';
        }
        if ($this->isColumnModified(ResourceTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'updated_at';
        }

        $sql = sprintf(
            'INSERT INTO resource (%s) VALUES (%s)',
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
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'image':
                        $stmt->bindValue($identifier, $this->image, PDO::PARAM_STR);
                        break;
                    case 'tel':
                        $stmt->bindValue($identifier, $this->tel, PDO::PARAM_STR);
                        break;
                    case 'Sms':
                        $stmt->bindValue($identifier, (int) $this->sms, PDO::PARAM_INT);
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
        $pos = ResourceTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getName();
                break;
            case 2:
                return $this->getImage();
                break;
            case 3:
                return $this->getTel();
                break;
            case 4:
                return $this->getSms();
                break;
            case 5:
                return $this->getCreatedAt();
                break;
            case 6:
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

        if (isset($alreadyDumpedObjects['Resource'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Resource'][$this->hashCode()] = true;
        $keys = ResourceTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getImage(),
            $keys[3] => $this->getTel(),
            $keys[4] => $this->getSms(),
            $keys[5] => $this->getCreatedAt(),
            $keys[6] => $this->getUpdatedAt(),
        );
        if ($result[$keys[5]] instanceof \DateTime) {
            $result[$keys[5]] = $result[$keys[5]]->format('c');
        }

        if ($result[$keys[6]] instanceof \DateTime) {
            $result[$keys[6]] = $result[$keys[6]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
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
     * @return $this|\Resource
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ResourceTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Resource
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setImage($value);
                break;
            case 3:
                $this->setTel($value);
                break;
            case 4:
                $this->setSms($value);
                break;
            case 5:
                $this->setCreatedAt($value);
                break;
            case 6:
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
        $keys = ResourceTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setImage($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setTel($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setSms($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setCreatedAt($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setUpdatedAt($arr[$keys[6]]);
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
     * @return $this|\Resource The current object, for fluid interface
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
        $criteria = new Criteria(ResourceTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ResourceTableMap::COL_ID)) {
            $criteria->add(ResourceTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(ResourceTableMap::COL_NAME)) {
            $criteria->add(ResourceTableMap::COL_NAME, $this->name);
        }
        if ($this->isColumnModified(ResourceTableMap::COL_IMAGE)) {
            $criteria->add(ResourceTableMap::COL_IMAGE, $this->image);
        }
        if ($this->isColumnModified(ResourceTableMap::COL_TEL)) {
            $criteria->add(ResourceTableMap::COL_TEL, $this->tel);
        }
        if ($this->isColumnModified(ResourceTableMap::COL_SMS)) {
            $criteria->add(ResourceTableMap::COL_SMS, $this->sms);
        }
        if ($this->isColumnModified(ResourceTableMap::COL_CREATED_AT)) {
            $criteria->add(ResourceTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(ResourceTableMap::COL_UPDATED_AT)) {
            $criteria->add(ResourceTableMap::COL_UPDATED_AT, $this->updated_at);
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
        $criteria = ChildResourceQuery::create();
        $criteria->add(ResourceTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Resource (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setImage($this->getImage());
        $copyObj->setTel($this->getTel());
        $copyObj->setSms($this->getSms());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

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
     * @return \Resource Clone of current object.
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
        if ('IncidentResource' == $relationName) {
            return $this->initIncidentResources();
        }
        if ('IncidentResourceRecord' == $relationName) {
            return $this->initIncidentResourceRecords();
        }
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
     * If this ChildResource is new, it will return
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
                    ->filterByResource($this)
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
     * @return $this|ChildResource The current object (for fluent API support)
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
            $incidentResourceRemoved->setResource(null);
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
                ->filterByResource($this)
                ->count($con);
        }

        return count($this->collIncidentResources);
    }

    /**
     * Method called to associate a ChildIncidentResource object to this object
     * through the ChildIncidentResource foreign key attribute.
     *
     * @param  ChildIncidentResource $l ChildIncidentResource
     * @return $this|\Resource The current object (for fluent API support)
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
        $incidentResource->setResource($this);
    }

    /**
     * @param  ChildIncidentResource $incidentResource The ChildIncidentResource object to remove.
     * @return $this|ChildResource The current object (for fluent API support)
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
            $incidentResource->setResource(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Resource is new, it will return
     * an empty collection; or if this Resource has previously
     * been saved, it will retrieve related IncidentResources from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Resource.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildIncidentResource[] List of ChildIncidentResource objects
     */
    public function getIncidentResourcesJoinIncident(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildIncidentResourceQuery::create(null, $criteria);
        $query->joinWith('Incident', $joinBehavior);

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
     * If this ChildResource is new, it will return
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
                    ->filterByResource($this)
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
     * @return $this|ChildResource The current object (for fluent API support)
     */
    public function setIncidentResourceRecords(Collection $incidentResourceRecords, ConnectionInterface $con = null)
    {
        /** @var ChildIncidentResourceRecord[] $incidentResourceRecordsToDelete */
        $incidentResourceRecordsToDelete = $this->getIncidentResourceRecords(new Criteria(), $con)->diff($incidentResourceRecords);


        $this->incidentResourceRecordsScheduledForDeletion = $incidentResourceRecordsToDelete;

        foreach ($incidentResourceRecordsToDelete as $incidentResourceRecordRemoved) {
            $incidentResourceRecordRemoved->setResource(null);
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
                ->filterByResource($this)
                ->count($con);
        }

        return count($this->collIncidentResourceRecords);
    }

    /**
     * Method called to associate a ChildIncidentResourceRecord object to this object
     * through the ChildIncidentResourceRecord foreign key attribute.
     *
     * @param  ChildIncidentResourceRecord $l ChildIncidentResourceRecord
     * @return $this|\Resource The current object (for fluent API support)
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
        $incidentResourceRecord->setResource($this);
    }

    /**
     * @param  ChildIncidentResourceRecord $incidentResourceRecord The ChildIncidentResourceRecord object to remove.
     * @return $this|ChildResource The current object (for fluent API support)
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
            $incidentResourceRecord->setResource(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Resource is new, it will return
     * an empty collection; or if this Resource has previously
     * been saved, it will retrieve related IncidentResourceRecords from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Resource.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildIncidentResourceRecord[] List of ChildIncidentResourceRecord objects
     */
    public function getIncidentResourceRecordsJoinIncident(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildIncidentResourceRecordQuery::create(null, $criteria);
        $query->joinWith('Incident', $joinBehavior);

        return $this->getIncidentResourceRecords($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Resource is new, it will return
     * an empty collection; or if this Resource has previously
     * been saved, it will retrieve related IncidentResourceRecords from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Resource.
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
     * Clears out the collIncidents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addIncidents()
     */
    public function clearIncidents()
    {
        $this->collIncidents = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collIncidents crossRef collection.
     *
     * By default this just sets the collIncidents collection to an empty collection (like clearIncidents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initIncidents()
    {
        $collectionClassName = IncidentResourceTableMap::getTableMap()->getCollectionClassName();

        $this->collIncidents = new $collectionClassName;
        $this->collIncidentsPartial = true;
        $this->collIncidents->setModel('\Incident');
    }

    /**
     * Checks if the collIncidents collection is loaded.
     *
     * @return bool
     */
    public function isIncidentsLoaded()
    {
        return null !== $this->collIncidents;
    }

    /**
     * Gets a collection of ChildIncident objects related by a many-to-many relationship
     * to the current object by way of the incident_resource cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildResource is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildIncident[] List of ChildIncident objects
     */
    public function getIncidents(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentsPartial && !$this->isNew();
        if (null === $this->collIncidents || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collIncidents) {
                    $this->initIncidents();
                }
            } else {

                $query = ChildIncidentQuery::create(null, $criteria)
                    ->filterByResource($this);
                $collIncidents = $query->find($con);
                if (null !== $criteria) {
                    return $collIncidents;
                }

                if ($partial && $this->collIncidents) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collIncidents as $obj) {
                        if (!$collIncidents->contains($obj)) {
                            $collIncidents[] = $obj;
                        }
                    }
                }

                $this->collIncidents = $collIncidents;
                $this->collIncidentsPartial = false;
            }
        }

        return $this->collIncidents;
    }

    /**
     * Sets a collection of Incident objects related by a many-to-many relationship
     * to the current object by way of the incident_resource cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $incidents A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildResource The current object (for fluent API support)
     */
    public function setIncidents(Collection $incidents, ConnectionInterface $con = null)
    {
        $this->clearIncidents();
        $currentIncidents = $this->getIncidents();

        $incidentsScheduledForDeletion = $currentIncidents->diff($incidents);

        foreach ($incidentsScheduledForDeletion as $toDelete) {
            $this->removeIncident($toDelete);
        }

        foreach ($incidents as $incident) {
            if (!$currentIncidents->contains($incident)) {
                $this->doAddIncident($incident);
            }
        }

        $this->collIncidentsPartial = false;
        $this->collIncidents = $incidents;

        return $this;
    }

    /**
     * Gets the number of Incident objects related by a many-to-many relationship
     * to the current object by way of the incident_resource cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Incident objects
     */
    public function countIncidents(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collIncidentsPartial && !$this->isNew();
        if (null === $this->collIncidents || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collIncidents) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getIncidents());
                }

                $query = ChildIncidentQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByResource($this)
                    ->count($con);
            }
        } else {
            return count($this->collIncidents);
        }
    }

    /**
     * Associate a ChildIncident to this object
     * through the incident_resource cross reference table.
     *
     * @param ChildIncident $incident
     * @return ChildResource The current object (for fluent API support)
     */
    public function addIncident(ChildIncident $incident)
    {
        if ($this->collIncidents === null) {
            $this->initIncidents();
        }

        if (!$this->getIncidents()->contains($incident)) {
            // only add it if the **same** object is not already associated
            $this->collIncidents->push($incident);
            $this->doAddIncident($incident);
        }

        return $this;
    }

    /**
     *
     * @param ChildIncident $incident
     */
    protected function doAddIncident(ChildIncident $incident)
    {
        $incidentResource = new ChildIncidentResource();

        $incidentResource->setIncident($incident);

        $incidentResource->setResource($this);

        $this->addIncidentResource($incidentResource);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$incident->isResourcesLoaded()) {
            $incident->initResources();
            $incident->getResources()->push($this);
        } elseif (!$incident->getResources()->contains($this)) {
            $incident->getResources()->push($this);
        }

    }

    /**
     * Remove incident of this object
     * through the incident_resource cross reference table.
     *
     * @param ChildIncident $incident
     * @return ChildResource The current object (for fluent API support)
     */
    public function removeIncident(ChildIncident $incident)
    {
        if ($this->getIncidents()->contains($incident)) { $incidentResource = new ChildIncidentResource();

            $incidentResource->setIncident($incident);
            if ($incident->isResourcesLoaded()) {
                //remove the back reference if available
                $incident->getResources()->removeObject($this);
            }

            $incidentResource->setResource($this);
            $this->removeIncidentResource(clone $incidentResource);
            $incidentResource->clear();

            $this->collIncidents->remove($this->collIncidents->search($incident));

            if (null === $this->incidentsScheduledForDeletion) {
                $this->incidentsScheduledForDeletion = clone $this->collIncidents;
                $this->incidentsScheduledForDeletion->clear();
            }

            $this->incidentsScheduledForDeletion->push($incident);
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
        $this->name = null;
        $this->image = null;
        $this->tel = null;
        $this->sms = null;
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
            if ($this->collIncidents) {
                foreach ($this->collIncidents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collIncidentResources = null;
        $this->collIncidentResourceRecords = null;
        $this->collIncidents = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ResourceTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildResource The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[ResourceTableMap::COL_UPDATED_AT] = true;

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
