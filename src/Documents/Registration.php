<?php


namespace LumenAuth\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\MappedSuperclass()
 */
abstract class Registration
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
     * @return Registration
     */
    public function setEmail(?string $email): Registration
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
     * @return Registration
     */
    public function setPassword(?string $password): Registration
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
     * @return Registration
     */
    public function setCreatedAt(?\DateTime $createdAt): Registration
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
