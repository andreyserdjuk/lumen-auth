<?php

namespace LumenAuth\Core;

use Doctrine\Persistence\ObjectManager;
use LumenAuth\Documents\Account;
use LumenAuth\Documents\RegistrationPending;
use LumenAuth\Documents\RegistrationRequest;

class RegistrationProcessor
{
    /**
     * @var ObjectManager
     */
    protected $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param string $email     valid email
     * @param string $password  encrypted password
     * @throws \Exception
     */
    public function accept(string $email, string $password): void
    {
        // todo avoid multiple queries
        /** @var RegistrationRequest $found */
        $found = $this->om->getRepository(RegistrationRequest::class)->findOneBy(['email' => $email]);
        if (!$found){
            $found = $this->om->getRepository(RegistrationPending::class)->findOneBy(['email' => $email]);
        }
        if (!$found){
            $found = $this->om->getRepository(Account::class)->findOneBy(['email' => $email]);
        }

        if (!$found) {
            $password = password_hash($password, PASSWORD_ARGON2ID);
            $registration = new RegistrationRequest();
            $registration
                ->setEmail($email)
                ->setPassword($password)
                ->setCreatedAt(new \DateTime());
            $this->om->persist($registration);
            $this->om->flush();
        } else {
            throw new \InvalidArgumentException('Email is already used.');
        }
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function activate(string $id): void
    {
        /** @var RegistrationPending $pending */
        $pending = $this->om->getRepository(RegistrationPending::class)->find($id);

        if ($pending) {
            $account = new Account();
            $account
                ->setEmail($pending->getEmail())
                ->setPassword($pending->getPassword())
                ->setCreatedAt(new \DateTime());
            $this->om->persist($account);
            $this->om->remove($pending);
            $this->om->flush();
        } else {
            throw new \InvalidArgumentException('Cannot activate user.');
        }
    }
}
