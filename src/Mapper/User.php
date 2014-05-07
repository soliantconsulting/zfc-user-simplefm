<?php

namespace Soliant\ZfcUserSimpleFM\Mapper;

use Soliant\SimpleFM\ZF2\Gateway\AbstractGateway;
use Soliant\ZfcUserSimpleFM\Entity\User as UserEntity;
use Traversable;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\Stdlib\Hydrator\HydratorInterface;

#use ZfcBase\Mapper\AbstractDbMapper;
use ZfcUser\Mapper\UserInterface;
use Soliant\SimpleFM\Adapter as DbAdapter;

class User extends AbstractGateway implements UserInterface, EventManagerAwareInterface
{
    protected $tableName  = 'user';
    protected $dbAdapter;
    protected $entity;
    protected $hydrator;

    public function __construct()
    {
        // Clear abstract gateway and use in init
    }

    public function init() {
        $this->setEntityName(get_class($this->entity));
        $this->setSimpleFMAdapter($this->getDbAdapter());
        $this->setEntityLayout($this->getTableName());
    }

    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;

        return $this;
    }

    public function getHydrator()
    {
        return $this->hydrator;
    }

    public function setEntityPrototype(UserEntity $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    public function setDbAdapter(DbAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;

        return $this;
    }

    public function findByEmail($email)
    {
        $entity = $this->findOneBy(array(
            'email' => $email
        ));

        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));

        return $entity;
    }

    public function findByUsername($username)
    {
        $select = $this->getSelect()
                       ->where(array('username' => $username));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findById($id)
    {
        $select = $this->getSelect()
                       ->where(array('user_id' => $id));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function insert($entity, $tableName = null, HydratorInterface $hydrator = null)
    {
        $dbAdapter = $this->getDbAdapter();
        $data = $this->getHydrator()->extract($entity);

        $dbAdapter->setLayoutname($this->getTableName());
        $dbAdapter->setCommandarray(
            array(
                '-max'     => 1,
                '-skip'    => 5,
                '-findany' => NULL
            )
        );

        print_r(get_class_methods($dbAdapter));die();

        $result = $dbAdapter->execute();


        $result = parent::insert($entity, $tableName, $hydrator);
        $entity->setId($result->getGeneratedValue());
        return $result;
    }

    public function update($entity, $where = null, $tableName = null, HydratorInterface $hydrator = null)
    {
        if (!$where) {
            $where = array('user_id' => $entity->getId());
        }

        return parent::update($entity, $where, $tableName, $hydrator);
    }

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * Set the event manager instance used by this context
     *
     * @param  EventManagerInterface $events
     * @return mixed
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $identifiers = array(__CLASS__, get_called_class());
        if (isset($this->eventIdentifier)) {
            if ((is_string($this->eventIdentifier))
                || (is_array($this->eventIdentifier))
                || ($this->eventIdentifier instanceof Traversable)
            ) {
                $identifiers = array_unique(array_merge($identifiers, (array) $this->eventIdentifier));
            } elseif (is_object($this->eventIdentifier)) {
                $identifiers[] = $this->eventIdentifier;
            }
            // silently ignore invalid eventIdentifier types
        }
        $events->setIdentifiers($identifiers);
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
