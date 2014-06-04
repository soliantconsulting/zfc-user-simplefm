<?php

namespace Soliant\ZfcUserSimpleFm\Entity;

use Soliant\SimpleFM\ZF2\Entity\AbstractEntity;
use ZfcUser\Entity\UserInterface;
use Zend\Stdlib\ArraySerializableInterface;

class User extends AbstractEntity implements UserInterface, ArraySerializableInterface
{
    public function __construct($data = null)
    {
        if ($data) {
            $this->exchangeArray($data);
        }
    }

    /**
     * For SimpleFM; id
     */
    protected $recid;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var int
     */
    protected $state;

    public function getArrayCopy()
    {
        return array(
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'displayName' => $this->getDisplayName(),
            'password' => $this->getPassword(),
            'state' => $this->getState(),
        );
    }

    public function exchangeArray(array $data)
    {
        foreach ($data as $field => $value) {
            $field = strtolower($field);
            switch ($field) {
                case 'id':
                    $this->setId($value);
                    break;

                case 'recid':
                    $this->setRecId($value);
                    break;

                case 'username':
                    $this->setUsername($value);
                    break;

                case 'email':
                    $this->setEmail($value);
                    break;

                case 'displayName':
                    $this->setDisplayName($value);
                    break;

                case 'password':
                    $this->setPassword($value);
                    break;

                case 'state':
                    $this->setState($value);
                    break;
            }
        }

        return $this;
    }

    public function getFieldMapWriteable()
    {
        return [
            'username' => 'username',
            'email' => 'email',
            'displayName' => 'displayName',
            'password' => 'password',
            'state' => 'state',
       ];
    }

    public function getFieldMapReadOnly()
    {
        return [
            'id' => 'id',
        ];
    }

    public function getDefaultWriteLayoutName()
    {
        throw new \Exception('This function belongs on the mapper and should not be called '
        . 'ever');
    }

    public function getDefaultControllerRouteSegment()
    {
        throw new \Exception('This function belongs on the mapper and should not be called '
        . 'ever');
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param  int $id
     * @return UserInterface
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param  string $username
     * @return UserInterface
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param  string $email
     * @return UserInterface
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param  string $displayName
     * @return UserInterface
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param  string $password
     * @return UserInterface
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param  int $state
     * @return UserInterface
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }
}
