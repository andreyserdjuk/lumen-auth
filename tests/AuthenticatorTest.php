<?php

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use LumenAuth\Core\Authenticator;
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
     * @param $ttl
     */
    public function testAuth(
        $password,
        $passwordHash,
        $email,
        $ttl,
        $isExpired
    )
    {
        $authenticator = $this->makeAuthenticator($passwordHash);
        $time = time();
        $token = $authenticator->authenticate($email, $password, $ttl);
        $signature = $token['signature'];
        $signatureSrc = $email . $passwordHash . ($time + $ttl);

        $this->assertTrue(password_verify($signatureSrc, $signature));
    }

    /**
     * @dataProvider authDataProvider
     *
     * @param $password
     * @param $passwordHash
     * @param $email
     * @param $ttl
     * @param $isExpired
     */
    public function testVerify(
        $password,
        $passwordHash,
        $email,
        $ttl,
        $isExpired
    )
    {
        if (!$isExpired) {
            $this->expectException(\InvalidArgumentException::class);
        }

        $authenticator = $this->makeAuthenticator($passwordHash);
        $expireTime = time() + $ttl;
        $signature = password_hash($email . $passwordHash . $expireTime, PASSWORD_ARGON2ID);

        $token = json_encode([
            'signature' => $signature,
            'expires'   => $expireTime,
            'email'     => $email,
        ]);

        $this->assertEquals($isExpired, $authenticator->verify($token));
    }

    /**
     * @dataProvider authDataProvider
     *
     * @param $password
     * @param $passwordHash
     * @param $email
     * @param $ttl
     * @param $isExpired
     */
    public function testEnd2End(
        $password,
        $passwordHash,
        $email,
        $ttl,
        $isExpired
    )
    {
        if (!$isExpired) {
            $this->expectException(\InvalidArgumentException::class);
        }

        $authenticator = $this->makeAuthenticator($passwordHash);
        $token = $authenticator->authenticate($email, $password, $ttl);
        $this->assertEquals(
            $isExpired,
            $authenticator->verify(json_encode($token)),
            'Cannot verify own token.'
        );
    }

    public function authDataProvider()
    {
        return [
            [
                '123',                                                      // password
                password_hash('123', PASSWORD_ARGON2ID),     // password hash
                'x123@x.com',                                               // email
                10,                                                         // ttl
                true,                                                       // not expired
            ],
            [
                '123password3112039',
                password_hash('123password3112039', PASSWORD_ARGON2ID),
                'mail@localhost',
                3600,
                true,                                                       // not expired
            ],
            [
                '123password3112039',
                password_hash('123password3112039', PASSWORD_ARGON2ID),
                'mail@localhost',
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
