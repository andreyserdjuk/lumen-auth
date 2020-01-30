<?php

namespace App\Console\Commands;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\Persistence\ObjectManager;
use Illuminate\Console\Command;
use LumenAuth\Documents\Account;
use LumenAuth\Documents\ActivationMail;
use LumenAuth\Documents\RegistrationPending;
use LumenAuth\Documents\RegistrationRequest;

class PurgeRegistrationRequests extends Command
{
    protected $signature = 'auth:purge';

    protected $description = 'Purge all of registration pending and requests.';

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
        $collections = [
            RegistrationRequest::class,
            RegistrationPending::class,
            Account::class,
            ActivationMail::class,
        ];

        foreach ($collections as $collection) {
            try {
                $collection = $this->om->getDocumentCollection($collection);
                $collection->drop();
                $this->info(sprintf('Collection "%s" was purged.', $collection));
            } catch (MongoDBException $e) {
                $this->error($e->getMessage());
            }
        }
    }
}
