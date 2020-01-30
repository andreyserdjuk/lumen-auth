<?php

namespace App\Console\Commands;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectManager;
use Illuminate\Console\Command;
use Illuminate\Mail\Mailer;
use LumenAuth\Documents\ActivationMail;

class SendMailSpool extends Command
{
    protected $signature = 'mail-queue:send';

    protected $description = 'Prepare activation emails for all of registration requests.';

    /**
     * @var DocumentManager
     */
    protected $om;

    /**
     * @var Mailer
     */
    protected $mailer;

    public function __construct(ObjectManager $om, Mailer $mailer)
    {
        parent::__construct();
        $this->om = $om;
        $this->mailer = $mailer;
    }

    public function handle()
    {
        $swiftMailer = $this->mailer->getSwiftMailer();

        /** @var ActivationMail[] $mails */
        $mails = $this->om
            ->createQueryBuilder(ActivationMail::class)
            ->limit(1000)
            ->getQuery()
            ->execute();

        foreach ($mails as $mail) {
            $message = (new \Swift_Message($mail->getSubject()))
                ->setFrom($mail->getFrom())
                ->setTo($mail->getTo())
                ->setBody(
                    $mail->getBody(),
                    'text/plain'
                );

            try {
                if ($swiftMailer->send($message)) {
                    $this->om->remove($mail);
                    $this->om->flush();
                    $this->info(sprintf('Sent to recipient "%s" from spool.', $mail->getTo()));
                } else {
                    $this->warn(sprintf(
                        'Could not send Mail - id:"%s", mail:"%s"',
                        $mail->getId(),
                        $mail->getTo())
                    );
                }
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
            }
        }
    }
}
