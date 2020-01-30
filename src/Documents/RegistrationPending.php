<?php

namespace LumenAuth\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document()
 */
class RegistrationPending
{
    /**
     * @var string|null
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ODM\Field(type="string")
     * @ODM\UniqueIndex(order="asc")
     */
    protected $email;

    /**
     * @var string|null
     *
     * @ODM\Field(type="string")
     */
    protected $password;

    /**
     * @var \DateTime|null
     *
     * @ODM\Field(type="date")
     * @ODM\Index(options={"expireAfterSeconds"=3600})
     */
    protected $createdAt;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return RegistrationPending
     */
    public function setEmail(?string $email): RegistrationPending
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return RegistrationPending
     */
    public function setPassword(?string $password): RegistrationPending
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|null $createdAt
     * @return RegistrationPending
     */
    public function setCreatedAt(?\DateTime $createdAt): RegistrationPending
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
