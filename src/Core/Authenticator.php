<?php

namespace LumenAuth\Core;

use Doctrine\Persistence\ObjectManager;
use LumenAuth\Documents\Account;

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

    public function authenticate(string $email, string $password, int $ttl): array
    {
        /** @var Account $account */
        $account = $this->om
            ->getRepository(Account::class)
            ->findOneBy([
                'email' => $email,
            ]);

        if (!$account || !password_verify($password, $account->getPassword())) {
            throw new \InvalidArgumentException('User not found.');
        }

        $expireTime = time() + $ttl;
        $signature = password_hash($email . $account->getPassword() . $expireTime, PASSWORD_ARGON2ID);

        return [
            'signature' => $signature,
            'expires'   => $expireTime,
            'email'     => $email,
        ];
    }

    public function verify(string $token): bool
    {
        $parts = json_decode($token, true);
        $signature = $parts['signature'] ?? null;
        $expireTime = isset($parts['expires']) ? intval($parts['expires']) : null;
        $email = $parts['email'] ?? null;
        $time = time();
        if ($time > $expireTime) {
            throw new \InvalidArgumentException('Token was expired.');
        }

        if ($email && $expireTime && $signature) {
            /** @var Account $account */
            $account = $this->om
                ->getRepository(Account::class)
                ->findOneBy([
                    'email' => $email,
                ]);

            if ($account) {
                $password = $account->getPassword();
                $signatureSrc = $email . $password . $expireTime;

                return password_verify($signatureSrc, $signature);
            }
        } else {
            throw new \InvalidArgumentException('Malformed auth token.');
        }

        return false;
    }
}
