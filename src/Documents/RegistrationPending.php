<?php

namespace LumenAuth\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document()
 * @ODM\Index(keys={"createdAt"=true}, options={"expireAfterSeconds"=3600})
 */
// todo setup delete after expiration time
class RegistrationPending extends Registration
{
}
