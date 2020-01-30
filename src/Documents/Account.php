<?php

namespace LumenAuth\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document()
 */
class Account
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
     * @return Account
     */
    public function setEmail(?string $email): Account
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
     * @return Account
     */
    public function setPassword(?string $password): Account
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
     * @return Account
     */
    public function setCreatedAt(?\DateTime $createdAt): Account
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
