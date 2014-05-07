<?php

namespace Soliant\ZfcUserSimpleFM\Mapper;

#use ZfcBase\Mapper\AbstractDbMapper;
use ZfcUser\Entity\UserInterface as UserEntityInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use ZfcUser\Mapper\UserInterface;
use Soliant\SimpleFM\Adapter as DbAdapter;
use Zend\Stdlib\Hydrator\AbstractHydrator;

class User implements UserInterface
{
    protected $tableName  = 'user';
    protected $dbAdapter;
    protected $entity;
    protected $hydrator;

    public function setHydrator(AbstractHydrator $hydrator)
    {
        $this->hydrator = $hydrator;

        return $this;
    }

    public function setEntityPrototype(UserEntityInterface $entity)
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
        $dbAdapter = $this->getDbAdapter();

        $dbAdapter->setLayoutname($this->getTableName());
        $dbAdapter->setCommandarray(
            array(
                '-max'     => 1,
                '-skip'    => 5,
                '-findall' => NULL
            )
        );

        $result = $dbAdapter->execute();

        print_r($result);die();


        $select = $this->getSelect()
                       ->where(array('email' => $email));

        $entity = $this->select($select)->current();
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
}
