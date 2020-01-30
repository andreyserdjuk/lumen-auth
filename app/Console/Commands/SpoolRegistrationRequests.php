<?php

namespace App\Console\Commands;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectManager;
use Illuminate\Console\Command;
use LumenAuth\Documents\ActivationMail;
use LumenAuth\Documents\RegistrationPending;
use LumenAuth\Documents\RegistrationRequest;

class SpoolRegistrationRequests extends Command
{
    protected $signature = 'mail-queue:spool';

    protected $description = 'Prepare activation emails for all of registration requests.';

    /**
     * @var DocumentManager
     */
    protected $om;

    public function __construct(ObjectManager $om)
    {
        parent::__construct();
        $this->om = $om;
    }

    public function handle()
    {
        $fromAddress = config('mail.from.address');
        $activationUrl = config('app.url') . '/activate/';

        do {
            /** @var RegistrationRequest $registration */
            $registration = $this->om->createQueryBuilder(RegistrationRequest::class)
                ->findAndRemove()
                ->getQuery()
                ->execute();

            if ($registration) {
                $pending = new RegistrationPending();
                $pending
                    ->setEmail($registration->getEmail())
                    ->setPassword($registration->getPassword())
                    ->setCreatedAt(new \DateTime());
                $this->om->persist($pending);
                $this->om->flush();
                $pendingId = $pending->getId();

                $mail = new ActivationMail();
                $mail
                    ->setSubject('Activate your profile')
                    ->setFrom($fromAddress)
                    ->setTo($registration->getEmail())
                    ->setBody(sprintf(
                        'Please activate your profile by url "%s%s" or code "%s"',
                        $activationUrl,
                        $pendingId,
                        $pendingId
                    ))
                    ->setCreatedAt(new \DateTime());
                $this->om->persist($mail);
                $this->om->flush();
            }
        } while ($registration);
    }
}
