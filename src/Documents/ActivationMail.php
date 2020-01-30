<?php

namespace LumenAuth\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document()
 */
class ActivationMail
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
     */
    protected $subject;

    /**
     * @var string|null
     *
     * @ODM\Field(type="string")
     */
    protected $from;

    /**
     * @var string|null
     *
     * @ODM\Field(type="string")
     */
    protected $to;

    /**
     * @var string|null
     *
     * @ODM\Field(type="string")
     */
    protected $body;

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
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return ActivationMail
     */
    public function setSubject(?string $subject): ActivationMail
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * @param string|null $from
     * @return ActivationMail
     */
    public function setFrom(?string $from): ActivationMail
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTo(): ?string
    {
        return $this->to;
    }

    /**
     * @param string|null $to
     * @return ActivationMail
     */
    public function setTo(?string $to): ActivationMail
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     * @return ActivationMail
     */
    public function setBody(?string $body): ActivationMail
    {
        $this->body = $body;
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
     * @return ActivationMail
     */
    public function setCreatedAt(?\DateTime $createdAt): ActivationMail
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
