<?php

namespace LumenAuth\Core;

use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;
use LumenAuth\Documents\Account;
use UnexpectedValueException;

class Authenticator
{
    /**
     * @var ObjectManager
     */
    protected $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function authenticate(string $email, string $password, string $key, int $ttl): string
    {
        $expireTime = time() + $ttl;

        /** @var Account $account */
        $account = $this->om
            ->getRepository(Account::class)
            ->findOneBy([
                'email' => $email,
            ]);

        if (!$account || !password_verify($password, $account->getPassword())) {
            throw new InvalidArgumentException('User not found.');
        }

        $payload = Base64::encode(json_encode([
            'email' => $email,
            'exp'   => $expireTime,
        ]));
        $signature = Base64::encode(hash_hmac(
            'sha256',
            $payload,
            $key,
            true
        ));

        return $payload . '~' . $signature;
    }

    public function getPayloadFromToken(string $token, string $key): array
    {
        $time = time();
        $parts = explode('~', $token);
        if (count($parts) !== 2) {
            throw new UnexpectedValueException('Malformed payload.');
        }
        [$payload64, $signature64] = $parts;
        $signature = Base64::decode($signature64);
        $signatureControl = hash_hmac(
            'sha256',
            $payload64,
            $key,
            true
        );

        if (!hash_equals($signatureControl, $signature)) {
            throw new UnexpectedValueException('Signature verification failed');
        }

        $payload = json_decode(Base64::decode($payload64), true);
        if (!$payload || !isset($payload['exp'])) {
            throw new UnexpectedValueException('Malformed payload.');
        }

        if ($payload['exp'] < $time) {
            throw new UnexpectedValueException('Token was expired.');
        }

        return $payload;
    }
}
