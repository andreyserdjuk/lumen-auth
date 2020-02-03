<?php

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use LumenAuth\Core\Authenticator;
use LumenAuth\Core\Base64;
use LumenAuth\Documents\Account;
use PHPUnit\Framework\TestCase;

class AuthenticatorTest extends TestCase
{
    /**
     * @dataProvider authDataProvider
     *
     * @param $password
     * @param $passwordHash
     * @param $email
     * @param $key
     * @param $ttl
     * @param $isNotExpired
     */
    public function testAuth(
        $password,
        $passwordHash,
        $email,
        $key,
        $ttl,
        $isNotExpired
    )
    {
        $authenticator = $this->makeAuthenticator($passwordHash);
        $time = time();
        $token = $authenticator->authenticate($email, $password, $key, $ttl);
        $parts = explode('~', $token);
        [$payload64, $signature64] = $parts;
        $signature = Base64::decode($signature64);
        $signatureControl = hash_hmac(
            'sha256',
            $payload64,
            $key,
            true
        );

        $this->assertEquals($signatureControl, $signature);
    }

    /**
     * @dataProvider authDataProvider
     *
     * @param $password
     * @param $passwordHash
     * @param $email
     * @param $key
     * @param $ttl
     * @param $isNotExpired
     */
    public function testVerify(
        $password,
        $passwordHash,
        $email,
        $key,
        $ttl,
        $isNotExpired
    )
    {
        if (!$isNotExpired) {
            $this->expectException(UnexpectedValueException::class);
            $this->expectExceptionMessage('Token was expired.');
        } else {
            $this->assertTrue(true);
        }

        $expireTime = time() + $ttl;
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
        $token = $payload . '~' . $signature;
        $authenticator = $this->makeAuthenticator($passwordHash);
        $authenticator->getPayloadFromToken($token, $key);
    }

    /**
     * @dataProvider authDataProvider
     *
     * @param $password
     * @param $passwordHash
     * @param $email
     * @param $key
     * @param $ttl
     * @param $isNotExpired
     */
    public function testEnd2End(
        $password,
        $passwordHash,
        $email,
        $key,
        $ttl,
        $isNotExpired
    )
    {
        if (!$isNotExpired) {
            $this->expectException(UnexpectedValueException::class);
            $this->expectExceptionMessage('Token was expired.');
        } else {
            $this->assertTrue(true);
        }

        $authenticator = $this->makeAuthenticator($passwordHash);
        $token = $authenticator->authenticate($email, $password, $key, $ttl);
        $authenticator->getPayloadFromToken($token, $key);
    }

    public function authDataProvider()
    {
        return [
            [
                '123',                                                      // password
                password_hash('123', PASSWORD_ARGON2ID),     // password hash
                'x123@x.com',                                               // email
                'secret',                                                   // key for hash_hmac
                10,                                                         // ttl
                true,                                                       // not expired
            ],
            [
                '123password3112039',
                password_hash('123password3112039', PASSWORD_ARGON2ID),
                'mail@localhost',
                'secret',                                                   // key for hash_hmac
                3600,
                true,                                                       // not expired
            ],
            [
                '123password3112039',
                password_hash('123password3112039', PASSWORD_ARGON2ID),
                'mail@localhost',
                'secret',                                                   // key for hash_hmac
                -1,
                false,                                                       // expired
            ],
        ];
    }

    private function makeAuthenticator($passwordHash)
    {
        $om = $this->createMock(ObjectManager::class);
        $repository = $this->createMock(ObjectRepository::class);
        $account = $this->createMock(Account::class);

        $om->method('getRepository')
            ->willReturn($repository);

        $repository->method('findOneBy')
            ->willReturn($account);

        $account->method('getPassword')
            ->willReturn($passwordHash);

        return new Authenticator($om);
    }
}
